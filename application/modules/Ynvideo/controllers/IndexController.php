<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_IndexController extends Core_Controller_Action_Standard
{
	protected $_roles;

	public function init()
	{
		$this -> _roles = array(
			'owner',
			'parent_member',
			'owner_member',
			'owner_member_member',
			'owner_network',
			'registered',
			'everyone'
		);
		if (!$this -> _helper -> requireAuth() -> setAuthParams('video', null, 'view') -> isValid())
			return;
	}

	public function indexAction()
	{
		// Render
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function createAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireAuth() -> setAuthParams('video', null, 'create') -> isValid())
		{
			return;
		}

		// Render
		$this -> _helper -> content -> setEnabled();

		// Get navigation
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideo_main', array());

		// get categories
		$this -> view -> categories = $categories = Engine_Api::_() -> getDbTable('categories', 'ynvideo') -> getAllCategoriesAndSortByLevel();

		// set up data needed to check quota
		$parent_type = $this -> _getParam('parent_type');
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('subject_id'));
		if(empty($parent_type) || empty($parent_id)) {
			$parent_type = 'user';
			$parent_id = $viewer -> getIdentity();
		}

		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getApi('core', 'ynvideo') -> getVideosPaginator($values);

		//$this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
		// TODO [DangTH] : get the maximum video that a user can upload
		$this -> view -> quota = $quota = Engine_Api::_() -> ynvideo() -> getAllowedMaxValue('video', $viewer -> level_id, 'max');

		$this -> view -> current_count = $paginator -> getTotalItemCount();

		// Create form
		$this -> view -> form = $form = new Ynvideo_Form_Video( array(
			'title' => 'Add New Video',
			'parent_type' => $parent_type,
			'parent_id' => $parent_id
		));
		$this -> view -> parent_type = $parent_type;
		
		if ($this -> _getParam('type', false))
		{
			$form -> getElement('type') -> setValue($this -> _getParam('type'));
		}
		
		if ($this -> _getParam('parent_type', false))
		{
			$form -> getElement('parent_type') -> setValue($this -> _getParam('parent_type'));
		}
		
		if ($parent_id && $parent_type == 'user_playercard')
		{
			$form -> getElement('playercard_id') -> setValue($parent_id);
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		if (!$form -> isValid($post))
		{
			$values = $form -> getValues('url');
			return;
		}
		// Process
		$values = $form -> getValues();
		$values['parent_id'] = $parent_id;
		if($values['parent_type'] == 'user_playercard')
		{
			$values['parent_id'] = $values['playercard_id'];
		}
		else if($values['parent_type'] == 'group')
		{
			$group = Engine_Api::_() -> advgroup() -> getGroupUser($viewer);
			$values['parent_id'] = $group -> getIdentity();
		}
		else if($values['parent_type'] == 'user_library')
		{
			$library = $viewer -> getMainLibrary();
			$values['parent_id'] = $library -> getIdentity();
		}
			
		$values['owner_type'] = 'user';
		$values['owner_id'] = $viewer -> getIdentity();
		if ($values['subcategory_id'] == 0)
		{
			$values['subcategory_id'] = $values['category_id'];
		}
		$insert_action = false;
		

		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create video
			$table = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
            if ($values['type'] == '6')
            {
                $values['code'] = base64_decode($values['code']);
                $regex = "/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/";
                preg_match($regex, $values['code'], $matches);
                if(count($matches) > 2)
                {
                    $values['code'] = $matches[3];
                    $values['photo'] = 0;
                }
            }
			if ($values['type'] == Ynvideo_Plugin_Factory::getUploadedType())
			{
					$video = Engine_Api::_() -> getItem('video', $this -> _getParam('id'));
					$video -> setFromArray($values);
					$video -> save();
			}
			else
			{
				$video = $table -> createRow();
				$video -> setFromArray($values);
				if ($values['type'] == Ynvideo_Plugin_Factory::getVideoURLType() || $values['type'] == 6)
				{
					$video -> status = 1;
				}
				$video -> save();

				if ($values['type'] == Ynvideo_Plugin_Factory::getVideoURLType())
				{
					$adapter = Ynvideo_Plugin_Factory::getPlugin((int)$values['type']);
					$adapter -> getVideoImage($video -> getIdentity());
				}

				if ($values['type'] != Ynvideo_Plugin_Factory::getVideoURLType() && $values['type'] != 6)
				{
					$adapter = Ynvideo_Plugin_Factory::getPlugin((int)$values['type']);
					$adapter -> setParams(array('link' => $values['url']));
					if ($adapter -> fetchLink())
					{
						$video -> storeThumbnail($adapter -> getVideoThumbnailImage(), 'small');
						$video -> storeThumbnail($adapter -> getVideoLargeImage(), 'large');
					}
					$video -> code = $adapter -> getVideoCode();
					$video -> duration = $adapter -> getVideoDuration();
					$video -> status = 1;
					$video -> save();
				}
			}

			// Insert new action item
			$insert_action = true;

			if ($values['ignore'] == true)
			{
				$video -> status = 1;
				$video -> save();
				$insert_action = true;
			}
			
			// Set photo
			if (!empty($values['photo']))
			{
				$video -> setPhoto($form -> photo);
			}

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			if ($parent_type == 'user' || empty($parent_type))
			{
				$roles = array(
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
			}
			else
			{
				$roles = array(
					'owner',
					'parent_member',
					'registered',
					'everyone'
				);
			}
			if (isset($values['auth_view']))
			{
				$auth_view = $values['auth_view'];
			}
			else
			{
				$auth_view = "everyone";
			}
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}

			if ($parent_type != 'user')
			{
				$roles = array(
					'owner',
					'parent_member',
					'registered',
					'everyone'
				);
			}
			else
			{
				$roles = array(
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
			}
			if (isset($values['auth_comment']))
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}
			
			if(!empty($values['clubs']))
			{
				$clubMapping = Engine_Api::_() -> getDbTable('mappingvideos', 'advgroup');
				foreach($values['clubs'] as $club)
				{
				 	$row = $clubMapping -> createRow();
					$row -> video_id =  $video -> getIdentity();
					$row -> club_id =  $club;
					$row -> save();
				}
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			if ($insert_action && $video -> status == 1)
			{
				$owner = $video -> getOwner();
				if ($parent_type == 'group')
				{
				}
				elseif ($video -> parent_type == 'event')
				{
					$item = Engine_Api::_()->getItem($parent_type, $parent_id);
					if ($item)
						$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($owner, $item, 'ynevent_video_create');
				}
				else
				{
					$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($owner, $video, 'video_new');
				}
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
				}
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
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
		
		if ($video -> parent_type == 'group')
		{
			$group = $video -> getParent('group');
			$this -> _redirectCustom($group);
		}
		else
		if ($video -> parent_type == 'user_playercard')
		{
			$user_playercard = Engine_Api::_() -> getItem($video -> parent_type, $video -> parent_id);
			$this -> _redirectCustom($user_playercard);
		}
		else if ($video -> type == Ynvideo_Plugin_Factory::getUploadedType())
		{
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'video_general', true);
		}
		else
		{
			return $this -> _helper -> redirector -> gotoRoute(array(
				'user_id' => $viewer -> getIdentity(),
				'video_id' => $video -> getIdentity()
			), 'video_view', true);
		}
	}

	public function viewAction()
	{
		$video_id = $this -> _getParam('video_id');
		$video = Engine_Api::_() -> getItem('video', $video_id);
		if ($video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		$type = $video -> getType();

		$video = Engine_Api::_() -> core() -> getSubject('video');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Photo Url
		$photoUrl = $video -> getPhotoUrl('thumb.normal');
		$pos = strpos($photoUrl, "http");
		if ($pos === false)
		{
			$photoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $photoUrl;
		}

		//Get Video Url
		$videoUrl = $video -> getHref();
		$pos = strpos($videoUrl, "http");
		if ($pos === false)
		{
			$videoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $videoUrl;
		}

		//Adding meta tags for sharing
		$view = Zend_Registry::get('Zend_View');
		$og = '<meta property="og:image" content="' . $photoUrl . '" />';
		$og .= '<meta property="og:title" content="' . $video -> getTitle() . '" />';
		$og .= '<meta property="og:url" content="' . $videoUrl . '" />';
		$view -> layout() -> headIncludes .= $og;

		$watchLaterTbl = Engine_Api::_() -> getDbTable('watchlaters', 'ynvideo');
		$watchLaterTbl -> update(array(
			'watched' => '1',
			'watched_date' => date('Y-m-d H:i:s')
		), array(
			"video_id = {$video->getIdentity()}",
			"user_id = {$viewer->getIdentity()}"
		));

		// if this is sending a message id, the user is being directed from a coversation
		// check if member is part of the conversation
		$message_id = $this -> getRequest() -> getParam('message');
		$message_view = false;
		if ($message_id)
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			if ($conversation -> hasRecipient(Engine_Api::_() -> user() -> getViewer()))
			{
				$message_view = true;
			}
		}
		$this -> view -> message_view = $message_view;

		if (!$message_view && !$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$this -> view -> videoTags = $video -> tags() -> getTagMaps();

		// Check if edit/delete is allowed
		$this -> view -> can_edit = $can_edit = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> checkRequire();
		$this -> view -> can_delete = $can_delete = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> checkRequire();

		// check if embedding is allowed
		$can_embed = true;
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('video.embeds', 1))
		{
			$can_embed = false;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$can_embed = false;
		}
		$this -> view -> can_embed = $can_embed;

		$embedded = "";
		// increment count
		if ($video -> status == 1)
		{
			if (!$video -> isOwner($viewer))
			{
				$video -> view_count++;
				$video -> save();
				Engine_Api::_()->getDbTable('views', 'ynvideo')->addView($video);
			}
            $embedded = $video -> getRichContent(true);
		}

		if ($video -> type == Ynvideo_Plugin_Factory::getUploadedType() && $video -> status == 1)
		{
			$session = new Zend_Session_Namespace('mobile');
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_()-> ynresponsive1() -> isMobile();
			}
			if (!empty($video -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file1_id);
				}
				if ($storage_file)
				{
					$this -> view -> video_location1 = $storage_file -> map();
					$this -> view -> video_location = '';
				}
			}
			else 
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$this -> view -> video_location = $storage_file -> map();
					$this -> view -> video_location1 = '';
				}
			}
		}
		else
		if ($video -> type == Ynvideo_Plugin_Factory::getVideoURLType())
		{
			$this -> view -> video_location = $video -> code;
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> numberOfEmail = $settings -> getSetting('ynvideo.friend.emails', 5);
		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> rating_count = Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> rated = Engine_Api::_() -> ynvideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> videoEmbedded = $embedded;

		if ($video -> category_id)
		{
			$this -> view -> categories = $categories = Engine_Api::_() -> getDbTable('categories', 'ynvideo') -> getCategories(array(
				$video -> category_id,
				$video -> subcategory_id
			));
		}

		// Render
		$this -> _helper -> content -> setEnabled();
	}

	public function validationAction()
	{
		$video_type = $this -> _getParam('type');
		$code = $this -> _getParam('code');
		$ajax = $this -> _getParam('ajax', false);
		$valid = false;
        
        if ($video_type == '6')
        {
            $valid = true;
        }
        else 
        {
            $adapter = Ynvideo_Plugin_Factory::getPlugin($video_type);
            $adapter -> setParams(array('code' => $code));
            $valid = $adapter -> isValid();
        }
		$this -> view -> code = $code;
		$this -> view -> ajax = $ajax;
		$this -> view -> valid = $valid;
	}

	public function listAction()
	{
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function manageAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		$this -> view -> can_create = $this -> _helper -> requireAuth() -> setAuthParams('video', null, 'create') -> checkRequire();

		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function addToAction()
	{
		if (0 !== ($video_id = (int)$this -> getRequest() -> getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('ynvideo_video', $video_id)) && $video instanceof Ynvideo_Model_Video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject('video') -> isValid())
		{
			return;
		}

		$this -> view -> video = $video;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			$this -> view -> loggedIn = false;
		}
		else
		{
			$this -> view -> loggedIn = true;
			$form = new Ynvideo_Form_Playlist_QuickCreate();
			if (!$this -> getRequest() -> isPost())
			{
				// if the request is not the post method, set the video_id for the form and render the form
				$this -> view -> form = $form;
				$form -> getElement('video_id') -> setValue($video -> getIdentity());
			}
			else
			{
				if (!$form -> isValid($this -> getRequest() -> getPost()))
				{
					$data = array(
						'result' => 0,
						'message' => Zend_Registry::get('Zend_Translate') -> _('The inputed value is invalid.'),
					);
					return $this -> _helper -> json($data);
				}

				if (!$this -> _helper -> requireAuth() -> setAuthParams('ynvideo_playlist', null, 'create') -> checkRequire())
				{
					$data = array(
						'result' => 0,
						'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have the authorization to create new playlist.'),
					);
					return $this -> _helper -> json($data);
				}

				$values = $form -> getValues();
				$values['creation_date'] = date('Y-m-d H:i:s');
				$values['modified_date'] = date('Y-m-d H:i:s');
				$values['user_id'] = $viewer -> getIdentity();
				$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynvideo');
				$db = $playlistTable -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$playlist = $playlistTable -> createRow();
					$playlist -> setFromArray($values);
					$playlist -> video_count = 1;
					$playlist -> save();

					$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $playlist, 'ynvideo_add_video_new_playlist');
					if ($action != null)
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $video);
					}

					// Rebuild privacy
					$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
					foreach ($actionTable->getActionsByObject($playlist) as $action)
					{
						$actionTable -> resetActivityBindings($action);
					}

					$auth = Engine_Api::_() -> authorization() -> context;
					if (empty($values['auth_view']))
					{
						$values['auth_view'] = 'everyone';
					}
					$viewMax = array_search($values['auth_view'], $this -> _roles);

					foreach ($this->_roles as $i => $role)
					{
						$auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
						$auth -> setAllowed($playlist, $role, 'comment', true);
					}

					$playlistAssocTable = Engine_Api::_() -> getDbTable('playlistassoc', 'ynvideo');
					$playlistAssoc = $playlistAssocTable -> createRow();
					$playlistAssoc -> playlist_id = $playlist -> getIdentity();
					$playlistAssoc -> video_id = $values['video_id'];
					$playlistAssoc -> creation_date = date('Y-m-d H:i:s');
					$playlistAssoc -> save();

					$db -> commit();

					$data = array(
						'result' => 1,
						'message' => $this -> view -> htmlLink($playlist -> getHref(), $playlist -> title),
					);
					return $this -> _helper -> json($data);
				}
				catch (Exception $e)
				{
					$db -> rollBack();
					throw $e;
				}
			}
		}

		$this -> view -> playlists = Engine_Api::_() -> ynvideo() -> getPlaylists($viewer -> getIdentity());
		$this -> _helper -> layout -> disableLayout();
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}

		if (0 !== ($video_id = (int)$this -> _getParam('video_id')) && null !== ($video = Engine_Api::_() -> getItem('video', $video_id)) && $video instanceof Ynvideo_Model_Video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject('video') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity() != $video -> owner_id && !$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> isValid())
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideo_main', array(), 'ynvideo_main_manage');

		$this -> view -> video = $video;
		$this -> view -> form = $form = new Ynvideo_Form_Edit( array(
			'video' => $video,
			'title' => 'Edit Video',
			'parent_type' => $video -> parent_type,
			'parent_id' => $video -> parent_id
		));
		$mappingTable = Engine_Api::_() -> getDbTable('mappingvideos', 'advgroup');
		$select = $mappingTable -> select() -> where("video_id = ?", $video_id);
		$clubs = $mappingTable -> fetchAll($select);
		$clubArray = array();
		foreach($clubs as $club)
		{
			$clubArray[] = $club -> club_id;
		}
		$form -> clubs -> setValue($clubArray);
		$form -> removeElement('parent_type');
		$form -> removeElement('playercard_id');
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$values = $form -> getValues();
			$video -> setFromArray($values);
			$video -> save();

			// Set photo
			if (!empty($values['photo']))
			{
				$video -> setPhoto($form -> photo);
			}
			
			$clubMapping = Engine_Api::_() -> getDbTable('mappingvideos', 'advgroup');
			$clubMapping -> delete(array('video_id' => $video->getIdentity()));
			
			if(!empty($values['clubs']))
			{
				foreach($values['clubs'] as $club)
				{
				 	$row = $clubMapping -> createRow();
					$row -> video_id =  $video -> getIdentity();
					$row -> club_id =  $club;
					$row -> save();
				}
			}
			
			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'parent_member',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);
			if ($values['auth_view'])
			{
				$auth_view = $values['auth_view'];
			}
			else
			{
				$auth_view = "everyone";
			}

			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'view', ($i <= $viewMax));
			}

			if ($values['auth_comment'])
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($video, $role, 'comment', ($i <= $commentMax));
			}

			// Add tags
			$tags = preg_split('/[,]+/', $values['tags']);
			$video -> tags() -> setTagMaps($viewer, $tags);

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($video) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			if (isset($favorites))
			{
				foreach ($favorites as $favorite)
				{
					foreach ($actionTable->getActionsByObject($favorite) as $action)
					{
						$actionTable -> resetActivityBindings($action);
					}
				}
			}
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		if($video -> parent_type == 'group')
		{
			$group = $video -> getParent('group');
			$this -> _redirectCustom($group);
		}
		elseif($video -> parent_type == 'user_playercard')
		{
			$user_playercard = Engine_Api::_() -> getItem($video -> parent_type, $video -> parent_id);
			$this -> _redirectCustom($user_playercard);
		}
		elseif($video -> parent_type == 'user_library') {
			$this -> _redirectCustom($video -> getOwner());
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'video_general', true);
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$video = Engine_Api::_() -> getItem('video', $this -> getRequest() -> getParam('video_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynvideo_Form_Delete();

		if (!$video)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Video doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $video -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> getApi('core', 'ynvideo') -> deleteVideo($video);
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Video has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'video_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function ratingAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$video_id = (int)$this -> _getParam('video_id');
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('video', $video_id);
			if ($video)
			{
				Engine_Api::_() -> core() -> setSubject($video);
			}
		}
		if (!$this -> _helper -> requireSubject('video') -> isValid())
		{
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();

		$rating = (int)$this -> _getParam('rating');
		$rating_type = $this ->_getParam('rating_type');

		$tableRating = Engine_Api::_() -> getDbTable('reviewRatings', 'ynvideo');
		$db = $tableRating -> getAdapter();
		$db -> beginTransaction();

		try
		{
			
			$tableRatingType = Engine_Api::_() -> getItemTable('ynvideo_ratingtype');
			$rating_types = $tableRatingType -> getAllRatingTypes();
			// Specific Rating
			foreach($rating_types as $item)
			{
				if($item -> getIdentity() == $rating_type) {
					$row = $tableRating -> getRowRatingThisType($item -> getIdentity(), $video -> getIdentity(), $viewer -> getIdentity());
					if(!$row)
					{
						$row = $tableRating -> createRow();
					}
					$row -> resource_id = $video -> getIdentity();
					$row -> user_id = $viewer -> getIdentity();
					$row -> rating_type = $item -> getIdentity();
					$row -> rating = $rating;
					$row -> save();
				}
			}
			$player = Engine_Api::_() -> getItem('user_playercard', $video -> parent_id);
			if($player){
				$player -> rating = $player -> getOverallRating();
				$player -> save();
			}
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		
	    $overrallValue = $tableRating -> getRatingOfType($rating_type, $video -> getIdentity());

		$data = array();
		$data[] = array(
			'rating' => $overrallValue,
			'rating_type' => $rating_type,
		);

		return $this -> _helper -> json($data);
	}
	
	public function rateAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$video_id = (int)$this -> _getParam('video_id');
		if ($video_id)
		{
			$video = Engine_Api::_() -> getItem('video', $video_id);
			if ($video)
			{
				Engine_Api::_() -> core() -> setSubject($video);
			}
		}
		if (!$this -> _helper -> requireSubject('video') -> isValid())
		{
			return;
		}

		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();

		$rating = (int)$this -> _getParam('rating');

		$table = Engine_Api::_() -> getDbtable('ratings', 'ynvideo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> ynvideo() -> setRating($video_id, $user_id, $rating);

			$video = Engine_Api::_() -> getItem('video', $video_id);
			$video -> rating = Engine_Api::_() -> ynvideo() -> getRating($video -> getIdentity());
			$video -> save();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$total = Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity());

		$data = array();
		$data[] = array(
			'total' => $total,
			'rating' => $rating,
		);

		return $this -> _helper -> json($data);
	}

	public function uploadAction()
	{
		if (isset($_GET['ul']) || isset($_FILES['Filedata']))
			return $this -> _forward('upload-video', null, null, array('format' => 'json'));

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$this -> view -> form = $form = new Ynvideo_Form_Video();
		$this -> view -> navigation = $this -> getNavigation();

		if (!$this -> getRequest() -> isPost())
		{
			if (null !== ($album_id = $this -> _getParam('album_id')))
			{
				$form -> populate(array('album' => $album_id));
			}
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$album = $form -> saveValues();
	}

	public function uploadVideoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!$_FILES['fileToUpload'])
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		if (!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload1') . print_r($_FILES, true);
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		$illegal_extensions = array(
			'php',
			'pl',
			'cgi',
			'html',
			'htm',
			'txt'
		);
		if (in_array(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION), $illegal_extensions))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Type');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}

		$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			
			$values['owner_id'] = $viewer -> getIdentity();
			$parent_id = 0;
			$parent_type = $this -> _getParam('parent_type');
			if($parent_type == 'user_playercard')
			{
				$parent_id = $this -> _getParam('playerId', 0);
			}
			else if($parent_type == 'group')
			{
				$group = Engine_Api::_() -> advgroup() -> getGroupUser($viewer);
				$parent_id = $group -> getIdentity();
			}
			else if($parent_type == 'user_library')
			{
				$parent_id = $viewer -> getIdentity();
			}
			
			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity(),
				'parent_type' => $parent_type,
				'parent_id' => $parent_id
			);
			$video = Engine_Api::_() -> ynvideo() -> createVideo($params, $_FILES['fileToUpload'], $values);

			$status = 1;
			$name = $_FILES['fileToUpload']['name'];
			$code = $video -> code;
			$video_id = $video -> video_id;

			// sets up title and owner_id now just incase members switch page as soon as upload is completed
			$video -> title = $_FILES['fileToUpload']['name'];
			$video -> owner_id = $viewer -> getIdentity();
			$video -> save();
			$db -> commit();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'name'=> $name, 'code' => $code, 'video_id' => $video_id)));
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.') . $e;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
		}
	}

	public function composeUploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			$this -> _redirect('login');
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid method.');
			return;
		}

		$video_title = $this -> _getParam('title');
		$video_url = $this -> _getParam('uri');
		$video_type = $this -> _getParam('type');
		$composer_type = $this -> _getParam('c_type', 'wall');
        $valid = false;
		// extract code
        if ($video_type != Ynvideo_Plugin_Factory::getUploadedType())
		{
			$adapter = Ynvideo_Plugin_Factory::getPlugin((int)$video_type);
			$adapter -> setParams(array('link' => $video_url));
			$valid = $adapter -> isValid();
		}

		// check to make sure the user has not met their quota of # of allowed video uploads
		// set up data needed to check quota
		$values['user_id'] = $viewer -> getIdentity();
		$paginator = Engine_Api::_() -> getApi('core', 'ynvideo') -> getVideosPaginator($values);
		//$quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
		// TODO [DangTH] : get the max value
		$this -> view -> quota = $quota = Engine_Api::_() -> ynvideo() -> getAllowedMaxValue('video', $viewer -> level_id, 'max');
		$current_count = $paginator -> getTotalItemCount();

		if (($current_count >= $quota) && !empty($quota))
		{
			// return error message
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('You have already uploaded the maximum number of videos allowed. If you would like to upload a new video, please delete an old one first.');
		}
		else
		if ($valid)
		{
			$db = Engine_Api::_() -> getDbtable('videos', 'ynvideo') -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$table = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
				$video = $table -> createRow();
				$video -> owner_id = $viewer -> getIdentity();
				$video -> type = $video_type;
				$video -> parent_type = 'user';
				$video -> parent_id = $viewer -> getIdentity();
                if($video_type == 6)
                {
                    $regex = '/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/';
                    preg_match($regex, $video_url, $matches);
                    if(count($matches) > 2)
                    {
                        $video_url = $matches[3];
                    }
                }
				$video -> code = $video_url;

				if ($video_type == Ynvideo_Plugin_Factory::getVideoURLType() || $video_type == 6)
				{
					$video -> title = Ynvideo_Plugin_Adapter_VideoURL::getDefaultTitle();
				}
				else
				{
					if ($adapter -> fetchLink())
					{
						// create video
						$video -> storeThumbnail($adapter -> getVideoThumbnailImage(), 'small');
						$video -> storeThumbnail($adapter -> getVideoLargeImage(), 'large');
						$video -> title = $adapter -> getVideoTitle();
						$video -> description = $adapter -> description;
						$video -> duration = $adapter -> getVideoDuration();
						$video -> code = $adapter -> getVideoCode();
						$video -> save();
					}
				}

				// If video is from the composer, keep it hidden until the post is complete
				if ($composer_type)
				{
					$video -> search = 0;
				}
				$video -> status = 1;
				$video -> save();

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			// make the video public
			if ($composer_type === 'wall')
			{
				// CREATE AUTH STUFF HERE
				$auth = Engine_Api::_() -> authorization() -> context;
				$roles = array(
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'registered',
					'everyone'
				);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($video, $role, 'view', ($i <= $roles));
					$auth -> setAllowed($video, $role, 'comment', ($i <= $roles));
				}
			}

			$this -> view -> status = true;
			$this -> view -> video_id = $video -> video_id;
			$this -> view -> photo_id = $video -> photo_id;
			$this -> view -> title = $video -> title;
            $this -> view -> type = $video -> type;
			$this -> view -> description = $video -> description;
			if ($video_type == Ynvideo_Plugin_Factory::getVideoURLType() || $video_type == 6)
			{
				$this -> view -> src = Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png';
			}
			else
			{
				$this -> view -> src = $video -> getPhotoUrl();
			}
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Video posted successfully.');
		}
		else
		{
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('We could not find a video there - please check the URL and try again.');
		}
	}

	public function addToGroupAction()
	{
		$video = Engine_Api::_() -> core() -> getSubject();

	}
	
	public function mobileViewAction()
	{
		$video_id = $this -> _getParam('video_id');
		$video = Engine_Api::_() -> getItem('video', $video_id);
		if ($video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		$type = $video -> getType();

		$video = Engine_Api::_() -> core() -> getSubject('video');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Photo Url
		$photoUrl = $video -> getPhotoUrl('thumb.normal');
		$pos = strpos($photoUrl, "http");
		if ($pos === false)
		{
			$photoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $photoUrl;
		}

		//Get Video Url
		$videoUrl = $video -> getHref();
		$pos = strpos($videoUrl, "http");
		if ($pos === false)
		{
			$videoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $videoUrl;
		}

		//Adding meta tags for sharing
		$view = Zend_Registry::get('Zend_View');
		$og = '<meta property="og:image" content="' . $photoUrl . '" />';
		$og .= '<meta property="og:title" content="' . $video -> getTitle() . '" />';
		$og .= '<meta property="og:url" content="' . $videoUrl . '" />';
		$view -> layout() -> headIncludes .= $og;

		$watchLaterTbl = Engine_Api::_() -> getDbTable('watchlaters', 'ynvideo');
		$watchLaterTbl -> update(array(
			'watched' => '1',
			'watched_date' => date('Y-m-d H:i:s')
		), array(
			"video_id = {$video->getIdentity()}",
			"user_id = {$viewer->getIdentity()}"
		));

		// if this is sending a message id, the user is being directed from a coversation
		// check if member is part of the conversation
		$message_id = $this -> getRequest() -> getParam('message');
		$message_view = false;
		if ($message_id)
		{
			$conversation = Engine_Api::_() -> getItem('messages_conversation', $message_id);
			if ($conversation -> hasRecipient(Engine_Api::_() -> user() -> getViewer()))
			{
				$message_view = true;
			}
		}
		$this -> view -> message_view = $message_view;

		if (!$message_view && !$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}

		$this -> view -> videoTags = $video -> tags() -> getTagMaps();

		// Check if edit/delete is allowed
		$this -> view -> can_edit = $can_edit = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> checkRequire();
		$this -> view -> can_delete = $can_delete = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> checkRequire();

		// check if embedding is allowed
		$can_embed = true;
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('video.embeds', 1))
		{
			$can_embed = false;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$can_embed = false;
		}
		$this -> view -> can_embed = $can_embed;

		$embedded = "";
		// increment count
		if ($video -> status == 1)
		{
			if (!$video -> isOwner($viewer))
			{
				$video -> view_count++;
				$video -> save();
				Engine_Api::_()->getDbTable('views', 'ynvideo')->addView($video);
			}
            $embedded = $video -> getRichContent(true);
		}

		if ($video -> type == Ynvideo_Plugin_Factory::getUploadedType() && $video -> status == 1)
		{
			$session = new Zend_Session_Namespace('mobile');
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_()-> ynresponsive1() -> isMobile();
			}
			if (!empty($video -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file1_id);
				}
				if ($storage_file)
				{
					$this -> view -> video_location1 = $storage_file -> map();
					$this -> view -> video_location = '';
				}
			}
			else 
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$this -> view -> video_location = $storage_file -> map();
					$this -> view -> video_location1 = '';
				}
			}
		}
		else
		if ($video -> type == Ynvideo_Plugin_Factory::getVideoURLType())
		{
			$this -> view -> video_location = $video -> code;
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> numberOfEmail = $settings -> getSetting('ynvideo.friend.emails', 5);
		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> rating_count = Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> rated = Engine_Api::_() -> ynvideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> videoEmbedded = $embedded;

		if ($video -> category_id)
		{
			$this -> view -> categories = $categories = Engine_Api::_() -> getDbTable('categories', 'ynvideo') -> getCategories(array(
				$video -> category_id,
				$video -> subcategory_id
			));
		}

	}
	public function popupViewAction()
	{
		$video_id = $this -> _getParam('video_id');
		$video = Engine_Api::_() -> getItem('video', $video_id);
		if ($video)
		{
			Engine_Api::_() -> core() -> setSubject($video);
		}
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		$type = $video -> getType();

		$video = Engine_Api::_() -> core() -> getSubject('video');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		//Get Photo Url
		$photoUrl = $video -> getPhotoUrl('thumb.normal');
		$pos = strpos($photoUrl, "http");
		if ($pos === false)
		{
			$photoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $photoUrl;
		}

		//Get Video Url
		$videoUrl = $video -> getHref();
		$pos = strpos($videoUrl, "http");
		if ($pos === false)
		{
			$videoUrl = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $videoUrl;
		}

		$watchLaterTbl = Engine_Api::_() -> getDbTable('watchlaters', 'ynvideo');
		$watchLaterTbl -> update(array(
			'watched' => '1',
			'watched_date' => date('Y-m-d H:i:s')
		), array(
			"video_id = {$video->getIdentity()}",
			"user_id = {$viewer->getIdentity()}"
		));

		// Check if edit/delete is allowed
		$this -> view -> can_edit = $can_edit = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'edit') -> checkRequire();
		$this -> view -> can_delete = $can_delete = $this -> _helper -> requireAuth() -> setAuthParams($video, null, 'delete') -> checkRequire();
		$embedded = "";
		// increment count
		if ($video -> status == 1)
		{
			if (!$video -> isOwner($viewer))
			{
				$video -> view_count++;
				$video -> save();
				Engine_Api::_()->getDbTable('views', 'ynvideo')->addView($video);
			}
            $embedded = $video -> getRichContent(true);
		}

		if ($video -> type == Ynvideo_Plugin_Factory::getUploadedType() && $video -> status == 1)
		{
			$session = new Zend_Session_Namespace('mobile');
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_()-> ynresponsive1() -> isMobile();
			}
			if (!empty($video -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file1_id);
				}
				if ($storage_file)
				{
					$this -> view -> video_location1 = $storage_file -> map();
					$this -> view -> video_location = '';
				}
			}
			else 
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$this -> view -> video_location = $storage_file -> map();
					$this -> view -> video_location1 = '';
				}
			}
		}
		else
		if ($video -> type == Ynvideo_Plugin_Factory::getVideoURLType())
		{
			$this -> view -> video_location = $video -> code;
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> numberOfEmail = $settings -> getSetting('ynvideo.friend.emails', 5);
		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> rating_count = Engine_Api::_() -> ynvideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> rated = Engine_Api::_() -> ynvideo() -> checkRated($video -> getIdentity(), $viewer -> getIdentity());
		$this -> view -> videoEmbedded = $embedded;
	}
}
