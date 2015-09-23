<?php
class Advalbum_AlbumController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$album_id = 0;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('advalbum_album', null, 'view') -> isValid())
			return;

		if (0 !== ($photo_id = ( int )$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('advalbum_photo', $photo_id)))
		{
			Engine_Api::_() -> core() -> setSubject($photo);
		}
		
		else
		if (0 !== ($album_id = ( int )$this -> _getParam('album_id')) && null !== ($album = Engine_Api::_() -> getItem('advalbum_album', $album_id)))
		{
			Engine_Api::_() -> core() -> setSubject($album);
		}
	}

	/*
	 * Download all images in album
	 *
	 */
	public function downloadAction()
	{
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> content -> setNoRender();
		Engine_Api::_() -> getApi('createzipfile', 'advalbum') -> downloadAlbum(( int )$this -> _getParam('album_id'));
	}

	public function editAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_main');

		// Hack navigation
		foreach ($navigation->getPages () as $page)
		{
			if ($page -> route != 'album_general' || $page -> action != 'manage')
				continue;
			$page -> active = true;
		}

		// Prepare data
		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_Edit();
		if ($album->virtual)
		{
			$form->removeElement('auth_add_photo');
			$form->removeElement('auth_tag');
		}
		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($album -> toArray());
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			foreach ($roles as $role)
			{
				if (1 === $auth -> isAllowed($album, $role, 'view'))
				{
					$form -> auth_view -> setValue($role);
				}
				if (1 === $auth -> isAllowed($album, $role, 'comment'))
				{
					$form -> auth_comment -> setValue($role);
				}
				if (!$album->virtual)
				{
					if (1 === $auth -> isAllowed($album, $role, 'addphoto'))
					{
						$form -> auth_add_photo -> setValue($role);
					}
					if (1 === $auth -> isAllowed($album, $role, 'tag'))
					{
						$form -> auth_tag -> setValue($role);
					}
				}				
			}

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

		// Process
		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			$album -> setFromArray($values);
			$album -> save();

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'everyone'
			);
			if ($values['auth_view'])
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
			}
			
			if ($values['auth_comment'])
				$auth_comment = $values['auth_comment'];
			else
				$auth_comment = "everyone";
			$commentMax = array_search($values['auth_comment'], $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
			}
			if (!$album->virtual)
			{
				if ($values['auth_add_photo'])
					$auth_add_photo = $values['auth_add_photo'];
				else
					$auth_add_photo = "everyone";
				$addphotoMax = array_search($values['auth_add_photo'], $roles);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($album, $role, 'addphoto', ($i <= $addphotoMax));
				}
				
				if ($values['auth_tag'])
					$auth_tag = $values['auth_tag'];
				else
					$auth_tag = "everyone";
				$tagMax = array_search($values['auth_tag'], $roles);
				foreach ($roles as $i => $role)
				{
					$auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
				}
			}
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$db -> beginTransaction();
		try
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject ( $album ) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manage'), 'album_general', true);
	}

	public function orderAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		$album = Engine_Api::_() -> core() -> getSubject();

		$order = $this -> _getParam('order');
		if (!$order)
		{
			$this -> view -> status = false;
			return;
		}

		// Get a list of all photos in this album, by order
		$photoTable = Engine_Api::_() -> getItemTable('advalbum_photo');
		$currentOrder = $photoTable -> select() -> from($photoTable, 'photo_id') -> where('album_id = ?', $album -> getIdentity()) -> order('order ASC') -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);

		// Find the starting point?
		$start = null;
		$end = null;
		for ($i = 0, $l = count($currentOrder); $i < $l; $i++)
		{
			if (in_array($currentOrder[$i], $order))
			{
				$start = $i;
				$end = $i + count($order);
				break;
			}
		}

		if (null === $start || null === $end)
		{
			$this -> view -> status = false;
			return;
		}

		for ($i = 0, $l = count($currentOrder); $i < $l; $i++)
		{
			if ($i >= $start && $i <= $end)
			{
				$photo_id = $order[$i - $start];
			}
			else
			{
				$photo_id = $currentOrder[$i];
			}
			$photoTable -> update(array('order' => $i), array('photo_id = ?' => $photo_id));
		}

		$this -> view -> status = true;
	}

	public function viewAction()
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;

		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		if (@!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'view') -> isValid())
			return;
	
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> playlist = $playlist = $this -> _getParam('playlist');
		if($this -> _getParam('slideshow'))
		{
			$this -> view -> body_class = 'slideshow-active';
		}
		else 
		{
			$this -> _helper -> content -> setEnabled();
		}
		$this -> view -> slideshow = $slideshow = $this -> _getParam('slideshow');
		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($album -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($album -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		
		$table = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$tableName = $table -> info('name');
		if ($album->virtual)
		{
			$virtualPhotoTbl = Engine_Api::_() -> getDbtable('virtualphotos', 'advalbum');
			$virtualPhotoTblName = $virtualPhotoTbl -> info('name');
			$db = $virtualPhotoTbl->getAdapter();
			$virtualPhotoIds = $db
					-> select()
					-> from($virtualPhotoTblName)
					-> where("album_id = ? ", $album -> getIdentity())
					-> query()
					-> fetchAll(Zend_Db::FETCH_COLUMN, 1);
			$virtualPhotoIds = array_unique($virtualPhotoIds);
			if (!count($virtualPhotoIds))
			{
				$virtualPhotoIds = "";
			}
			$select = $table -> select() -> from($tableName) -> where("photo_id IN (?)", $virtualPhotoIds) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}
		else
		{
			$select = $table -> select() -> from($tableName) -> where("album_id = ?", $album -> album_id) -> order("order");
			$photo_list = $table -> fetchAll($select);
		}

		$this -> view -> rating_count = Engine_Api::_() -> advalbum() -> countRating($album -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$this -> view -> is_rated = Engine_Api::_() -> advalbum() -> checkRated($album -> getIdentity(), $viewer -> getIdentity(), Advalbum_Plugin_Constants::RATING_TYPE_ALBUM);
		$session = new Zend_Session_Namespace('mobile');

		if ($slideshow || $playlist)
		{
			// get the photos (all)
			if ($playlist)
			{
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_playlist.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list
				));
				$response = Zend_Controller_Front::getInstance() -> getResponse();
				$response -> setHeader('Content-Type', 'text/xml', TRUE);
			}
			else
			{
				// $this->_helper->layout->setLayout('default-simple');
				$this -> _helper -> layout -> disableLayout();
				$this -> view -> html_full = $this -> view -> partial('_slideshow.tpl', array(
					'album' => $album,
					'photo_list' => $photo_list,
					'effect' => $this -> _getParam('effect')
				));
			}
		}
		else
		{
			// Prepare params
			$this -> view -> page = $page = $this -> _getParam('page');

			// Prepare data
			if ($album->virtual)
			{
				$this -> view -> paginator = $paginator = Zend_Paginator::factory($photo_list);
			}
			else
			{
				$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
			}
			$paginator -> setItemCountPerPage($settings -> getSetting('album_page', 24));
			$paginator -> setCurrentPageNumber($page);

			// Do other stuff
			$this -> view -> mine = true;
			$this -> view -> can_add_photo= $can_add_photo = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'addphoto') -> checkRequire();
			
			$this -> view -> can_edit = $this -> _helper -> requireAuth() -> setAuthParams($album, null, 'edit') -> checkRequire();
			if (!$album -> getOwner() -> isSelf(Engine_Api::_() -> user() -> getViewer()))
			{
				$album -> view_count++;
				$album -> save();
				$this -> view -> mine = false;
			}

			// other albums
			$limit = $settings -> getSetting('album_others', 4);
			$otherTable = Engine_Api::_() -> getDbTable('albums', 'advalbum');
			$otherTableName = $otherTable -> info('name');
			$otherSelect = $otherTable -> select() -> from($otherTableName) -> where("owner_id  = ?", $album -> owner_id) -> where("album_id  <> ?", $album -> album_id) -> where("search = ?", "1") -> order("view_count DESC") -> limit($limit);
			$otherAlbums = $otherTable -> fetchAll($otherSelect);
			$this -> view -> otherAlbums = $otherAlbums;

			$photo_listing_id = 'photo_listing_in_album_view';
			$no_photos_message = $this -> view -> translate('There is no photo.');
			if ($this -> view -> mine || $this -> view -> can_edit)
			{
				$sortable = 1;
			}
			else
			{
				$sortable = 0;
			}
			$photo_listing_id = 'photo_listing_in_album_' . $album -> getIdentity();
			$no_photos_message = $this -> view -> translate('There is no photo in this album.');
			$strRand = rand(1, 100) . rand(1, 100);
			$this -> view -> rand = $strRand;
			$auto_show = 0;
			if ($this -> _getParam('id') && $this -> _getParam('id') != "")
			{
				$auto_show = $this -> _getParam('id');
			}
			$this -> view -> auto_show = $auto_show;
			$mode_grid = $mode_pinterest = 1;
			$mode_enabled = array();
			$view_mode = 'grid';
			
			if(isset($params['mode_grid']))
			{
				$mode_grid = $params['mode_grid'];
			}
			if($mode_grid)
			{
				$mode_enabled[] = 'grid';
			}			
			if(isset($params['mode_pinterest']))
			{
				$mode_pinterest = $params['mode_pinterest'];
			}
			if($mode_pinterest)
			{
				$mode_enabled[] = 'pinterest';
			}
			if(isset($params['view_mode']))
			{
				$view_mode = $params['view_mode'];
			}			
			if($mode_enabled && !in_array($view_mode, $mode_enabled))
			{
				$view_mode = $mode_enabled[0];
			}		
			
			$class_mode = "ynalbum-grid-view";
			switch ($view_mode) 
			{
				case 'pinterest':
					$class_mode = "ynalbum-pinterest-view";
					break;
				default:
					$class_mode = "ynalbum-grid-view";
					break;
			}
			$this -> view -> html_photo_list = $this -> view -> partial('_photolist.tpl', array(
				'paginator' => $paginator,
				'photo_listing_id' => $photo_listing_id,
				'sortable' => $sortable,
				'no_photos_message' => $no_photos_message,
				'untitled' => 1,
				'no_author_info' => 1,
				'show_title_info' => 1,
				'css' => 'global_form_box',
				'no_bottom_space' => 1,
				'rand' => $strRand,
				'auto_show' => $auto_show,
				'album' => $album,
				'class_mode' => $class_mode,
				'view_mode' => $view_mode,
				'mode_enabled' => $mode_enabled
			));
			if ($session -> mobile)
			{
				$this -> view -> html_mobile_slideshow = $this -> view -> partial('_m_slideshow.tpl', 'advalbum', array('photo_list' => $paginator));
			}
		}
	}

	public function deleteAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> getItem('advalbum_album', $this -> getRequest() -> getParam('album_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($album, null, 'delete') -> isValid())
			return;

		// print_r($album);die;
		$this -> view -> form = $form = new Advalbum_Form_Album_Delete();

		if (!$album)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Album doesn't exists or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> delete();
			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Album has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'album_general', true),
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'messages' => Array($this -> view -> message)
		));
	}

	public function deleteAdminAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> core() -> getSubject();

		$this -> view -> form = $form = new Advalbum_Form_Album_Delete();
		if (!$album)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Album doesn't exists or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> delete();
			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Album has been deleted.');
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => 10,
			'parentRefresh' => 10,
			'messages' => array('')
		));
	}

	public function editphotosAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_main');

		// Hack navigation
		foreach ($navigation->getPages () as $page)
		{
			if ($page -> route != 'album_general' || $page -> action != 'manage')
				continue;
			$page -> active = true;
		}

		// Prepare data
		$this -> view -> album = $album = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> max_color = Engine_Api::_()->getDbTable("settings", "core")->getSetting("advalbum.maxcolor", 1);
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage($paginator -> getTotalItemCount());

		// Make form
		$this -> view -> form = $form = new Advalbum_Form_Album_Photos();
		$colorAll = Engine_Api::_()->getDbTable("colors", "advalbum")->getColorAssoc();
		foreach ($paginator as $photo)
		{
			$subform = new Advalbum_Form_Photo_Edit( array('elementsBelongTo' => $photo -> getGuid()));
			$subform -> populate($photo -> toArray());
			$mainColors = $photo->getColors();
			$orderedColors = $this->order($colorAll, $mainColors);
			$subform -> getElement('color')->addMultiOptions($orderedColors);
			$subform -> getElement('color')->setValue($mainColors);
			$form -> addSubForm($subform, $photo -> getGuid());
			$form -> cover -> addMultiOption($photo -> getIdentity(), $photo -> getIdentity());
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$table = $album -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();
			if (!empty($values['cover']))
			{
				$album -> photo_id = $values['cover'];
				$album -> save();
			}

			// Process
			foreach ($paginator as $photo)
			{
				$subform = $form -> getSubForm($photo -> getGuid());
				$values = $subform -> getValues();
				$values = $values[$photo -> getGuid()];
				unset($values['photo_id']);
				if (isset($values['delete']) && $values['delete'] == '1')
				{
					$photo -> delete();
				}
				else
				{
					$photo -> setFromArray($values);
					if (isset($values['color']) && !empty($values['color']))
					{
						$photo -> saveColors($values['color']);
					}
					$photo -> save();
				}
			}

			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'view',
			'album_id' => $album -> album_id
		), 'album_specific', true);
	}

	protected function order($colorAll, $mainColors)
	{
		$temp = array();
		foreach ($mainColors  as $color)
		{
			$temp[$color] = $color;
		}
		foreach ($colorAll as $key => $value)
		{
			if (!in_array($key, $mainColors))
			{
				$temp[$key] = $value;
			}
		}
		return $temp;
	}
	
	public function composeUploadAction()
	{
		if (!Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			$this -> _redirect('login');
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid method');
			return;
		}

		if (empty($_FILES['Filedata']))
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
			return;
		}

		// Get album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$type = $this -> _getParam('type', 'wall');

			if (empty($type))
				$type = 'wall';

			$album = $table -> getSpecialAlbum($viewer, $type);

			$photo = Engine_Api::_() -> advalbum() -> createPhoto(array(
				'owner_type' => 'user',
				'owner_id' => Engine_Api::_() -> user() -> getViewer() -> getIdentity()
			), $_FILES['Filedata']);

			if ($type === 'message')
			{
				$photo -> title = Zend_Registry::get('Zend_Translate') -> _('Attached Image');
			}

			$photo -> album_id = $album -> album_id;
			$photo -> save();

			if ($type === 'message')
			{
				Engine_Api::_() -> getApi('search', 'core') -> unindex($photo);
			}

			if (!$album -> photo_id)
			{
				$album -> photo_id = $photo -> getIdentity();
				$album -> save();
			}

			if ($type != 'message')
			{
				// Authorizations
				$auth = Engine_Api::_() -> authorization() -> context;
				$auth -> setAllowed($photo, 'everyone', 'view', true);
				$auth -> setAllowed($photo, 'everyone', 'comment', true);
				$auth -> setAllowed($album, 'everyone', 'view', true);
				$auth -> setAllowed($album, 'everyone', 'comment', true);
			}

			$db -> commit();

			$this -> view -> status = true;
			$this -> view -> photo_id = $photo -> photo_id;
			$this -> view -> album_id = $album -> album_id;
			$this -> view -> src = $photo -> getPhotoUrl();
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Photo saved successfully');
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			// throw $e;
			$this -> view -> status = false;
		}
	}

}
