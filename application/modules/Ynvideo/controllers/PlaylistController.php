<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_PlaylistController extends Core_Controller_Action_Standard
{

	protected $_roles;

	public function init()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> menus_navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideo_main', array(), 'ynvideo_main_playlist');
		$this -> _roles = array(
			'owner',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);

		if (0 !== ($playlist_id = (int)$this -> _getParam('playlist_id')) && null !== ($playlist = Engine_Api::_() -> getItem('ynvideo_playlist', $playlist_id)) && $playlist instanceof Ynvideo_Model_Playlist)
		{
			Engine_Api::_() -> core() -> setSubject($playlist);
		}
	}

	public function indexAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function quickCreateAction()
	{
		if (0 !== ($video_id = (int)$this -> getRequest() -> getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynvideo_video', $video_id)) && $video instanceof Ynvideo_Model_Video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject('video') -> isValid())
		{
			return;
		}
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireSubject('ynvideo_playlist') -> isValid())
		{
			return;
		}

		$this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('ynvideo_playlist');
		if (!$this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'edit') -> isValid())
		{
			return;
		}

		$this -> view -> form = $form = new Ynvideo_Form_Playlist_Edit( array('playlist' => $playlist));

		// Check post/form
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();

			$playlist -> setFromArray($values);
			$playlist -> modified_date = date('Y-m-d H:i:s');
			$playlist -> save();

			if (!empty($values['photo']))
			{
				$playlist -> setPhoto($form -> photo);
			}

			// Auth
			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $this -> _roles);
			$commentMax = array_search($values['auth_comment'], $this -> _roles);
			$auth = Engine_Api::_() -> authorization() -> context;
			foreach ($this->_roles as $i => $role)
			{
				$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($playlist) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array(), 'video_playlist', true);
	}

	public function deleteAction()
	{
		$playlist = Engine_Api::_() -> getItem('ynvideo_playlist', $this -> getRequest() -> getParam('playlist_id'));

		if (!$this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynvideo_Form_Playlist_Delete();

		if (!$playlist)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("The playlist doesn't exist or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
			return;
		}

		$db = $playlist -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$playlist -> delete();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The video playlist has been deleted.');

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _($this -> view -> message)),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function createAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynvideo_playlist', null, 'create') -> isValid())
		{
			return;
		}
		$this -> view -> form = $form = new Ynvideo_Form_Playlist_Create();
		$this -> view -> playlist_id = $this -> _getParam('playlist_id', '0');

		// Check method/data
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$viewer = $this -> view -> viewer;

		// Process saving the new playlist
		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynvideo');
		$db = $playlistTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$playlist = $playlistTable -> createRow();
			$playlist -> setFromArray($values);
			$playlist -> save();

			if (!empty($values['photo']))
			{
				try
				{
					$playlist -> setPhoto($form -> photo);
				}
				catch (Engine_Image_Adapter_Exception $e)
				{
					Zend_Registry::get('Zend_Log') -> log($e -> __toString(), Zend_Log::WARN);
				}
			}

			// Auth
			$auth = Engine_Api::_() -> authorization() -> context;

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $this -> _roles);
			$commentMax = array_search($values['auth_comment'], $this -> _roles);

			foreach ($this->_roles as $i => $role)
			{
				$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollback();
			throw $e;
		}

		// add activity feed for creating a new playlist
		$db -> beginTransaction();
		try
		{
			$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $playlist, 'ynvideo_playlist_new');
			if ($action != null)
			{
				Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $playlist);
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($playlist) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array(), 'video_playlist', true);
	}

	public function addAction()
	{
		if (!$this -> _helper -> requireSubject('ynvideo_playlist') -> isValid())
		{
			return;
		}

		$this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('ynvideo_playlist');

		$video_id = (int)$this -> _getParam('video_id');
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('ynvideo_video', $video_id);
		}

		if (isset($video))
		{
			if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
			{
				$data = array(
					'result' => 0,
					'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have the authorization to view this video.'),
				);
				return $this -> _helper -> json($data);
			}

			$viewer = Engine_Api::_() -> user() -> getViewer();
			$playlistTbl = Engine_Api::_() -> getDbTable('playlists', 'ynvideo');
			$db = $playlistTbl -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$playlistAssoc = $playlist -> addVideoToPlaylist($video);

				if ($playlistAssoc)
				{
					$auth = Engine_Api::_() -> authorization() -> context;
					$auth -> setAllowed($playlistAssoc, 'registered', 'view', true);
					$auth -> setAllowed($playlistAssoc, 'registered', 'comment', true);
				}

				$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $actionTable -> addActivity($viewer, $playlist, 'ynvideo_playlist_add_video', '');

				if ($action != null)
				{
					$actionTable -> attachActivity($action, $video);
				}

				foreach ($actionTable->getActionsByObject($video) as $action)
				{
					$actionTable -> resetActivityBindings($action);
				}
				$db -> commit();

				$data = array(
					'result' => 1,
					'message' => $this -> view -> htmlLink($playlist -> getHref(), $playlist -> title)
				);
				return $this -> _helper -> json($data);
			}
			catch (Ynvideo_Model_ExistedException $e)
			{
				$data = array(
					'result' => -1,
					'message' => Zend_Registry::get('Zend_Translate') -> _('This video is existed in this playlist !!!'),
				);
				return $this -> _helper -> json($data);
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
		}

		$data = array(
			'result' => 0,
			'message' => Zend_Registry::get('Zend_Translate') -> _('This video doesn\'t exist. Please try another one !!!'),
		);
		return $this -> _helper -> json($data);
	}

	public function viewAction()
	{
		if (!$this -> _helper -> requireSubject('ynvideo_playlist') -> isValid())
		{
			return;
		}

		$this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('ynvideo_playlist');

		if (!$this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'view') -> isValid())
		{
			return;
		}

		$videoTbl = Engine_Api::_() -> getDbTable('videos', 'ynvideo');
		$videoTblName = $videoTbl -> info('name');
		$playlistAssocTbl = Engine_Api::_() -> getDbTable('playlistassoc', 'ynvideo');
		$playlistAssocTblName = $playlistAssocTbl -> info('name');

		$params = $this -> _getAllParams();
		$select = Engine_Api::_() -> ynvideo() -> getVideosSelect($params);
		$select -> join($playlistAssocTblName, "$playlistAssocTblName.video_id = $videoTblName.video_id") -> where("$playlistAssocTblName.playlist_id = ?", $playlist -> getIdentity()) -> where("$videoTblName.search = 1") -> where("$videoTblName.status = 1");

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$videoPerPage = $settings -> getSetting('ynvideo.page', 10);
		$this -> view -> videoPaginator = $videoPaginator = Zend_Paginator::factory($select);
		$videoPaginator -> setCurrentPageNumber($this -> _getParam('page'), 1);
		$videoPaginator -> setItemCountPerPage($videoPerPage);

		// Render
		$this -> _helper -> content -> setEnabled();

		$this -> view -> params = $_GET;
		$this -> view -> canDelete = $this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'delete') -> checkRequire();
		$this -> view -> canEdit = $this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'edit') -> checkRequire();
		$this -> view -> canRemove = $this -> _helper -> requireAuth() -> setAuthParams($playlist, null, 'remove') -> checkRequire();
	}

	public function removeAction()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynvideo_playlist', null, 'remove') -> isValid())
		{
			return;
		}

		$video_id = (int)$this -> _getParam('video_id');
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('video', $video_id);
		}
		if (!(isset($video) && $video != null))
		{
			return $this -> _helper -> requireSubject() -> forward();
		}

		if (!$this -> _helper -> requireSubject('ynvideo_playlist') -> isValid())
		{
			return;
		}
		$this -> view -> playlist = $playlist = Engine_Api::_() -> core() -> getSubject('ynvideo_playlist');

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynvideo_Form_Remove( array(
			'remove_title' => 'Remove video',
			'remove_description' => 'Are you sure you want to remove this video from the playlist?'
		));

		if (!$video)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("The video doesn't exist.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method.');
			return;
		}

		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		try
		{
			if (Engine_Api::_() -> ynvideo() -> removeVideoFromPlaylist($video -> getIdentity(), $playlist -> getIdentity()))
			{
				$this -> view -> status = true;
				$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The video has been removed from the playlist.');
			}
			else
			{
				$this -> view -> status = false;
				$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('There is an error occured, please try again.');
			}
			$db -> commit();

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _($this -> view -> message)),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
	}

}
?>