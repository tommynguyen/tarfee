<?php
class Ynsocialads_PhotoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		$this -> view -> tab = $this->_getParam('tab', null);
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			if (0 !== ($photo_id = (int)$this -> _getParam('photo_id')) && null !== ($photo = Engine_Api::_() -> getItem('ynsocialads_photo', $photo_id)))
			{
				Engine_Api::_() -> core() -> setSubject($photo);
			}

			elseif (0 !== ($ad_id = (int)$this -> _getParam('ad_id')) && null !== ($ad = Engine_Api::_() -> getItem('ynsocialads_ad', $ad_id)))
			{
				Engine_Api::_() -> core() -> setSubject($ad);
			}
		}
		
		if (!Engine_Api::_() -> core() -> hasSubject())
			return $this -> _helper -> requireAuth -> forward();		
	}

	
	public function deleteAction()
	{
		$photo = Engine_Api::_() -> core() -> getSubject();

		$this -> view -> form = $form = new Ynsocialads_Form_Photo_Delete();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynsocialads') -> getAdapter();
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
			'parentRefresh' => true,
			'closeSmoothbox' => true,
		));
	}
	
	public function uploadAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> ad = $ad = Engine_Api::_() -> core() -> getSubject();
		
		$this -> view -> form = $form = new Ynsocialads_Form_Photo_Upload();
		$form -> ad_id -> setValue($ad -> getIdentity());

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		$this->_redirect('socialads/ads/view/id/'.$ad->ad_id);
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

		$ad = Engine_Api::_() -> getItem('ynsocialads_ad', $_POST['ad_id']);


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

		$db = Engine_Api::_() -> getDbtable('photos', 'ynsocialads') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();

			$params = array(
				// We can set them now since only one album is allowed
				'ad_id' => $ad -> getIdentity(),
				'user_id' => $viewer -> getIdentity(),
				'collection_id' => $ad -> getIdentity(),
			);

			$photoTable = Engine_Api::_() -> getItemTable('ynsocialads_photo');
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
			$error = $e -> getMessage();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array(
						'status' => $status,
						'error' => $error,
						'name' => $name
					)))));
		}
	}


	public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('ynsocialads_photo', $this -> getRequest() -> getParam('photo_id'));
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'ynsocialads') -> getAdapter();
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
}
