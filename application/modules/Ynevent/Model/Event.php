<?php

class Ynevent_Model_Event extends Core_Model_Item_Abstract
{

	protected $_type = 'event';
	protected $_owner_type = 'user';

	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	public function membership()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('membership', 'ynevent'));
	}

	public function _postInsert()
	{
		parent::_postInsert();
		// Create auth stuff
		$context = Engine_Api::_() -> authorization() -> context;
		$context -> setAllowed($this, 'everyone', 'view', true);
		$context -> setAllowed($this, 'registered', 'comment', true);
		$viewer = Engine_Api::_() -> user() -> getViewer();
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
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'event'
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

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image-> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Resize image (feature)
		$image = Engine_Image::factory();
		@$image -> open($file) ;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(242, 150) -> write($path . '/fe_' . $name) -> destroy();

		// Resize image (normal)
		$image = Engine_Image::factory();
		$image -> open($file);
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(140, 160) -> write($path . '/in_' . $name) -> destroy();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file);

		$size = min($image -> height, $image -> width);
		$x = ($image -> width - $size) / 2;
		$y = ($image -> height - $size) / 2;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resample($x, $y, $size, $size, 48, 48) -> write($path . '/is_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iIconNormal = $storage -> create($path . '/in_' . $name, $params);
		$iFeature = $storage -> create($path . '/fe_' . $name, $params);
		$iSquare = $storage -> create($path . '/is_' . $name, $params);

		$iMain -> bridge($iProfile, 'thumb.profile');
		$iMain -> bridge($iIconNormal, 'thumb.normal');
		$iMain -> bridge($iFeature, 'thumb.feature');
		$iMain -> bridge($iSquare, 'thumb.icon');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);
		@unlink($path . '/in_' . $name);
		@unlink($path . '/fe_' . $name);
		@unlink($path . '/is_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		// Add to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photoTable = Engine_Api::_() -> getItemTable('event_photo');
		$eventAlbum = $this -> getSingletonAlbum();
		$photoItem = $photoTable -> createRow();
		$photoItem -> setFromArray(array(
			'event_id' => $this -> getIdentity(),
			'album_id' => $eventAlbum -> getIdentity(),
			'user_id' => $viewer -> getIdentity(),
			'file_id' => $iMain -> getIdentity(),
			'collection_id' => $eventAlbum -> getIdentity(),
			'user_id' => $viewer -> getIdentity(),
		));
		$photoItem -> save();

		return $this;
	}

	public function setCoverPhoto($photo)
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
			'parent_type' => 'event'
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

		$iMain = $storage -> create($path . '/m_' . $name, $params);
		
		// Remove temp files
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> cover_photo = $iMain -> file_id;
		
		$this -> save();

		return $this;
	}

	public function getDescription()
	{
		// @todo decide how we want to handle multibyte string functions
		$tmpBody = strip_tags($this -> description);
		return (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody);
	}

	/**
	 * Gets an absolute URL to the page to view this item
	 *
	 * @return string
	 */
	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'event_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $this->getSlug(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	protected function _delete()
	{
		if ($this -> _disableHooks)
			return;

		// Delete all memberships
		$this -> membership() -> removeAllMembers();

		// Delete all albums
		$albumTable = Engine_Api::_() -> getItemTable('event_album');
		$albumSelect = $albumTable -> select() -> where('event_id = ?', $this -> getIdentity());
		foreach ($albumTable->fetchAll($albumSelect) as $eventAlbum)
		{
			$eventAlbum -> delete();
		}

		// Delete all topics
		$topicTable = Engine_Api::_() -> getItemTable('event_topic');
		$topicSelect = $topicTable -> select() -> where('event_id = ?', $this -> getIdentity());
		foreach ($topicTable->fetchAll($topicSelect) as $eventTopic)
		{
			$eventTopic -> delete();
		}

		parent::_delete();
	}

	public function getSingletonAlbum()
	{
		$table = Engine_Api::_() -> getItemTable('event_album');

		$select = $table -> select() -> where('event_id = ?', $this -> getIdentity()) -> order('album_id ASC') -> limit(1);

		$album = $table -> fetchRow($select);

		if (null === $album)
		{
			$album = $table -> createRow();
			$album -> setFromArray(array('event_id' => $this -> getIdentity()));
			$album -> save();
		}

		return $album;
	}

	/*
	 public function getSingletonAlbum() {
	 $table = Engine_Api::_()->getItemTable('event_album');

	 $select = $table->select()
	 ->where('event_id = ?', $this->getIdentity())
	 ->order('album_id ASC')
	 ->limit(1);

	 $album = $table->fetchRow($select);

	 if (null === $album) {
	 $album = $table->createRow();
	 $album->setFromArray(array(
	 'event_id' => $this->getIdentity()
	 ));
	 $album->save();
	 }

	 return $album;
	 }
	 */
	public function categoryName()
	{
		$categoryTable = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		return $categoryTable -> select() -> from($categoryTable, 'title') -> where('category_id = ?', $this -> category_id) -> limit(1) -> query() -> fetchColumn();
	}

	public function getAttendingCount()
	{
		return $this -> membership() -> getMemberCount(true, Array('rsvp' => 2));
	}

	public function getMaybeCount()
	{
		return $this -> membership() -> getMemberCount(true, Array('rsvp' => 1));
	}

	public function getNotAttendingCount()
	{
		return $this -> membership() -> getMemberCount(true, Array('rsvp' => 0));
	}

	public function getAwaitingReplyCount()
	{
		return $this -> membership() -> getMemberCount(false, Array('rsvp' => 3));
	}

	public function getNextEvent()
	{
		$table = Engine_Api::_() -> getItemTable('event');
		$eventTableName = $table -> info('name');
		$select = $table -> select();
		$select -> setIntegrityCheck(false) -> from("$eventTableName", array("$eventTableName.*")) -> where("$eventTableName.repeat_group = ?", $this -> repeat_group) -> where("$eventTableName.repeat_order > ?", $this -> repeat_order) -> order("$eventTableName.repeat_order") -> limit(1);
		return $table -> fetchRow($select);
	}
	
	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
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
		return $view->layout()->staticBaseUrl . "application/modules/Ynevent/externals/images/nophoto_event_$type.png";
			
	}
	public function getFullAddress()
	{
		$st_address = "";
		if ($this -> address != '')
			$st_address .= $this -> address;

		if ($this -> city != '')
			$st_address .= ", " . $this -> city;

		if ($this -> country != '')
			$st_address .= ", " . $this -> country;

		if ($this -> zip_code != '')
			$st_address .= ", " . $this -> zip_code;

		$pos = strpos($st_address, ",");
		if ($pos === 0)
			$st_address = substr($st_address, 1);
		return $st_address;
	}
	
	 public function getVideosPaginator($params = array(), $order_by = true) {
        $paginator = Zend_Paginator::factory($this->getVideosSelect($params, $order_by));
        if (!empty($params['page'])) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if (!empty($params['limit'])) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }

    public function getVideosSelect($params = array(), $order_by = true) {
        $table = Engine_Api::_()->getItemTable('video');
        $rName = $table->info('name');

        $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $tmName = $tmTable->info('name');

        $select = $table->select()->from($table->info('name'))->setIntegrityCheck(false);
		
		if (!empty($params['ids']) && count($params['ids']) > 0) {
            $select->where('video_id IN (?)', $params['ids']);
        }
		else {
			$select->where('video_id = 0');
		}
		
        if (!empty($params['orderby'])) {
            if (isset($params['order'])) {
                $order = $params['order'];
            } else {
                $order = '';
            }
            switch ($params['orderby']) {
                case 'most_liked' :
                    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
                    $likeTableName = $likeTable->info('name');
                    $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
                    $select->joinLeft($likeVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.like_id) DESC");
                    break;
                case 'most_commented' :
                    $commentTable = Engine_Api::_()->getDbTable('comments', 'core');
                    $commentTableName = $commentTable->info('name');
                    $commentVideoTableSelect = $commentTable->select()->where('resource_type = ?', 'video');
                    $select->join($commentVideoTableSelect, "t.resource_id = $rName.video_id");
                    $select->group("$rName.video_id");
                    $select->order("count(t.comment_id) DESC");
                    break;
                case 'featured' :
                    $select->where('featured = ?', 1);
                    $select->order("$rName.creation_date DESC");
                    break;
                default :
                    $select->order("$rName.{$params['orderby']} DESC");
            }
        } else {
            if ($order_by) {
                $select->order("$rName.creation_date DESC");
            }
        }

        if (!empty($params['text'])) {
            $searchTable = Engine_Api::_()->getDbtable('search', 'core');
            $db = $searchTable->getAdapter();
            $sName = $searchTable->info('name');
            $select
                ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
                ->where($sName . '.type = ?', 'video')
                ->where($sName . '.title LIKE ?', "%{$params['text']}%")
            //->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
            //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
            ;
        }

        if (!empty($params['title'])) {
            $select->where("$rName.title LIKE ?", "%{$params['title']}%");
        }

        if (!empty($params['status']) && is_numeric($params['status'])) {
            $select->where($rName . '.status = ?', $params['status']);
        }
        if (!empty($params['search']) && is_numeric($params['search'])) {
            $select->where($rName . '.search = ?', $params['search']);
        }
        if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
            $select->where($rName . '.owner_id = ?', $params['user_id']);
        }

        if (!empty($params['user']) && $params['user'] instanceof User_Model_User) {
            $select->where($rName . '.owner_id = ?', $params['user_id']->getIdentity());
        }

        if (array_key_exists('category', $params) && is_numeric($params['category'])) {
            if ($params['category'] != 0) {
                $select->where("$rName .category_id = {$params['category']} OR  $rName.subcategory_id = {$params['category']}");
            } else {
                $select->where("$rName .category_id = {$params['category']}");
            }
        }

        if (!empty($params['tag'])) {
            $select->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
                ->where($tmName . '.resource_type = ?', 'video')
                ->where($tmName . '.tag_id = ?', $params['tag']);
        }

        if (!empty($params['videoIds']) && is_array($params['videoIds']) && count($params['videoIds']) > 0) {
            $select->where('video_id in (?)', $params['videoIds']);
        }

        if (isset($params['type']) && is_numeric($params['type'])) {
            $select->where('type = ?', $params['type']);
        }

        if (isset($params['featured']) && is_numeric($params['featured'])) {
            $select->where('featured = ?', $params['featured']);
        }

        //Owner in Admin Search
        if (!empty($params['owner'])) {
            $key = stripslashes($params['owner']);
            $select->setIntegrityCheck(false)
                ->join('engine4_users as u1', "u1.user_id = $rName.owner_id", '')
                ->where("u1.displayname LIKE ?", "%$key%");
        }

        if (!empty($params['fieldOrder'])) {
            if ($params['fieldOrder'] == 'owner') {
                $select->setIntegrityCheck(false)
                    ->join('engine4_users as u2', "u2.user_id = $rName.owner_id", '')
                    ->order("u2.displayname {$params['order']}");
            } else {
                $select->order("{$params['fieldOrder']} {$params['order']}");
            }
        }

        return $select;
    }

	public function getSportId() {
		return $this->category_id;
	}
}
