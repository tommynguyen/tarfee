<?php
class Advgroup_Model_Group extends Core_Model_Item_Abstract
{
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	protected $_type = 'group';
	
	
	public function sendEmailToFollowers($recipients, $message) 
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        // Check recipients
        if( is_string($recipients) ) 
        {
			$recipients = preg_split("/[\s,]+/", $recipients);
        }
        if( is_array($recipients) ) 
        {
			$recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
        }
        if( !is_array($recipients) || empty($recipients) ) 
        {
			return 0;
        }
    
        // Check message
        $message = trim($message);
        $sentEmails = 0;
        $photo_url = ($this->getPhotoUrl('thumb.profile')) ? $this->getPhotoUrl('thumb.profile') : 'application/modules/Advgroup/externals/images/nophoto_group_thumb_profile.png';
        foreach( $recipients as $recipient ) 
        {
            $mailType = 'advgroup_email_to_friends';
            $mailParams = array(
              'host' => $_SERVER['HTTP_HOST'],
              'email' => $recipient,
              'date' => time(),
              'sender_email' => $viewer->email,
              'sender_title' => $viewer->getTitle(),
              'sender_link' => $viewer->getHref(),
              'sender_photo' => $viewer->getPhotoUrl('thumb.icon'),
              'message' => $message,
              'object_link' => $this->getHref(),
              'object_title' => $this->title,
              'object_photo' => $photo_url,
              'object_description' => $this->description, 
            );
            
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
              $recipient,
              $mailType,
              $mailParams
            );
            $sentEmails++;
        }
        return $sentEmails;
    }
	
	public function getSlug($str = NULL, $maxstrlen = 64)
	{
		$str = $this -> getTitle();
		if (strlen($str) > 32)
		{
			$str = Engine_String::substr($str, 0, 32) . '...';
		}
		$str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
		$str = strtolower($str);
		$str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
		$str = preg_replace('/-+/', '-', $str);
		$str = trim($str, '-');
		if (!$str)
		{
			$str = '-';
		}
		return $str;
	}

	/**
	 * Gets an absolute URL to the page to view this item
	 *
	 * @return string
	 */

	public function getHref($params = array())
	{
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'group_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getDescription()
	{
		// @todo decide how we want to handle multibyte string functions
		if (isset($this -> description))
		{
			$tmpBody = strip_tags($this -> description);
			return Engine_Api::_() -> advgroup() -> subPhrase($tmpBody, 350);
		}
		return '';
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
		return $view->layout()->staticBaseUrl . "application/modules/Advgroup/externals/images/nophoto_group_$type.png";
			
	}
	
	public function isParentGroupOwner(Core_Model_Item_Abstract $owner)
	{
		if (!$owner -> getIdentity())
		{
			return false;
		}

		$parent_group = $this -> getParentGroup();

		if ($parent_group && $parent_group -> isOwner($owner))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
   public function canManageUser($user)
   {
    return $this->authorization()->isAllowed($user, 'member.edit')
    || $this->authorization()->isAllowed($user, 'edit')
    || $this->isOwner($user) || $this->isParentGroupOwner($user) ;
   }

	public function getParent($recurseType = null)
	{
		return $this -> getOwner('user');
	}

	public function countSubGroups()
	{
		$table = Engine_Api::_() -> getItemtable('group');
		$select = $table -> select() -> where('parent_id = ?', $this -> group_id);
		return count($table -> fetchAll($select));
	}

	public function getParentGroup()
	{
		return Engine_Api::_() -> getItem('group', $this -> parent_id);
	}

	public function getAllSubGroups()
	{
		$table = Engine_Api::_() -> getItemTable('group');
		$select = $table -> select() -> where('parent_id = ?', $this -> group_id);
		return $sub_groups = $table -> fetchAll($select);
	}

	public function getAllGroupsAssoc()
	{
		$result = array($this -> group_id);
		$table = Engine_Api::_() -> getItemTable('group');
		$select = $table -> select() -> where('parent_id = ?', $this -> group_id);
		$sub_groups = $table -> fetchAll($select);
		foreach ($sub_groups as $sub_group)
		{
			$result[] = $sub_group -> group_id;
		}
		return $result;
	}

	public function getSingletonAlbum()
	{
		$table = Engine_Api::_() -> getItemTable('advgroup_album');
		$select = $table -> select() -> where('group_id = ?', $this -> getIdentity()) -> order('album_id ASC')->limit(1);

		$album = $table -> fetchRow($select);

		if (null === $album)
		{
			$album = $table -> createRow();
			$album -> setFromArray(array(
				'group_id' => $this -> getIdentity(),
				'title' => 'Group Profile',
				'user_id' => $this -> getOwner() -> getIdentity(),
			));
			$album -> save();
		}

		return $album;
	}
	
	public function getGroupPhoto()
	{
		$table = Engine_Api::_() -> getItemTable('advgroup_photo');
		$select = $table -> select() -> where('group_id = ?', $this -> getIdentity()) -> order('album_id ASC')
		->where('is_featured = 1')
		;

		return $photo = $table -> fetchAll($select);

		
	}

	public function getOfficerList()
	{
		$table = Engine_Api::_() -> getItemTable('advgroup_list');
		$select = $table -> select() -> where('owner_id = ?', $this -> getIdentity()) -> where('title = ?', 'GROUP_OFFICERS') -> limit(1);

		$list = $table -> fetchRow($select);

		if (null === $list)
		{
			$list = $table -> createRow();
			$list -> setFromArray(array(
				'owner_id' => $this -> getIdentity(),
				'title' => 'GROUP_OFFICERS',
			));
			$list -> save();
		}

		return $list;
	}
	
	public function isOfficer()
	{
		$list = $this->getOfficerList();
	    $viewer = Engine_Api::_() -> user() -> getViewer();
		if( !empty($viewer->resource_id) ) {
	        $memberInfo = $viewer;
	        $member = $this->item('user', $memberInfo->user_id);
	    } else {
	    $memberInfo = $this->membership()->getMemberInfo($viewer);
	    }
        $listItem_checkviewer = $list->get($viewer);
        $isOfficer_checkviewer = ( null !== $listItem_checkviewer );

		return $isOfficer_checkviewer;
	}

	public function getCategory()
	{
		return Engine_Api::_() -> getDbtable('categories', 'advgroup') -> find($this -> category_id) -> current();
	}

	public function getSportCategory()
	{
		return Engine_Api::_() -> getDbtable('sportcategories', 'user') -> find($this -> sportcategory_id) -> current();
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
			return $this;
			//throw new Group_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'group',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if(defined('exif_read_data'))
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
		$image -> resize(240, 240) -> write($path . '/p_' . $name) -> destroy();


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

		// Add to album
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$photoTable = Engine_Api::_() -> getItemTable('advgroup_photo');
		$groupAlbum = $this -> getSingletonAlbum();
		$photoItem = $photoTable -> createRow();
		$photoItem -> setFromArray(array(
			'group_id' => $this -> getIdentity(),
			'album_id' => $groupAlbum -> getIdentity(),
			'user_id' => $viewer -> getIdentity(),
			'file_id' => $iMain -> getIdentity(),
			'collection_id' => $groupAlbum -> getIdentity(),
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
	
	public function getEventsPaginator()
	{
		if (Engine_Api::_() -> hasModuleBootstrap('event'))
		{
			$table = Engine_Api::_() -> getDbtable('events', 'event');
		}
		else
		{
			$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		}
		$select = $table -> select() -> where('parent_type = ?', 'group');
		$select -> where('parent_id = ?', $this -> getIdentity()) -> order('creation_date DESC');
		return Zend_Paginator::factory($select);
	}

	public function membership()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('membership', 'advgroup'));
	}
	
	public function blacklist()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('blacklist', 'advgroup'));
	}

	public function getAlbumCount($user_id)
	{
		$table = Engine_Api::_() -> getItemTable('advgroup_album');
		$name = $table -> info('name');
		$select = $table -> select() -> from($name, 'COUNT(*) AS count') -> where("group_id = $this->group_id") -> where("user_id = $user_id");
		return $select -> query() -> fetchColumn(0);
	}

	public function getFeatured()
	{
		$group = Engine_Api::_() -> getItem('group', $this -> group_id);
		if (count($group) <= 0)
			return false;
		else
		{
			if ($group -> featured == '1')
				return true;
			else
				return false;
		}
		return false;
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

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 **/
	public function reports()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('reports', 'core'));
	}

	// Internal hooks
	protected function _postInsert()
	{
		if ($this -> _disableHooks)
			return;

		parent::_postInsert();

		// Create auth stuff
		$context = Engine_Api::_() -> authorization() -> context;
		foreach (array('everyone', 'registered', 'member') as $role)
		{
			$context -> setAllowed($this, $role, 'view', true);
		}
	}

	protected function _delete()
	{
		if ($this -> _disableHooks)
			return;

		// Delete all memberships
		$this -> membership() -> removeAllMembers();

		// Delete officer list
		$this -> getOfficerList() -> delete();

		// Delete all albums
		$albumTable = Engine_Api::_() -> getItemTable('advgroup_album');
		$albumSelect = $albumTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($albumTable->fetchAll($albumSelect) as $groupAlbum)
		{
			$groupAlbum -> delete();
		}

		// Delete all topics
		$topicTable = Engine_Api::_() -> getItemTable('advgroup_topic');
		$topicSelect = $topicTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($topicTable->fetchAll($topicSelect) as $groupTopic)
		{
			$groupTopic -> delete();
		}

		//Delete all links
		$linkTable = Engine_Api::_() -> getItemTable('advgroup_link');
		$linkSelect = $linkTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($linkTable->fetchAll($linkSelect) as $groupLink)
		{
			$groupLink -> delete();
		}

		//Delete all announcment
		$announcementTable = Engine_Api::_() -> getItemTable('advgroup_announcement');
		$announcementSelect = $announcementTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($announcementTable->fetchAll($announcementSelect) as $groupAnnouncement)
		{
			$groupAnnouncement -> delete();
		}

		//Delete invites
		$inviteTable = Engine_Api::_() -> getDbTable('invites', 'advgroup');
		$inviteSelect = $inviteTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($inviteTable->fetchAll($inviteSelect) as $groupInvite)
		{
			$groupInvite -> delete();
		}

		//Delete polls
		$pollTable = Engine_Api::_() -> getDbTable('polls', 'advgroup');
		$pollSelect = $pollTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($pollTable->fetchAll($pollSelect) as $groupPoll)
		{
			$groupPoll -> delete();
		}

		//Delete reports
		$reportTable = Engine_Api::_() -> getDbTable('reports', 'advgroup');
		$reportSelect = $reportTable -> select() -> where('group_id = ?', $this -> getIdentity());
		foreach ($reportTable->fetchAll($reportSelect) as $groupReport)
		{
			$groupReport -> delete();
		}

		//Delete all events
		if (Engine_Api::_() -> hasItemType('event'))
		{
			$eventTable = Engine_Api::_() -> getItemTable('event');
			$eventSelect = $eventTable -> select() -> where("parent_type = 'group' and parent_id = ?", $this -> getIdentity());
			foreach ($eventTable->fetchAll($eventSelect) as $groupEvent)
			{
				$groupEvent -> delete();
			}
		}
		if (Engine_Api::_() -> hasItemType('video'))
		{
			$videoTable = Engine_Api::_() -> getItemTable('video');
			$videoSelect = $videoTable -> select() -> where("parent_type = 'group' and parent_id = ?", $this -> getIdentity());
			foreach ($videoTable->fetchAll($videoSelect) as $groupVideo)
			{
				$groupVideo -> delete();
			}
		}
		parent::_delete();
	}

	public function getGroupMembers()
	{
		$select = $this -> membership() -> getMembersObjectSelect();
		$models = new User_Model_DbTable_Users();
		$members = $models -> fetchAll($select);
		
		return $members;
	}
	
	public function countGroupMembers()
	{
		$select = $this -> membership() -> getMembersObjectSelect();
		$models = new User_Model_DbTable_Users();
		$count = 0;
		foreach($models -> fetchAll($select) as  $member)
		{
			$count++;
		}
		return $count;
	}
	
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}
	
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}
	
	public function getTimeAgo()
	{
		$view = Zend_Registry::get("Zend_View");
		$now = time();
		$timeAgo = "Active ";
		$date = strtotime($this->creation_date);
		$years = date('Y', $now) - date("Y", $date);
		$months = date('n', $now) - date("n", $date);
		$days = date('j', $now) - date("j", $date);
		if($months < 0)
		{
			$months = 12 + $months;
			$years = $years - 1;
		}
		if($days < 0)
		{
			$days = 30 + $days;
			$months = $months - 1;
		}
		if($years)
		{
			$timeAgo .= $view -> translate(array("%s year ago", "%s years ago", $years),$years);
			return $timeAgo;
		}
		else if($months)
		{
			$timeAgo .= $view -> translate(array("%s month ago", "%s months ago", $months), $months);
			return $timeAgo;
		}	
		else if($days)
		{
			$timeAgo .= $view -> translate(array("%s day ago", "%s days ago", $days), $days);
			return $timeAgo;
		}
		else 
		  return $view->translate("Active today");
	}
	
	public function isNewGroup()
	{
		
		$time = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.advgrouptime', 20);
		$unittime = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.advgroupunittime',1);
		$multiple = 0 ;  	
		$type = "";
		  	switch ($unittime) {
		    case 1:
		        $type = "days";
				$multiple = 30;
		        break;
		    case 2:
		        $type = "days";
				$multiple = 7;
		        break;
		    case 3:
		        $type = "day";
				$multiple = 1;
		        break;
		 }
		
		$date=date_create(date('Y-m-d h:i:s'));
			
		date_sub($date,date_interval_create_from_date_string($time*$multiple." ".$type));
		
		if(strtotime($this->creation_date)	> strtotime(date_format($date,"Y-m-d h:i:s")))
		{
			
			return true;
		} 
		else{
			
			return false;
		}
		
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
		
		if (!empty($params['user_ids']) && is_array($params['user_ids'])) {
			$select->where($rName . '.owner_id IN (?)', $params['user_ids']);
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
		return $this->sportcategory_id;
	}
	
	public function getCountry() {
		return Engine_Api::_()->getItem('user_location', $this->country_id);
	}
	
	public function getProvince() {
		return Engine_Api::_()->getItem('user_location', $this->province_id);
	}
	
	public function getCity() {
		return Engine_Api::_()->getItem('user_location', $this->city_id);
	}
}
