<?php

class User_PhotoController extends Core_Controller_Action_Standard
{
	public function uploadAction()
	{

		// Render
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Get form
		
		$item_type = $this ->_getParam('type');
		$item_id = $this ->_getParam('id');
		$item = Engine_Api::_() -> getItem($item_type, $item_id);
		if(!$item) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		if(!$item -> getOwner() -> isSelf($viewer)) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$this -> view -> form = $form = new User_Form_Photo_Upload();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		
		$values = $form -> getValues();
		
		$photoTable = Engine_Api::_() -> getItemTable('user_photo');
		$db = $photoTable -> getAdapter();
		$db -> beginTransaction();
		
		try
		{
			$file_ids = explode(' ', trim($values['html5uploadfileids']));
			foreach($file_ids as $file_id)
			{
				$params = array(
					'user_id' => $viewer -> getIdentity(),
					'item_id' => $item_id,
					'item_type' => $item_type,
					'file_id' => $file_id
				);
				$photo = $photoTable -> createRow();
				$photo -> setFromArray($params);
				$photo -> save();
			}
			$db -> commit();
		}
		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		$this -> _forward('success', 'utility', 'core', array(
				'closeSmoothbox' => true,
				'parentRefresh' => true,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _($this -> view -> translate('Add Photos Success...')))
		));
	}

	public function uploadPhotoAction()
	{
		$this -> _helper -> layout() -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);

		if (!$this -> _helper -> requireUser() -> checkRequire())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (!$this -> getRequest() -> isPost())
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error)))));
		}

		if (empty($_FILES['files']))
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $error)))));
		}
		$name = $_FILES['files']['name'][0];
		$type = explode('/', $_FILES['files']['type'][0]);
		if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image')
		{
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

		$db = Engine_Api::_() -> getDbtable('photos', 'user') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$params = array(
				'owner_type' => 'user',
				'owner_id' => $viewer -> getIdentity()
			);
			$temp_file = array(
						'type' => $_FILES['files']['type'][0],
						'tmp_name' => $_FILES['files']['tmp_name'][0],
						'name' => $_FILES['files']['name'][0]
					);
					
			$song_path = pathinfo($temp_file['name']);
		    $params    = array_merge(array(
		      'type'        => 'song',
		      'name'        => $temp_file['name'],
		      'parent_type' => 'user_photo',
		      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
		      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
		      'extension'   => substr($file['name'], strrpos($file['name'], '.')+1),
		    ), $params);
		    $photo = Engine_Api::_()->storage()->create($temp_file, $params);
			$photo_id = $photo  -> getIdentity();

			$status = true;
			$name = $_FILES['files']['name'][0];
			$photo_id = $photo_id;
			$db -> commit();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name'=> $name, 'photo_id' => $photo_id)))));
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			$status = false;
			$name = $_FILES['files']['name'][0];
			$error = $e-> getMessage();
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error'=> $error, 'name' => $name)))));
		}

	}
}
