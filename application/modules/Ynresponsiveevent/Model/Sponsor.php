<?php
class Ynresponsiveevent_Model_Sponsor extends Core_Model_Item_Abstract
{
	protected $_type = 'ynresponsiveevent_sponsor';

	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'ynresponsive1_sponsor'
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if (function_exists('exif_read_data')) 
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
		$image -> open($file) ;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();
		
		// Resize image (normal)
	    $image = Engine_Image::factory();
	    $image->open($file)
	      ->resize(140, 160)
	      ->write($path.'/in_'.$name)
	      ->destroy();
		
		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iIconNormal = $storage->create($path.'/in_'.$name, $params);
		
    	$iMain->bridge($iIconNormal, 'thumb.normal');
		
		// Remove temp files
		@unlink($path . '/m_' . $name);
		@unlink($path.'/in_'.$name);

		// Update row
		$this -> photo_id = $iMain -> file_id;
		$this -> save();
		return $this;
	}

	public function getPhotoUrl($type = null)
	{
		$imgUrl = parent::getPhotoUrl($type);
		if($imgUrl)
		{
			return $imgUrl;			
		}
		$type = ( $type ? str_replace('.', '_', $type) : 'thumb_main' );
		$view = Zend_Registry::get("Zend_View");
		return $view->layout()->staticBaseUrl . "application/modules/Ynresponsiveevent/externals/images/nophoto_event_$type.png";
	}
}
