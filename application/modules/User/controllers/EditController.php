<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_EditController extends Core_Controller_Action_User
{
	public function init()
	{
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			// Can specifiy custom id
			$id = $this -> _getParam('id', null);
			$subject = null;
			if (null === $id)
			{
				$subject = Engine_Api::_() -> user() -> getViewer();
				Engine_Api::_() -> core() -> setSubject($subject);
			}
			else
			{
				$subject = Engine_Api::_() -> getItem('user', $id);
				Engine_Api::_() -> core() -> setSubject($subject);
			}
		}

		if (!empty($id))
		{
			$params = array('id' => $id);
		}
		else
		{
			$params = array();
		}
		// Set up navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('user_edit', array('params' => $params));

		// Set up require's
		$this -> _helper -> requireUser();
		$this -> _helper -> requireSubject('user');
		$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit');
	}

	public function profileAction() {
		$this->view->user = $user = Engine_Api::_()->core()->getSubject();
    	$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    	//change profile base on level
		$table = Engine_Api::_()->getApi('core', 'fields')->getTable('user', 'values');
	    $select = $table->select();
	    $select->where('field_id = ?', 1);
	    $select->where('item_id = ?', $user -> getIdentity());
	    $value_profile = $table->fetchRow($select);
		if($value_profile)
		{
			$profile_id = Engine_Api::_() -> user() -> getProfileTypeBaseOnLevel($user->level_id);
			if($value_profile -> value != $profile_id)
			{
				$value_profile -> value = $profile_id;
				$value_profile -> save();
			}
		}
		else {
			$value_profile = $table -> createRow();
			$value_profile -> field_id = 1;
			$value_profile -> item_id = $user -> getIdentity();
			$profile_id = Engine_Api::_() -> user() -> getProfileTypeBaseOnLevel($user->level_id);
			$value_profile -> value = $profile_id;
			$value_profile -> save();
		}
	
    	// General form w/o profile type
    	$aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    	$this->view->topLevelId = $topLevelId = 0;
    	$this->view->topLevelValue = $topLevelValue = null;
    	if( isset($aliasedFields['profile_type']) ) {
      		$aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
      		$topLevelId = $aliasedFields['profile_type']->field_id;
      		$topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
      		if( !$topLevelId || !$topLevelValue ) {
        		$topLevelId = null;
        		$topLevelValue = null;
      		}
      		$this->view->topLevelId = $topLevelId;
      		$this->view->topLevelValue = $topLevelValue;
    	}
    
    	// Get form
    	$form = $this->view->form = new Fields_Form_Standard(array(
      		'item' => Engine_Api::_()->core()->getSubject(),
      		'topLevelId' => $topLevelId,
      		'topLevelValue' => $topLevelValue,
      		'hasPrivacy' => true,
      		'privacyValues' => $this->getRequest()->getParam('privacy'),
    	));
    
		$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
		$countriesAssoc = array('0'=>'') + $countriesAssoc;
	
		$provincesAssoc = array();
		$country_id = $this->_getParam('country_id', $user->country_id);
		if ($country_id) {
			$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
			$provincesAssoc = array('0'=>'') + $provincesAssoc;
		}
	
		$form->addElement('Select', 'country_id', array(
			'label' => 'Country',
			'multiOptions' => $countriesAssoc,
			'value' => $country_id
		));
	
		$citiesAssoc = array();
		$province_id = $this->_getParam('province_id', $user->province_id);
		if ($province_id) {
			$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
			$citiesAssoc = array('0'=>'') + $citiesAssoc;
		}
	
		$form->addElement('Select', 'province_id', array(
			'label' => 'Province/State',
			'multiOptions' => $provincesAssoc,
			'value' => $province_id
		));
	
		$city_id = $this->_getParam('city_id', $user->city_id);
		$form->addElement('Select', 'city_id', array(
			'label' => 'City',
			'multiOptions' => $citiesAssoc,
			'value' => $city_id
		));
		
		$continent = '';
		$country = Engine_Api::_()->getItem('user_location', $country_id);
		if ($country) $continent = $country->getContinent();
		$form->addElement('Text', 'continent', array(
			'label' => 'Continent',
			'value' => $continent,
			'disabled' => true
		));
		
    	if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      		$form->saveValues();
	
	  		$values = $this->getRequest()->getPost();
	  		$user->country_id = $values['country_id'];
	  		$user->province_id = $values['province_id'];
	  		$user->city_id = $values['city_id'];
	  
      		// Update display name
      		$aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($user);
     	 	$user->setDisplayName($aliasValues);
      		//$user->modified_date = date('Y-m-d H:i:s');
      		$user->save();

      		// update networks
      		Engine_Api::_()->network()->recalculate($user);
      
      		$form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    	}
	}

	public function photoAction()
	{
		$this -> view -> user = $user = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		// Get form
		$this -> view -> form = $form = new User_Form_Edit_Photo();

		if (empty($user -> photo_id))
		{
			$form -> removeElement('remove');
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		if(!empty($values['url']))
		{
			$filename = $this -> copyImg($this -> getImageURL($values['url']), md5($values['url']));
			if($filename)
			{
				$user -> setPhoto($filename);
				@unlink($filename);
				$iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);
		        // Insert activity
		        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
		          '{item:$subject} added a new profile photo.');
		
		        // Hooks to enable albums to work
		        if( $action ) {
		          $event = Engine_Hooks_Dispatcher::_()
		            ->callEvent('onUserProfilePhotoUpload', array(
		                'user' => $user,
		                'file' => $iMain,
		              ));
		
		          $attachment = $event->getResponse();
		          if( !$attachment ) $attachment = $iMain;
		
		          // We have to attach the user himself w/o album plugin
		          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
				}
			}
		}
		// Uploading a new photo
		if ($form -> Filedata -> getValue() !== null)
		{
			$db = $user -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$fileElement = $form -> Filedata;

				$user -> setPhoto($fileElement);

				$iMain = Engine_Api::_() -> getItem('storage_file', $user -> photo_id);

				// Insert activity
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

				// Hooks to enable albums to work
				if ($action)
				{
					$event = Engine_Hooks_Dispatcher::_() -> callEvent('onUserProfilePhotoUpload', array(
						'user' => $user,
						'file' => $iMain,
					));

					$attachment = $event -> getResponse();
					if (!$attachment)
						$attachment = $iMain;

					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
				}

				$db -> commit();
			}

			// If an exception occurred within the image adapter, it's probably an invalid
			// image
			catch( Engine_Image_Adapter_Exception $e )
			{
				$db -> rollBack();
				$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The uploaded file is not supported or is corrupt.'));
			}

			// Otherwise it's probably a problem with the database or the storage system
			// (just throw it)
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}

		// Resizing a photo
		else
		if ($form -> getValue('coordinates') !== '')
		{
			$storage = Engine_Api::_() -> storage();
			
			$iMain = $storage -> get($user -> photo_id, 'thumb.main');
			$iProfile = $storage -> get($user -> photo_id, 'thumb.profile');
			$iSquare = $storage -> get($user -> photo_id, 'thumb.icon');
			
			// Read into tmp file
			$mName = $iMain -> getStorageService() -> temporary($iMain);
			$pName = dirname($mName) . '/p_' . basename($mName);
			$iName = dirname($mName) . '/nis_' . basename($mName);

			list($x, $y, $w, $h) = explode(':', $form -> getValue('coordinates'));
			
			$image = Engine_Image::factory();
			$image -> open($mName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48) -> write($iName) -> destroy();
			$iSquare -> store($iName);
			
			$image -> open($mName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 200, 200) -> write($pName) -> destroy();
			$iProfile -> store($pName);
			
			// Remove temp files
			@unlink($pName);
			@unlink($iName);
		}
		$form -> reset();
	}

	public function removePhotoAction()
	{
		// Get form
		$this -> view -> form = $form = new User_Form_Edit_RemovePhoto();

		if (!$this -> getRequest() -> isPost() || !$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$user = Engine_Api::_() -> core() -> getSubject();
		$user -> photo_id = 0;
		$user -> save();

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your photo has been removed.');

		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your photo has been removed.'))
		));
	}

	public function styleAction()
	{
		$this -> view -> user = $user = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('user', null, 'style') -> isValid())
			return;

		// Get form
		$this -> view -> form = $form = new User_Form_Edit_Style();

		// Get current row
		$table = Engine_Api::_() -> getDbtable('styles', 'core');
		$select = $table -> select() -> where('type = ?', $user -> getType()) -> where('id = ?', $user -> getIdentity()) -> limit();

		$row = $table -> fetchRow($select);

		// Not posting, populate
		if (!$this -> getRequest() -> isPost())
		{
			$form -> populate(array('style' => (null === $row ? '' : $row -> style)));
			return;
		}

		// Whoops, form was not valid
		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Cool! Process
		$style = $form -> getValue('style');

		// Process
		$style = strip_tags($style);

		$forbiddenStuff = array(
			'-moz-binding',
			'expression',
			'javascript:',
			'behaviour:',
			'vbscript:',
			'mocha:',
			'livescript:',
		);

		$style = str_replace($forbiddenStuff, '', $style);

		// Save
		if (null == $row)
		{
			$row = $table -> createRow();
			$row -> type = $user -> getType();
			$row -> id = $user -> getIdentity();
		}

		$row -> style = $style;
		$row -> save();

		$form -> addNotice(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'));
	}

	public function externalPhotoAction()
	{
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$user = Engine_Api::_() -> core() -> getSubject();

		// Get photo
		$photo = Engine_Api::_() -> getItemByGuid($this -> _getParam('photo'));
		if (!$photo || !($photo instanceof Core_Model_Item_Abstract) || empty($photo -> photo_id))
		{
			$this -> _forward('requiresubject', 'error', 'core');
			return;
		}

		if (!$photo -> authorization() -> isAllowed(null, 'view'))
		{
			$this -> _forward('requireauth', 'error', 'core');
			return;
		}

		// Make form
		$this -> view -> form = $form = new User_Form_Edit_ExternalPhoto();
		$this -> view -> photo = $photo;

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		// Process
		$db = $user -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Get the owner of the photo
			$photoOwnerId = null;
			if (isset($photo -> user_id))
			{
				$photoOwnerId = $photo -> user_id;
			}
			else
			if (isset($photo -> owner_id) && (!isset($photo -> owner_type) || $photo -> owner_type == 'user'))
			{
				$photoOwnerId = $photo -> owner_id;
			}

			// if it is from your own profile album do not make copies of the image
			if ($photo instanceof Album_Model_Photo && ($photoParent = $photo -> getParent()) instanceof Album_Model_Album && $photoParent -> owner_id == $photoOwnerId && $photoParent -> type == 'profile')
			{

				// ensure thumb.icon and thumb.profile exist
				$newStorageFile = Engine_Api::_() -> getItem('storage_file', $photo -> file_id);
				$filesTable = Engine_Api::_() -> getDbtable('files', 'storage');
				if ($photo -> file_id == $filesTable -> lookupFile($photo -> file_id, 'thumb.profile'))
				{
					try
					{
						$tmpFile = $newStorageFile -> temporary();
						$image = Engine_Image::factory();
						$image -> open($tmpFile) -> resize(200, 400) -> write($tmpFile) -> destroy();
						$iProfile = $filesTable -> createFile($tmpFile, array(
							'parent_type' => $user -> getType(),
							'parent_id' => $user -> getIdentity(),
							'user_id' => $user -> getIdentity(),
							'name' => basename($tmpFile),
						));
						$newStorageFile -> bridge($iProfile, 'thumb.profile');
						@unlink($tmpFile);
					}
					catch( Exception $e )
					{
						echo $e;
						die();
					}
				}
				if ($photo -> file_id == $filesTable -> lookupFile($photo -> file_id, 'thumb.icon'))
				{
					try
					{
						$tmpFile = $newStorageFile -> temporary();
						$image = Engine_Image::factory();
						$image -> open($tmpFile);
						$size = min($image -> height, $image -> width);
						$x = ($image -> width - $size) / 2;
						$y = ($image -> height - $size) / 2;
						$image -> resample($x, $y, $size, $size, 48, 48) -> write($tmpFile) -> destroy();
						$iSquare = $filesTable -> createFile($tmpFile, array(
							'parent_type' => $user -> getType(),
							'parent_id' => $user -> getIdentity(),
							'user_id' => $user -> getIdentity(),
							'name' => basename($tmpFile),
						));
						$newStorageFile -> bridge($iSquare, 'thumb.icon');
						@unlink($tmpFile);
					}
					catch( Exception $e )
					{
						echo $e;
						die();
					}
				}

				// Set it
				$user -> photo_id = $photo -> file_id;
				$user -> save();

				// Insert activity
				// @todo maybe it should read "changed their profile photo" ?
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} changed their profile photo.');
				if ($action)
				{
					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $photo);
				}
			}

			// Otherwise copy to the profile album
			else
			{
				$user -> setPhoto($photo);

				// Insert activity
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

				// Hooks to enable albums to work
				$newStorageFile = Engine_Api::_() -> getItem('storage_file', $user -> photo_id);
				$event = Engine_Hooks_Dispatcher::_() -> callEvent('onUserProfilePhotoUpload', array(
					'user' => $user,
					'file' => $newStorageFile,
				));

				$attachment = $event -> getResponse();
				if (!$attachment)
				{
					$attachment = $newStorageFile;
				}

				if ($action)
				{
					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
				}
			}

			$db -> commit();
		}

		// Otherwise it's probably a problem with the database or the storage system
		// (just throw it)
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Set as profile photo')),
			'smoothboxClose' => true,
		));
	}

	public function clearStatusAction()
	{
		$this -> view -> status = false;

		if ($this -> getRequest() -> isPost())
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$viewer -> status = '';
			$viewer -> status_date = '00-00-0000';
			$viewer -> save();

			$this -> view -> status = true;
		}
	}

	// MinhNC add Cover photo
	public function coverAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$user = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> user = $user;
		$this -> view -> form = $form = new User_Form_Edit_Cover();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$values = $form -> getValues();
		// Set photo
		if (!empty($values['photo']))
		{
			$user -> setCoverPhoto($form -> photo);
			$user -> cover_top = 0;
			$user -> save();
		}

		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Upload new cover photo successful!')),
			'format' => 'smoothbox',
			'smoothboxClose' => true,
			'parentRefresh' => true,
		));
	}

	public function repositionAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$this -> view -> user = $user = Engine_Api::_() -> core() -> getSubject();
		$position = $this -> _getParam('position', null);
		if (is_null($position))
		{
			echo Zend_Json::encode(array(
				'status' => false,
				'message' => Zend_Registry::get('Zend_Translate') -> _('The request is invalid.')
			));
		}

		$user -> cover_top = $position;
		$user -> save();
		echo Zend_Json::encode(array('status' => true));
	}

	public function photoPopupAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> user = $user = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		// Get form
		$this -> view -> form = $form = new User_Form_Edit_PhotoPopup();

		if (empty($user -> photo_id))
		{
			$form -> removeElement('remove');
		}

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();
		$isClose = true;
		if(!empty($values['url']))
		{
			$isClose = false;
			$filename = $this -> copyImg($this -> getImageURL($values['url']), md5($values['url']));
			if($filename)
			{
				$user -> setPhoto($filename);
				@unlink($filename);
				$iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);
		        // Insert activity
		        $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update',
		          '{item:$subject} added a new profile photo.');
		
		        // Hooks to enable albums to work
		        if( $action ) {
		          $event = Engine_Hooks_Dispatcher::_()
		            ->callEvent('onUserProfilePhotoUpload', array(
		                'user' => $user,
		                'file' => $iMain,
		              ));
		
		          $attachment = $event->getResponse();
		          if( !$attachment ) $attachment = $iMain;
		
		          // We have to attach the user himself w/o album plugin
		          Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
				}
			}
		}
		
		// Uploading a new photo
		if ($form -> Filedata -> getValue() !== null)
		{
			$isClose = false;
			$db = $user -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$fileElement = $form -> Filedata;

				$user -> setPhoto($fileElement);

				$iMain = Engine_Api::_() -> getItem('storage_file', $user -> photo_id);

				// Insert activity
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

				// Hooks to enable albums to work
				if ($action)
				{
					$event = Engine_Hooks_Dispatcher::_() -> callEvent('onUserProfilePhotoUpload', array(
						'user' => $user,
						'file' => $iMain,
					));

					$attachment = $event -> getResponse();
					if (!$attachment)
						$attachment = $iMain;

					// We have to attach the user himself w/o album plugin
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $attachment);
				}

				$db -> commit();
			}

			// If an exception occurred within the image adapter, it's probably an invalid
			// image
			catch( Engine_Image_Adapter_Exception $e )
			{
				$db -> rollBack();
				$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The uploaded file is not supported or is corrupt.'));
			}

			// Otherwise it's probably a problem with the database or the storage system
			// (just throw it)
			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}

		// Resizing a photo
		else
		if ($form -> getValue('coordinates') !== '')
		{
			$storage = Engine_Api::_() -> storage();
			
			$iMain = $storage -> get($user -> photo_id, 'thumb.main');
			$iProfile = $storage -> get($user -> photo_id, 'thumb.profile');
			$iSquare = $storage -> get($user -> photo_id, 'thumb.icon');
			
			// Read into tmp file
			$mName = $iMain -> getStorageService() -> temporary($iMain);
			$pName = dirname($mName) . '/p_' . basename($mName);
			$iName = dirname($mName) . '/nis_' . basename($mName);

			list($x, $y, $w, $h) = explode(':', $form -> getValue('coordinates'));
			
			$image = Engine_Image::factory();
			$image -> open($mName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 48, 48) -> write($iName) -> destroy();
			$iSquare -> store($iName);
			
			$image -> open($mName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 200, 200) -> write($pName) -> destroy();
			$iProfile -> store($pName);
			
			// Remove temp files
			@unlink($pName);
			@unlink($iName);
		}
		$form -> reset();
		if($isClose)
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Closed!')),
				'format' => 'smoothbox',
				'smoothboxClose' => true,
				'parentRefresh' => true,
			));
		}
	}
	public function getImageURL($url) 
	{
		if (strpos($url, '-/h') > 0) {
			$type = substr($url, strrpos($url, '.'));
			$image_url = substr($url, strpos($url, '-/h') + 2, strrpos($url, '.') - (strpos($url, '-/h') + 2)) . $type;
			$image_url = str_replace("%3A", ":", $image_url);
			return $image_url;
		} else {
			return $url;
		}
	}
  	public function copyImg($url, $name) {
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$check_allow_url_fopen = ini_get('allow_url_fopen');
		if (($check_allow_url_fopen == 'on') || ($check_allow_url_fopen == 'On') || ($check_allow_url_fopen == '1')) {
			$gis = getimagesize($url);
			if(!$gis)
			{
				return false;
			}
			$type = $gis[2];
			switch($type) {
				case "1" :
					$imorig = imagecreatefromgif($url);
					break;
				case "2" :
					$imorig = imagecreatefromjpeg($url);
					break;
				case "3" :
					$imorig = imagecreatefrompng($url);
					break;
				default :
					$imorig = imagecreatefromjpeg($url);
			}
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			$imorig = imagecreatefromstring($data);
			if(!$imorig)
			{
				return false;
			}
		}

		// Save
		$filename = $path . DIRECTORY_SEPARATOR . $name . '.png';
		$im = imagecreatetruecolor(720, 720);
		$x = imagesx($imorig);
		$y = imagesy($imorig);
		if (imagecopyresampled($im, $imorig, 0, 0, 0, 0, 720, 720, $x, $y)) 
		{
			imagejpeg($im, $filename);
		}
		return $filename; 
	}

}
