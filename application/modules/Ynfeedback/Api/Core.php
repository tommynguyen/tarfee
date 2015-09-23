<?php
class Ynfeedback_Api_Core extends  Core_Api_Abstract {
	
	public function sendNotificationToFollower($idea, $type, $object, $subject)
	{
		//getFollower
		$tableFollowers = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
		$follows = $tableFollowers -> getAllFollow($idea -> getIdentity());
		foreach($follows as $follow)
		{
			$user = Engine_Api::_() -> getItem('user', $follow -> user_id);
			if(!empty($user))
			{
				//send notification
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($user, $object, $subject, $type);
			}
		}
	}
	
	public function typeCreate($label) {
		$field = Engine_Api::_() -> fields() -> getField('1', 'ynfeedback_idea');
		// Create new blank option
		$option = Engine_Api::_() -> fields() -> createOption('ynfeedback_idea', $field, array('field_id' => $field -> field_id, 'label' => $label, ));
		// Get data
		$mapData = Engine_Api::_() -> fields() -> getFieldsMaps('ynfeedback_idea');
		$metaData = Engine_Api::_() -> fields() -> getFieldsMeta('ynfeedback_idea');
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynfeedback_idea');
		// Flush cache
		$mapData -> getTable() -> flushCache();
		$metaData -> getTable() -> flushCache();
		$optionData -> getTable() -> flushCache();

		return $option -> option_id;
	}
	
    public function setPhoto($photo, $params) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else
        if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $name = basename($file);
        }
        else {
            throw new Ynfeedback_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
        }

        
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        if (empty($params)) {
            $params = array(
                'parent_type' => 'user',
                'parent_id' => Engine_Api::_()->user()->getViewer() -> getIdentity()
            );
        }
        // Save
        $storage = Engine_Api::_() -> storage();
        $angle = 0;
        if(function_exists('exif_read_data'))
        {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation']))
            {
                switch($exif['Orientation'])
                {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }   
        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        @$image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(140, 105) -> write($path . '/in_' . $name) -> destroy();

        // Resize image (icon)
       $image = Engine_Image::factory();
       $image->open($file);
       
       $size = min($image->height, $image->width);
       $x = ($image->width - $size) / 2;
       $y = ($image->height - $size) / 2;

       $image->resample($x, $y, $size, $size, 48, 48)
         ->write($path.'/is_'.$name)
         ->destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path.'/is_'.$name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');
        $iMain -> bridge($iIconNormal, 'thumb.normal');
        $iMain -> bridge($iSquare, 'thumb.icon');
        
        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);
        // Update row
        return $iMain -> getIdentity();
    }

    public function setIconPhoto($photo) {
        $photo_id = $this->setPhoto($photo, array());
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->setSetting('ynfeedback_button_icon', $photo_id);
    }

    public function getPhotoLink($id, $type = null) {
        if (is_null($type)) $type = 'thumb.icon';
        if ($id) {
            $photo = Engine_Api::_()->getItemTable('storage_file')->getFile($id, $type);
            if( !$photo ) {
                return 'application/modules/Ynfeedback/externals/images/icon.png';
            }
            return $photo->map();
        }
        return 'application/modules/Ynfeedback/externals/images/icon.png';
    }
    
    public function uploadFile($file, $params) {
        // Save
        $storage = Engine_Api::_() -> storage();
        $aMain = $storage -> create($file, $params);
        // Update row
        return $aMain -> getIdentity();
    }
    
    function isMobile() {
        $session = new Zend_Session_Namespace('mobile');
        return ($session -> mobile);
    }    
}