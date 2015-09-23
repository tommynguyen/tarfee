<?php

class User_Model_Library extends Core_Model_Item_Abstract
{
	protected $_owner_type = 'user';

	public function getSubLibrary()
	{
		if ($this -> level == 0)
		{
			$tableLibrary = Engine_Api::_() -> getItemTable('user_library');
			$select = $tableLibrary -> select() -> where('parent_id = ?', $this -> getIdentity());
			return $tableLibrary -> fetchAll($select);
		}
		else
		{
			return false;
		}

	}

	function isViewable()
	{
		return $this -> authorization() -> isAllowed(null, 'view');
	}

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
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'user_library',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iMain -> bridge($iProfile, 'thumb.profile');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}
	
	public function getTotalVideo()
	{
		$params['owner_type'] = $this -> getType();
		$params['owner_id'] = $this -> getIdentity();
		$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
		return $mappingTable -> getTotalVideo($params);
	}
	
	public function getVideos()
	{
		$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
	    $videoTable = Engine_Api::_()->getItemTable('video');
	    $params['owner_type'] = $this -> getType();
		$params['owner_id'] = $this -> getIdentity();
	    return $videoTable -> fetchAll($mappingTable -> getVideosSelect($params));
	}
	
	public function getTotalVideoView()
	{
		$params['owner_type'] = $this -> getType();
		$params['owner_id'] = $this -> getIdentity();
		$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
		return (int)$mappingTable -> getTotalVideoView($params);
	}
	
	public function getTotalVideoComment()
	{
		$params['owner_type'] = $this -> getType();
		$params['owner_id'] = $this -> getIdentity();
		$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
		return (int)$mappingTable -> getTotalVideoComment($params);
	}

}
