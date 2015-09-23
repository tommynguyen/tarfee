<?php

class Ynevent_PhotoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('event_photo', $photo_id)))
			{
				Engine_Api::_() -> core() -> setSubject($photo);
			}

			elseif (0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('event', $event_id)))
			{
				Engine_Api::_() -> core() -> setSubject($event);
			}
		}

		$this -> _helper -> requireUser -> addActionRequires(array(
			'upload',
			'upload-photo', // Not sure if this is the right
			'edit',
		));

		$this -> _helper -> requireSubject -> setActionRequireTypes(array(
			'list' => 'event',
			'upload' => 'event',
			'view' => 'event_photo',
			'edit' => 'event_photo',
		));
	}

	public function listAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $event -> getSingletonAlbum();

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'view') -> isValid())
		{
			return;
		}

		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		$this -> view -> canUpload = $event -> authorization() -> isAllowed(null, 'photo');
	}

	public function viewAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> photo = $photo = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> album = $album = $photo -> getCollection();
		$this -> view -> event = $event = $photo -> getEvent();
		$this -> view -> canEdit = $photo -> authorization() -> isAllowed(null, 'edit');

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'view') -> isValid())
		{
			return;
		}

		if (!$viewer || !$viewer -> getIdentity() || $photo -> user_id != $viewer -> getIdentity())
		{
			$photo -> view_count = new Zend_Db_Expr('view_count + 1');
			$photo -> save();
		}
	}

	public function uploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$album = $event -> getSingletonAlbum();

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'photo') -> isValid())
		{
			return;
		}

		$this -> view -> form = $form = new Ynevent_Form_Photo_Upload();
		$session = new Zend_Session_Namespace('mobile');
		if (!$session -> mobile)
		{
			$form -> event_id -> setValue($event -> getIdentity());
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$table = Engine_Api::_() -> getItemTable('event_photo');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$params = array(
				'event_id' => $event -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'collection_id' => $album -> getIdentity(),
				'album_id' => $album -> getIdentity()
			);
			// mobile upload photos
			$arr_photo_id = array();
			if ($session -> mobile && !empty($_FILES['photos']))
			{
				$files = $_FILES['photos'];
				if(!$files['name'][0])
				{
					$form -> addError($this -> view -> translate("Please choose a photo to upload!"));
					return;
				}
				foreach ($files['name'] as $key => $value)
				{
					$type = explode('/', $files['type'][$key]);
					if ($type[0] != 'image' || !is_uploaded_file($files['tmp_name'][$key]))
					{
						continue;
					}
					try
					{
						$temp_file = array(
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'name' => $files['name'][$key]
						);
						$photoTable = Engine_Api::_() -> getItemTable('event_photo');
						$photo = $photoTable -> createRow();
						$photo -> setFromArray($params);
						$photo -> save();

						$photo -> setPhoto($temp_file);

						$arr_photo_id[] = $photo -> getIdentity();
					}

					catch ( Exception $e )
					{
						throw $e;
						return;
					}
				}
			}
			else
			{
				$values = $form -> getValues();
				$arr_photo_id = explode(' ', trim($values['html5uploadfileids']));
			}
			$values = $form -> getValues();

			if ($arr_photo_id)
			{
				$values['file'] = $arr_photo_id;
			}
			// Add action and attachments
			$api = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $event, 'ynevent_photo_upload', null, array('count' => count($values['file'])));

			// Do other stuff
			$count = 0;
			foreach ($values['file'] as $photo_id)
			{
				$photo = Engine_Api::_() -> getItem("event_photo", $photo_id);
				if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
					continue;

				$photo -> collection_id = $album -> album_id;
				$photo -> album_id = $album -> album_id;
				$photo -> save();

				if ($action instanceof Activity_Model_Action && $count < 8)
				{
					$api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
				}
				$count++;
			}

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if($this -> _getParam('tab', null) && !$session -> mobile)
		{
			return $this->_helper->redirector->gotoRoute(array('id' => $event -> getIdentity(), 'tab' => $this -> _getParam('tab', null)), 'event_profile', true);
		}
		else
			$this -> _redirectCustom($event);
	}

	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error
					)))));
		}

		$event = Engine_Api::_() -> getItem('event', $_POST['event_id']);

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'photo') -> isValid())
		{
			return;
		}
		// @todo check auth
		//$event

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $error
					)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$album = $event -> getSingletonAlbum();

			$params = array(
				// We can set them now since only one album is allowed
				'collection_id' => $album -> getIdentity(),
				'album_id' => $album -> getIdentity(),

				'event_id' => $event -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
			);

			$photoTable = Engine_Api::_() -> getItemTable('event_photo');
			$photo = $photoTable -> createRow();
			$photo -> setFromArray($params);
			$photo -> save();

			$temp_file = array(
				'type' => $_FILES['files']['type'][0],
				'tmp_name' => $_FILES['files']['tmp_name'][0],
				'name' => $_FILES['files']['name'][0]
			);

			$photo -> setPhoto($temp_file);
			$db -> commit();

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo -> photo_id;
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'name' => $name,
						'photo_id' => $photo_id
					)))));

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = Zend_Registry::get('Zend_Translate') -> _('An error occurred.');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
	}

	public function editAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();

		if (!$this -> _helper -> requireAuth() -> setAuthParams($photo, null, 'edit') -> isValid())
		{
			return;
		}

		$this -> view -> form = $form = new Ynevent_Form_Photo_Edit();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> setFromArray($form -> getValues()) -> save();

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Changes saved')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
			'closeSmoothbox' => true,
		));
	}

	public function deleteAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		$event = $photo -> getParent('event');

		if (!$this -> _helper -> requireAuth() -> setAuthParams($photo, null, 'edit') -> isValid())
		{
			return;
		}

		$this -> view -> form = $form = new Ynevent_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate($photo -> toArray());
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();

			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Photo deleted')),
			'layout' => 'default-simple',
			'parentRedirect' => $event -> getHref(),
			'closeSmoothbox' => true,
		));
	}

	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynevent_photo', $this -> getRequest() -> getParam('photo_id'));
		if (!$this -> _helper -> requireAuth() -> setAuthParams($photo, null, 'edit') -> isValid())
		{
			return;
		}
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function setSlideshowAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();
		
		$photo->is_featured = !$photo->is_featured;
		$photo->save();
		
		$arr =  array('photo_id' => $photo->getIdentity(), 'status' => $photo->is_featured);
		
		echo Zend_Json::encode($arr);
		exit();
	}
}
