<?php

class Ynevent_Installer extends Engine_Package_Installer_Module
{
	public function onEnable()
	{
		parent::onEnable();

		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'event';");
	}

	public function onDisable()
	{
		parent::onDisable();

		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'event';");
	}
	
	private function _addEventHomepage()
	{
		$db = $this->getDb();
    	$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynevent'";
    	$module = $db->fetchRow($select);
    	
    	$preview_version = array('4.01', '4.01p1', '4.01p2', '4.01p3', '4.01p4', '4.02', '4.02p1', '4.03', '4.03p1', '4.03p2', '4.04', '4.04p1');    	
	    if(in_array($module['version'], $preview_version))
	    {
    		$query = "DELETE FROM `engine4_core_pages` WHERE name = 'ynevent_index_browse'";
    		$db->query($query);
		}

		// Event brower page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_browse') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_browse',
				'displayname' => 'Advanced Event Upcoming Page',
				'title' => 'Advanced Event Upcoming Page',
				'description' => 'This page lists upcoming events.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 6,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 6,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 3,
			));
			
			
			// Insert slideshow 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.feature-events',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 6,
			));
			
			// Insert ynevent.list-most-time
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.list-most-time',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 8,
			));
			
			// Insert ynevent.list-most-items
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.list-most-items',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 10,
			));
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 5,
			));
			$main_right_id = $db -> lastInsertId();
			
			// Insert right search
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-search',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 12,
			));
			// Insert right event-of-day
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.event-of-day',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 13,
			));
			// Insert right events-tags
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.events-tags',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 14,
			));
		}
		else 
		{
			$tab_container_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ynevent.list-most-time') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$query = "DELETE FROM `engine4_core_content` WHERE name = 'core.content' AND `page_id` = $page_id AND `parent_content_id` = $tab_container_id";
			$db -> query($query);
			
			$query = "DELETE FROM `engine4_core_content` WHERE name = 'ynevent.list-most-time-past' AND `page_id` = $page_id AND `parent_content_id` = $tab_container_id";
			$db -> query($query);
		}
	}
	// Past events page
	private function _addEventPastpage()
	{
		$db     = $this->getDb();
		// Event past page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_past') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_past',
				'displayname' => 'Advanced Event Past Page',
				'title' => 'Advanced Event Past Page',
				'description' => 'This page lists past events.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 6,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 6,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 3,
			));
			
			
			// Insert slideshow 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.feature-events',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 6,
			));
			
			// Insert ynevent.list-most-time-past
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.list-most-time-past',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 7,
			));
			// Insert ynevent.list-most-items
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.list-most-items',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 8,
			));
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 5,
			));
			$main_right_id = $db -> lastInsertId();
			
			// Insert right search
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-search',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 12,
			));
			// Insert right event-of-day
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.event-of-day',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 13,
			));
			// Insert right events-tags
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.events-tags',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 14,
			));
		}
	}
	// Search result page
	private function _addEventListingpage()
	{
		$db = $this->getDb();
		// Event listing page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_listing') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_listing',
				'displayname' => 'Advanced Event Listing Page',
				'title' => 'Advanced Event Lisitng Page',
				'description' => 'This page lists search result events.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 6,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 6,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 3,
			));
			
			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 7,
			));
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 5,
			));
			$main_right_id = $db -> lastInsertId();
			
			// Insert right search
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-search',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 12,
			));
			// Insert right event-of-day
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.event-of-day',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 13,
			));
			// Insert right events-tags
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.events-tags',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 14,
			));
		}
	}

	private function _addEventProfilepage()
	{
		$db     = $this->getDb();
    	$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynevent'";
    	$module = $db->fetchRow($select);
    	
    	$preview_version = array('4.01', '4.01p1', '4.01p2', '4.01p3', '4.01p4', '4.02', '4.02p1', '4.03', '4.03p1', '4.03p2', '4.04', '4.04p1');    	
	    if(in_array($module['version'], $preview_version))
	    {
	    	$array_temp = array();
    		$array_temp[] = "DELETE FROM `engine4_core_pages` WHERE name = 'ynevent_profile_index'";
    		foreach($array_temp as $temp)
    		{
    			$db->query($temp);
    		}
		}

		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_profile_index') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_profile_index',
				'displayname' => 'Advanced Event Profile Page',
				'title' => 'Advanced Event Profile Page',
				'description' => 'This page lists event profile.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();
			
			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
				'order' => 6,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 3,
			));
			
			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 6,
			));
			$main_middle_id = $db -> lastInsertId();
			
			// Insert profile cover 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-cover',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 6,
			));
			
			// Insert profile announcement 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-announcements',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 7,
			));
			// Insert profile description 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-description',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 8,
			));
			
			// Insert profile description 
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 9,
				'params' => '{"max":"6","title":"","nomobile":"0","name":"core.container-tabs"}',
			));
			$main_container_id = $db -> lastInsertId();
			// Insert profile ynevent.profile-map
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-map',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 11,
				'params' => '{"title":"Location","titleCount":true}',
			));
			// Insert activity.feed
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'activity.feed',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 10,
				'params' => '{"title":"Updates"}',
			));
			// Insert ynevent.profile-blogs
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-blogs',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 12,
				'params' => '{"title":"Blogs","itemCountPerPage":"10"}',
			));
			// Insert ynevent.profile-calendar
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-calendar',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 13,
				'params' => '{"title":"Calendar"}',
			));
			// insert ynevent.profile-members
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-members',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 14,
				'params' => '{"title":"Guests"}',
			));
			// insert ynevent.profile-review
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-review',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 15,
				'params' => '{"title":"Review"}',
			));
			// insert ynevent.profile-photos
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-photos',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 16,
				'params' => '{"title":"Photos"}',
			));
			// insert ynevent.profile-videos
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-videos',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 17,
				'params' => '{"title":"Videos"}',
			));
			// insert core.profile-links
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.profile-links',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 18,
				'params' => '{"title":"Links"}',
			));
			// insert ynevent.profile-sponsors
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-sponsors',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 19,
				'params' => '{"title":"Sponsors"}',
			));
			// insert ynevent.profile-discussions
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-discussions',
				'page_id' => $page_id,
				'parent_content_id' => $main_container_id,
				'order' => 20,
				'params' => '{"title":"Discussions"}',
			));
			
			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 5,
			));
			$main_right_id = $db -> lastInsertId();
			// Insert ynevent.profile-google-calendar
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-google-calendar',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 22,
			));
			// Insert ynevent.profile-video
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-video',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 23,
				'params' => '{"title":"Highlight video"}',
			));
			// Insert ynevent.profile-tags
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-tags',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 24,
				'params' => '{"title":"Event Tags"}',
			));
			// Insert ynevent.profile-slideshow-photos
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-slideshow-photos',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 25,
				'params' => '{"title":"Slideshow Photos"}',
			));
			// Insert ynevent.profile-rsvp
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-rsvp',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 26,
			));
			// Insert widget-announcements
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.profile-widget-announcements',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 27,
			));
			
		}
	}
	
	public function onInstall()
	{
		$this -> _addEventHomepage();
		$this -> _addEventPastpage();
		$this -> _addEventListingpage();
		$this -> _addEventProfilepage();
		$this -> _addEventCreatePage();
		$this -> _addEventManagePage();
		$this -> _addEventCalendarPage();
		$this -> _addMobiEventProfilePage();

		parent::onInstall();
		$this -> _ynBuildStructure();

		//Disable SE Event
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'event';");

		$db = $this -> getDb();
		// Browse page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_browse') -> limit(1) -> query() -> fetchColumn();
		if ($page_id)
		{
			$featured_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynevent.feature-events') -> limit(1) -> query() -> fetchColumn();

			if (!$featured_widget_id)
			{
				//Getting middle main
				$main_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> where('name = ?', 'main') -> limit(1) -> query() -> fetchColumn();
				if ($main_middle_id)
				{
					//Getting middle container
					$container_middle_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> where('parent_content_id = ?', $main_middle_id) -> limit(1) -> query() -> fetchColumn();
					if ($container_middle_id)
					{
						// Insert content
						$db -> insert('engine4_core_content', array(
							'type' => 'widget',
							'name' => 'ynevent.feature-events',
							'page_id' => $page_id,
							'parent_content_id' => $container_middle_id,
							'order' => 1,
						));
					}
				}
			}
		}
		
		// Manage page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_manage') -> limit(1) -> query() -> fetchColumn();
		if ($page_id)
		{
			$search_widget_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynevent.manage-search') -> limit(1) -> query() -> fetchColumn();
		
			if (!$search_widget_id)
			{
				//Getting right main
				$main_right_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> where('name = ?', 'right') -> limit(1) -> query() -> fetchColumn();
				if ($main_right_id)
				{
					// Insert widget search
					$db -> insert('engine4_core_content', array(
							'type' => 'widget',
							'name' => 'ynevent.manage-search',
							'page_id' => $page_id,
							'parent_content_id' => $main_right_id,
							'order' => 1,
					));
					// Insert widget search
					$db -> insert('engine4_core_content', array(
							'type' => 'widget',
							'name' => 'ynevent.event-of-day',
							'page_id' => $page_id,
							'parent_content_id' => $main_right_id,
							'order' => 2,
					));
				}
			}
		}
	}

	/**
	 * rebuild structure from structure file
	 * structure file is builded from rip export
	 * @return void
	 */
	protected function _ynBuildStructure()
	{
		$filename = dirname(__FILE__) . '/structure.php';
		$structure =
		include $filename;

		if (isset($structure['module']) && !empty($structure['module']))
		{
			$this -> _ynBuildModule($structure['module']);
		}

		if (isset($structure['pages']) && !empty($structure['pages']))
		{
			$this -> _ynBuildPages($structure['pages']);
		}

		if (isset($structure['menus']) && !empty($structure['menus']))
		{
			$this -> _ynBuildMenus($structure['menus']);
		}

		if (isset($structure['menuitems']) && !empty($structure['menuitems']))
		{
			$this -> _ynBuildMenuItems($structure['menuitems']);
		}

		if (isset($structure['mails']) && !empty($structure['mails']))
		{
			$this -> _ynBuildMails($structure['mails']);
		}

		if (isset($structure['jobtypes']) && !empty($structure['jobtypes']))
		{
			$this -> _ynBuildJobTypes($structure['jobtypes']);
		}

		if (isset($structure['actiontypes']) && !empty($structure['actiontypes']))
		{
			$this -> _ynBuildActionTypes($structure['actiontypes']);
		}

		if (isset($structure['notificationtypes']) && !empty($structure['notificationtypes']))
		{
			$this -> _ynBuildNotificationTypes($structure['notificationtypes']);
		}

		if (isset($structure['permissions']) && !empty($structure['permissions']))
		{
			$this -> _ynBuildPermission($structure['permissions']);
		}

	}

	/**
	 * update package information from this page, we are welcome all experted
	 * information.
	 */
	protected function _ynBuildModule($row)
	{
		$name = $row['name'];
		$db = $this -> getDb();

		if ($db -> fetchOne("select count(*) from engine4_core_modules where name='{$name}'"))
		{
			unset($row['name']);
			$db -> update('engine4_core_modules', $row, "name='{$name}'");
		}
		else
		{
			$db -> insert('engine4_core_modules', $row);
		}
	}

	/**
	 * rebuild menu
	 */
	protected function _ynBuildMenus($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_core_menus where name='" . $row['name'] . "'"))
			{
				unset($row['id']);
				$db -> insert('engine4_core_menus', $row);
			}
		}
	}

	/**
	 * rebuild menu items
	 */
	protected function _ynBuildMenuItems($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_core_menuitems where name='" . $row['name'] . "'"))
			{
				unset($row['id']);
				$db -> insert('engine4_core_menuitems', $row);
			}
		}

	}

	/**
	 * rebuild mail
	 */
	protected function _ynBuildMails($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_core_mailtemplates where type='" . $row['type'] . "'"))
			{
				unset($row['mailtemplate_id']);
				$db -> insert('engine4_core_mailtemplates', $row);
			}
		}
	}

	/**
	 * rebuild mail
	 */
	protected function _ynBuildJobTypes($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_core_jobtypes where type='" . $row['type'] . "'"))
			{
				unset($row['jobtype_id']);
				$db -> insert('engine4_core_jobtypes', $row);
			}
		}
	}

	/**
	 * rebuild mail
	 */
	protected function _ynBuildNotificationTypes($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_activity_notificationtypes where type='" . $row['type'] . "'"))
			{
				$db -> insert('engine4_activity_notificationtypes', $row);
			}
		}
	}

	/**
	 * rebuild mail
	 */
	protected function _ynBuildActionTypes($rows)
	{
		$db = $this -> getDb();
		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			if (!$db -> fetchOne("select count(*) from engine4_activity_actiontypes where type='" . $row['type'] . "'"))
			{
				$db -> insert('engine4_activity_actiontypes', $row);
			}
		}
	}

	protected function _ynBuildPermission($rows)
	{
		$db = $this -> getDb();

		foreach ($rows as $row)
		{
			if (empty($row))
			{
				continue;
			}
			list($level, $type, $name, $value, $params) = $row;

			if ($value === NULL)
			{
				$value = 'NULL';
			}

			if ($params == NULL)
			{
				$params = 'NULL';
			}
			else
			{
				$params = $db -> quote($params);
			}

			$sql = "INSERT IGNORE INTO `engine4_authorization_permissions`
                      SELECT
                        level_id as `level_id`,
                        '{$type}' as `type`,
                        '{$name}' as `name`,
                        '$value' as `value`,
                        $params as `params`
                      FROM `engine4_authorization_levels` WHERE `type` IN('$level');
                ";
			$db -> query($sql);
		}

	}

	/**
	 * rebuidl pages
	 */
	protected function _ynBuildPages($pageStructure)
	{
		$db = $this -> getDb();

		foreach ($pageStructure as $name => $page)
		{
			// check page
			$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', $name) -> limit(1) -> query() -> fetchColumn();
			if ($page_id)
			{
				continue;
			}
			else
			{
				$this -> _ynAddOnePage($page);
			}
		}

	}

	protected function _ynAddOnePage($page)
	{
		$db = $this -> getDb();
		// Insert page
		$db -> insert('engine4_core_pages', array(
			'name' => $page['name'],
			'displayname' => $page['displayname'],
			'url' => $page['url'],
			'title' => $page['title'],
			'description' => $page['description'],
			'keywords' => $page['keywords'],
			'custom' => $page['custom'],
			'fragment' => $page['fragment'],
			'layout' => $page['layout'],
			'levels' => $page['levels'],
			'provides' => $page['provides']
		));

		$page_id = $db -> lastInsertId();

		if (!$page_id)
		{
			return false;
		}

		if (isset($page['ynchildren']) && !empty($page['ynchildren']))
		{
			$this -> _ynAddPageContent($page_id, null, $page['ynchildren']);
		}
		return true;
	}

	protected function _ynAddPageContent($page_id, $parent_content_id = null, $contents)
	{
		$db = $this -> getDb();
		foreach ($contents as $content)
		{
			if (empty($content))
			{
				continue;
			}
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'parent_content_id' => $parent_content_id,
				'type' => $content['type'],
				'name' => $content['name'],
				'order' => $content['order'],
				'params' => $content['params'],
				'attribs' => $content['attribs']
			));

			$pid = $db -> lastInsertId();

			if (!$pid)
			{
				throw new Engine_Package_Installer_Exception("can not insert to page content!");
			}

			/**
			 * recursiver insert to content
			 */
			if (isset($content['ynchildren']) && !empty($content['ynchildren']))
			{
				$this -> _ynAddPageContent($page_id, $pid, $content['ynchildren']);
			}
		}
	}

	protected function _addEventCreatePage()
	{
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_create') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_create',
				'displayname' => 'Advanced Event Create Page',
				'title' => 'Event Create',
				'description' => 'This page allows users to create events.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));

		}

	}

	protected function _addEventManagePage()
	{
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_manage') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_manage',
				'displayname' => 'Advanced Event Manage Page',
				'title' => 'My Events',
				'description' => 'This page lists a user\'s events.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert main-right
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'right',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 1,
			));
			$main_right_id = $db -> lastInsertId();

			// Insert right search
			$db -> insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynevent.manage-search',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 1,
			));
			
			// Insert right event-of-day
			$db -> insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'ynevent.event-of-day',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 2,
			));
			
			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));

			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));
		}
	}

	protected function _addEventCalendarPage()
	{
		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_index_calendar') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_index_calendar',
				'displayname' => 'Advanced Event Calendar Page',
				'title' => 'Event Calendar',
				'description' => 'This page allows users check event calendar.',
				'custom' => 0,
			));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'top',
				'page_id' => $page_id,
				'order' => 1,
			));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'main',
				'page_id' => $page_id,
				'order' => 2,
			));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $top_id,
			));
			$top_middle_id = $db -> lastInsertId();

			// Main-Right container
			$db -> insert('engine4_core_content', 
				array('page_id' => $page_id, 
				'type' => 'container', 
				'name' => 'right', 
				'parent_content_id' => $main_id, 
				'order' => 1, 'params' => '', ));
			
			$main_right_id = $db -> lastInsertId('engine4_core_content');
			
			// Insert main-middle
			$db -> insert('engine4_core_content', array(
				'type' => 'container',
				'name' => 'middle',
				'page_id' => $page_id,
				'parent_content_id' => $main_id,
				'order' => 2,
			));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'ynevent.browse-menu',
				'page_id' => $page_id,
				'parent_content_id' => $top_middle_id,
				'order' => 1,
			));
			
			// Main-Right Widgets
			$db -> insert('engine4_core_content', 
				array('page_id' => $page_id, 
				'type' => 'widget', 
				'name' => 'ynevent.event-of-day', 
				'parent_content_id' => $main_right_id, 
				'order' => 1, 
			));
			
			// Insert content
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'core.content',
				'page_id' => $page_id,
				'parent_content_id' => $main_middle_id,
				'order' => 1,
			));

		}

	}

	protected function _addMobiEventProfilePage()
	{
		$db = $this -> getDb();
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynevent_mobiprofile_index') -> limit(1);
		$page_id = $select -> query() -> fetchColumn();

		if (!$page_id)
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'ynevent_mobiprofile_index',
				'displayname' => 'YouNet Mobile Advanced Event Profile',
				'title' => 'YouNet Mobile Advanced Event Profile',
				'description' => 'This is the mobile verison of an advanced event profile.',
				'custom' => 0
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 1,
				'params' => '',
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 2,
				'params' => '',
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-status',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-photo',
				'parent_content_id' => $middle_id,
				'order' => 4,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-rsvp',
				'parent_content_id' => $middle_id,
				'order' => 5,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-options',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-info',
				'parent_content_id' => $middle_id,
				'order' => 7,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-announcements',
				'parent_content_id' => $middle_id,
				'order' => 8,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-map',
				'parent_content_id' => $middle_id,
				'order' => 9,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-follow',
				'parent_content_id' => $middle_id,
				'order' => 10,
				'params' => '',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 11,
				'params' => '{"max":3}',
			));
			$tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynmobileview.mobi-feed',
				'parent_content_id' => $tab_id,
				'order' => 12,
				'params' => '{"title":"What\'s New"}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-members',
				'parent_content_id' => $tab_id,
				'order' => 13,
				'params' => '{"title":"Guests","titleCount":true}',
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'ynevent.profile-photos',
				'parent_content_id' => $tab_id,
				'order' => 14,
				'params' => '{"title":"Photos","titleCount":true}',
			));
			/*
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'ynevent.profile-videos',
			 'parent_content_id' => $tab_id,
			 'order' => 14,
			 'params' => '{"title":"Videos","titleCount":true}',
			 ));
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'ynevent.profile-sponsors',
			 'parent_content_id' => $tab_id,
			 'order' => 15,
			 'params' => '{"title":"Sponsors","titleCount":true}',
			 ));
			 $db -> insert('engine4_core_content', array(
			 'page_id' => $page_id,
			 'type' => 'widget',
			 'name' => 'ynevent.profile-discussions',
			 'parent_content_id' => $tab_id,
			 'order' => 16,
			 'params' => '{"title":"Discussions","titleCount":true}',
			 ));
			 */
		}
		else 
		{
			$tab_container_id = $db -> select() -> from('engine4_core_content', 'parent_content_id') -> where('name = ?', 'ynevent.profile-info') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			$content_id = $db -> select() -> from('engine4_core_content', 'content_id') -> where('name = ?', 'ynevent.profile-announcements') -> where ('page_id = ?', $page_id) -> limit(1) -> query() -> fetchColumn();
			if(!$content_id)
			{
				$db -> insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'ynevent.profile-announcements',
					'parent_content_id' => $tab_container_id,
					'order' => 7,
					'params' => '',
				));
			}
		}
	}

}
