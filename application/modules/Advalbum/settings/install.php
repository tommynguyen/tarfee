<?php

class Advalbum_Installer extends Engine_Package_Installer_Module
{

	function onInstall()
	{
		$this -> _albumCreatePage();
		$this -> _albumManagePage();
		$this -> _albumViewPage();
		$this -> _virtualAlbumCreatePage();
		
		
		//
		// install content areas
		//
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		
		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// album.profile-albums

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'advalbum.profile-albums');

		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			// container_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
			$container_id = $select -> query() -> fetchObject() -> content_id;

			// middle_id (will always be there)
			$select = new Zend_Db_Select($db);
			$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
			$middle_id = $select -> query() -> fetchObject() -> content_id;

			// tab_id (tab container) may not always be there
			$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
			$tab_id = $select -> query() -> fetchObject();
			if ($tab_id && @$tab_id -> content_id)
			{
				$tab_id = $tab_id -> content_id;
			}
			else
			{
				$tab_id = null;
			}

			// tab on profile
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.profile-albums',
				'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
				'order' => 4,
				'params' => '{"title":"Albums","titleCount":true}'
			));
		}
		
		
		// update album home page
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advalbum_index_browse') -> limit(1);
		$info = $select -> query() -> fetch();
		$page_id = 0;
		if ($info)
		{
			$page_id = $info['page_id'];
		}
		
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('name = ?', 'advalbum.top-recent-albums') -> where('page_id = ?', $page_id) -> limit(1);
		$info = $select -> query() -> fetch();
		if ($info)
		{
			$order = $info['order'];
			$parent_content_id = $info['parent_content_id'];
			try
			{
				$db -> insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'core.container-tabs',
					'parent_content_id' => $parent_content_id,
					'order' => $order,
					'params' => '{"max":"6","title":"","name":"core.container-tabs"}'
				));
				$tab_id = $db -> lastInsertId('engine4_core_content');
				
				$db -> insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'advalbum.top-albums',
					'parent_content_id' => $tab_id,
					'order' => 1,
					'params' => '{"title":"Top Albums"}'
				));
				$db -> insert('engine4_core_content', array(
					'page_id' => $page_id,
					'type' => 'widget',
					'name' => 'advalbum.recent-albums',
					'parent_content_id' => $tab_id,
					'order' => 2,
					'params' => '{"title":"Recent Ablums"}'
				));
			}
			catch( Exception $e )
			{
			}
		}
		
	
		// Browse Album
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advalbum_index_browse') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'advalbum_index_browse',
				'displayname' => 'Advanced Albums Homepage',
				'title' => 'Browse Albums',
				'description' => 'This is browse albums page.'
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => ''
			));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $top_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-menu',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => ''
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => ''
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'right',
				'parent_content_id' => $container_id,
				'order' => 5,
				'params' => ''
			));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 1,
				'params' => '{"max":"6","title":"","name":"core.container-tabs"}'
			));
			$tab0_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.featured-photos',
				'parent_content_id' => $tab0_id,
				'order' => 1,
				'params' => '{"title":"Featured Photos"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.featured-albums',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => '{"title":"Featured Albums"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.top-recent-albums',
				'parent_content_id' => $middle_id,
				'order' => 7,
				'params' => '{"title":""}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 8,
				'params' => '{"max":"6","title":"","name":"core.container-tabs"}'
			));
			$tab11_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.top-albums',
				'parent_content_id' => $tab11_id,
				'order' => 1,
				'params' => '{"title":"Top Albums"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.recent-albums',
				'parent_content_id' => $tab11_id,
				'order' => 2,
				'params' => '{"title":"Recent Albums"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 9,
				'params' => '{"max":"6","title":"","name":"core.container-tabs"}'
			));
			$tab1_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.recent-photos',
				'parent_content_id' => $tab1_id,
				'order' => 1,
				'params' => '{"title":"Recent Photos"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.most-viewed-photos',
				'parent_content_id' => $tab1_id,
				'order' => 2,
				'params' => '{"title":"Most Viewed Photos"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.most-commented-photos',
				'parent_content_id' => $tab1_id,
				'order' => 3,
				'params' => '{"title":"Most Commented Photos"}'
			));

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'core.container-tabs',
				'parent_content_id' => $middle_id,
				'order' => 10,
				'params' => '{"max":"6"}'
			));
			$tab2_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.this-month-photos',
				'parent_content_id' => $tab2_id,
				'order' => 1,
				'params' => '{"title":"This Month\'s Photos"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.this-week-photos',
				'parent_content_id' => $tab2_id,
				'order' => 2,
				'params' => '{"title":"This Week\'s Photos"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.today-photos',
				'parent_content_id' => $tab2_id,
				'order' => 3,
				'params' => '{"title":"Today\'s Photos"}'
			));

			// right column

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-search',
				'parent_content_id' => $right_id,
				'order' => 17,
				'params' => ''
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-categories',
				'parent_content_id' => $right_id,
				'order' => 18,
				'params' => '{"title":"Categories"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-top-members',
				'parent_content_id' => $right_id,
				'order' => 19,
				'params' => '{"title":"Top Members"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-statistics',
				'parent_content_id' => $right_id,
				'order' => 20,
				'params' => '{"title":"Statistics"}'
			));
		}
		// Listing Album
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advalbum_index_listing') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'advalbum_index_listing',
				'displayname' => 'Advanced Albums Listing',
				'title' => 'Albums Listing',
				'description' => 'This is albums listing page.'
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => ''
			));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $top_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-menu',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => ''
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => ''
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'right',
				'parent_content_id' => $container_id,
				'order' => 5,
				'params' => ''
			));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-listing',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => ''
			));
			// right column

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-search',
				'parent_content_id' => $right_id,
				'order' => 17,
				'params' => ''
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-categories',
				'parent_content_id' => $right_id,
				'order' => 18,
				'params' => '{"title":"Categories"}'
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-top-members',
				'parent_content_id' => $right_id,
				'order' => 19,
				'params' => '{"title":"Top Members"}'
			));
		}
		
		
		// Listing Photo
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'advalbum_index_listing-photo') -> limit(1); ;
		$info = $select -> query() -> fetch();

		if (empty($info))
		{
			$db -> insert('engine4_core_pages', array(
				'name' => 'advalbum_index_listing-photo',
				'displayname' => 'Advanced Albums Listing Photos',
				'title' => 'Photos Listing',
				'description' => 'This is photos listing page.'
			));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'top',
				'parent_content_id' => null,
				'order' => 1,
				'params' => ''
			));
			$top_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $top_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.albums-menu',
				'parent_content_id' => $middle_id,
				'order' => 3,
				'params' => ''
			));
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'main',
				'parent_content_id' => null,
				'order' => 2,
				'params' => ''
			));
			$container_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'middle',
				'parent_content_id' => $container_id,
				'order' => 6,
				'params' => ''
			));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'container',
				'name' => 'right',
				'parent_content_id' => $container_id,
				'order' => 5,
				'params' => ''
			));
			$right_id = $db -> lastInsertId('engine4_core_content');
			// middle column
			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.photos-listing',
				'parent_content_id' => $middle_id,
				'order' => 6,
				'params' => ''
			));
			// right column

			$db -> insert('engine4_core_content', array(
				'page_id' => $page_id,
				'type' => 'widget',
				'name' => 'advalbum.photos-search',
				'parent_content_id' => $right_id,
				'order' => 17,
				'params' => ''
			));
		}
		
		if ($this -> checkModuleAlbum())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'album';");

			// delete duplicate album row from engine4_authorization_allow
			$query = "select * FROM `engine4_authorization_allow` AS a1  Where a1.resource_type = 'album' AND EXISTS (
                                    SELECT * FROM `engine4_authorization_allow` a2
                                    WHERE a2.resource_type = 'advalbum_album'
                                    AND a1.resource_id = a2.resource_id
                                    AND a1.`action` = a2.`action`
                                    AND a1.role = a2.role
                                    AND a1.role_id = a2.role_id)";
			$this -> deleteDuplicateAlbumPermissionRow($query);

			$this -> updateAlbumsConfiguration("1");

			/*
			 try {
			 $db->query(
			 "ALTER TABLE `engine4_album_photos` ADD COLUMN `order` int(11) unsigned NOT NULL default '0' AFTER `modified_date`;");
			 } catch (exception $e) {}
			 try {
			 $db->query("ALTER TABLE `engine4_album_albums` ADD COLUMN `rating` FLOAT NOT NULL DEFAULT '0'");
			 } catch (exception $e) {}
			 try {
			 $db->query("ALTER TABLE `engine4_album_albums` ADD COLUMN `featured` TINYINT(1) NULL DEFAULT '0'");
			 } catch (exception $e) {}
			 */
		}
		try
		{
			$db -> query("ALTER TABLE `engine4_album_photos` ADD COLUMN `order` int(11) unsigned NOT NULL default '0' AFTER `modified_date`;");
		}
		catch(exception $e)
		{
		}
		// hide the previous versions of YouNetCore's Adv Albums
		try
		{
			$this -> renamePreviousVersion();
		}
		catch (exception $e)
		{
		}

		parent::onInstall();
	}

	protected function _albumManagePage()
	{

		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advalbum_index_manage') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'advalbum_index_manage',
				'displayname' => 'Advanced Album Manage Page',
				'title' => 'My Albums',
				'description' => 'This page lists album a user\'s albums.',
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
			
			// Insert menu
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advalbum.albums-menu',
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

			// Insert search
			$db -> insert('engine4_core_content', array(
				'type' => 'widget',
				'name' => 'advalbum.albums-search',
				'page_id' => $page_id,
				'parent_content_id' => $main_right_id,
				'order' => 1,
			));

		}

		return $this;
	}

	protected function _albumCreatePage()
	{

		$db = $this -> getDb();

		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advalbum_index_upload') -> limit(1) -> query() -> fetchColumn();

		if (!$page_id)
		{

			// Insert page
			$db -> insert('engine4_core_pages', array(
				'name' => 'advalbum_index_upload',
				'displayname' => 'Advanced Album Create Page',
				'title' => 'Add New Photos',
				'description' => 'This page is the album create page.',
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
				'name' => 'advalbum.albums-menu',
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

	protected function _albumViewPage()
	{
		$db = $this -> getDb();
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advalbum_album_view') -> limit(1) -> query() -> fetchColumn();
	
		// insert if it doesn't exist yet
		if (!$page_id)
		{
			// Insert page
			$db -> insert('engine4_core_pages', array(
					'name' => 'advalbum_album_view',
					'displayname' => 'Advanced Album View',
					'title' => 'Advanced Album View',
					'description' => 'Advanced Album View',
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
				
			// Insert menu
			$db -> insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'advalbum.albums-menu',
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
			
			$db -> insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'core.comments',
					'page_id' => $page_id,
					'parent_content_id' => $main_middle_id,
					'order' => 2,
			));
	
			// Insert user other albums widget
			$db -> insert('engine4_core_content', array(
					'type' => 'widget',
					'name' => 'advalbum.user-other-albums',
					'page_id' => $page_id,
					'parent_content_id' => $main_right_id,
					'order' => 1,
			));
	
		}
	
		return $this;
	}
	
	protected function _virtualAlbumCreatePage()
	{
		$db = $this -> getDb();
		
		// profile page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'advalbum_index_create-virtual-album') -> limit(1) -> query() -> fetchColumn();
		
		if (!$page_id)
		{
		
			// Insert page
			$db -> insert('engine4_core_pages', array(
					'name' => 'advalbum_index_create-virtual-album',
					'displayname' => 'Advanced Album Create Virtual Album Page',
					'title' => 'Advanced Album Create Virtual Album Page',
					'description' => 'Advanced Album Create Virtual Album Page.',
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
					'name' => 'advalbum.albums-menu',
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
	
	
	function onEnable()
	{
		if ($this -> checkModuleAlbum())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'album';");

			$query = "select * FROM `engine4_authorization_allow` AS a1  Where a1.resource_type = 'album' AND EXISTS (
                                    SELECT * FROM `engine4_authorization_allow` a2
                                    WHERE a2.resource_type = 'advalbum_album'
                                    AND a1.resource_id = a2.resource_id
                                    AND a1.`action` = a2.`action`
                                    AND a1.role = a2.role
                                    AND a1.role_id = a2.role_id)";
			$this -> deleteDuplicateAlbumPermissionRow($query);

			$this -> updateAlbumsConfiguration("1");
		}
		parent::onEnable();
	}

	function onDisable()
	{
		if ($this -> checkModuleAlbum())
		{
			$db = $this -> getDb();
			$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'album';");

			$query = "select * FROM `engine4_authorization_allow` AS a1  Where a1.resource_type = 'advalbum_album' AND EXISTS (
                                    SELECT * FROM `engine4_authorization_allow` a2
                                    WHERE a2.resource_type = 'album'
                                    AND a1.resource_id = a2.resource_id
                                    AND a1.`action` = a2.`action`
                                    AND a1.role = a2.role
                                    AND a1.role_id = a2.role_id)";
			$this -> deleteDuplicateAlbumPermissionRow($query);

			$this -> updateAlbumsConfiguration("0");
		}
		parent::onDisable();
	}

	public function deleteDuplicateAlbumPermissionRow($select)
	{
		$db = $this -> getDb();
		$result = $db -> query($select);
		if (!empty($result))
		{
			while ($row = $result -> fetch())
			{
				$t = $db -> query("DELETE FROM `engine4_authorization_allow` WHERE
                        resource_type = '{$row['resource_type']}' AND
                                resource_id = '{$row['resource_id']}' AND
                                action = '{$row['action']}' AND
                                role = '{$row['role']}' AND
                                role_id = '{$row['role_id']}'");
			}
		}
	}

	public function checkModuleAlbum()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_modules') -> where('name = ?', 'album') -> limit(1);
		$check = $select -> query() -> fetch();
		if (empty($check))
			return false;
		return true;
	}

	public function getAlbumVersion()
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_modules') -> where('name = ?', 'album') -> limit(1);
		$check = $select -> query() -> fetch();
		if (empty($check))
		{
			return 0;
		}
		else
		{
			$version = str_replace('.', '', $check['version']);
			$version = (int) substr($version, 0, 3);
			return $version;
		}
	}

	public function updateAlbumsConfiguration($enable)
	{
		$version = $this -> getAlbumVersion();
		$db = $this -> getDb();
		if ($enable == "0")
		{
			set_time_limit(0);
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'album_photo_new', `object_type` = 'album' WHERE `engine4_activity_actions`.`type` ='advalbum_photo_new';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'comment_album', `object_type` = 'album_photo' WHERE `engine4_activity_actions`.`type` ='comment_advalbum';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'comment_album_photo', `object_type` = 'album_photo' WHERE `engine4_activity_actions`.`type` ='comment_advalbum_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type`='album' WHERE `engine4_activity_notifications`.`object_type`= 'advalbum_album';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type`='album_photo' WHERE `engine4_activity_notifications`.`object_type`= 'advalbum_photo';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'album_photo' WHERE `engine4_activity_attachments`.`type` ='advalbum_photo';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'album', `type` = 'album_photo_new' WHERE `engine4_activity_stream`.`type` ='advalbum_photo_new';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'album', `type` = 'comment_album' WHERE `engine4_activity_stream`.`type` ='comment_advalbum';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'album_photo', `type` = 'comment_album_photo' WHERE `engine4_activity_stream`.`type` ='comment_advalbum_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type`='album_photo' WHERE `engine4_core_likes`.`resource_type` = 'advalbum_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type`='album' WHERE `engine4_core_likes`.`resource_type` = 'advalbum_album';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type`='album_photo' WHERE `engine4_core_comments`.`resource_type` = 'advalbum_photo';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type`='album' WHERE `engine4_core_comments`.`resource_type` = 'advalbum_album';");
			$db -> query("UPDATE `engine4_core_tagmaps` SET `resource_type` ='album_photo' WHERE `engine4_core_tagmaps`.`resource_type` = 'advalbum_photo';");
			$db -> query("UPDATE `engine4_authorization_allow` SET `resource_type` = 'album' WHERE `engine4_authorization_allow`.`resource_type` = 'advalbum_album';");
			$db -> query("UPDATE `engine4_core_menuitems` SET `enabled` = 0 WHERE `engine4_core_menuitems`.`module` = 'advalbum';");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'album' WHERE `engine4_core_search`.`type` = 'advalbum_album';");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'album_photo' WHERE `engine4_core_search`.`type` = 'advalbum_photo';");
			if ($version > 0 && $version < 417)
			{
				try
				{
					$db -> query("ALTER TABLE `engine4_album_photos` CHANGE `album_id` `collection_id` int(11) unsigned NOT NULL ;");
				}
				catch (exception $e)
				{
				}
			}
		}
		if ($enable == "1")
		{
			set_time_limit(0);
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'advalbum_photo_new' ,`object_type` = 'advalbum_album' WHERE `engine4_activity_actions`.`type` ='album_photo_new';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'comment_advalbum', `object_type` = 'advalbum_photo' WHERE `engine4_activity_actions`.`type` ='comment_album';");
			$db -> query("UPDATE `engine4_activity_actions` SET `type` = 'comment_advalbum_photo', `object_type` = 'advalbum_photo' WHERE `engine4_activity_actions`.`type` ='comment_album_photo';");
			$db -> query("UPDATE `engine4_activity_attachments` SET `type` = 'advalbum_photo' WHERE `engine4_activity_attachments`.`type` ='album_photo';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type`='advalbum_album' WHERE `engine4_activity_notifications`.`object_type`= 'album';");
			$db -> query("UPDATE `engine4_activity_notifications` SET `object_type`='advalbum_photo' WHERE `engine4_activity_notifications`.`object_type`= 'album_photo';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'advalbum_album', `type` = 'advalbum_photo_new' WHERE `engine4_activity_stream`.`type` ='album_photo_new';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'advalbum_album', `type` = 'comment_advalbum' WHERE `engine4_activity_stream`.`type` ='comment_album';");
			$db -> query("UPDATE `engine4_activity_stream` SET `object_type` = 'advalbum_photo', `type` = 'comment_advalbum_photo' WHERE `engine4_activity_stream`.`type` ='comment_album_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type`='advalbum_photo' WHERE `engine4_core_likes`.`resource_type` = 'album_photo';");
			$db -> query("UPDATE `engine4_core_likes` SET `resource_type`='advalbum_album' WHERE `engine4_core_likes`.`resource_type` = 'album';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type`='advalbum_photo' WHERE `engine4_core_comments`.`resource_type` = 'album_photo';");
			$db -> query("UPDATE `engine4_core_comments` SET `resource_type`='advalbum_album' WHERE `engine4_core_comments`.`resource_type` = 'album';");
			$db -> query("UPDATE `engine4_core_tagmaps` SET `resource_type` ='advalbum_photo' WHERE `engine4_core_tagmaps`.`resource_type` = 'album_photo';");
			$db -> query("UPDATE `engine4_authorization_allow` SET `resource_type` = 'advalbum_album' WHERE `engine4_authorization_allow`.`resource_type` = 'album';");
			$db -> query("UPDATE `engine4_core_menuitems` SET `enabled` = 1 WHERE `engine4_core_menuitems`.`module` = 'advalbum';");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'advalbum_album' WHERE `engine4_core_search`.`type` = 'album'");
			$db -> query("UPDATE `engine4_core_search` SET `type` = 'advalbum_photo' WHERE `engine4_core_search`.`type` = 'album_photo'");
			if ($version > 0 && $version < 417)
			{
				try
				{
					$db -> query("ALTER TABLE `engine4_album_photos` CHANGE `collection_id` `album_id`  int(11) unsigned NOT NULL ;");
				}
				catch (exception $e)
				{
				}
			}
		}
	}

	public function renamePreviousVersion()// hide YouNetCore's old version of
	// Adv Album
	{
		defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
		$file_path = APPLICATION_PATH . '/application/modules/Album/settings/manifest.php';
		if (file_exists($file_path))
		{
			$options =
			include $file_path;
			if ($options && isset($options['package']))
			{
				if (isset($options['package']['name']) && $options['package']['name'] == 'album')
				{
					if (isset($options['package']['meta']) && isset($options['package']['meta']['author']))
					{
						$author = $options['package']['meta']['author'];
						$pos = strpos($author, 'YouNet');
						if ($pos !== FALSE)
						{
							// products of YouNet
							$version = "";
							if (isset($options['package']['version']))
							{
								$version = trim($options['package']['version']);
							}
							// update module name (Advanced Album => Album)
							// Note: cannot get the package file by version cause 4.03
							// but has 4.04 in manifest file, so need to scan all the
							// files
							$json_path = APPLICATION_PATH . "/application/packages/";
							$arr_files = array(
								'module-album-4.05p1.json',
								'module-album-4.05.json',
								'module-album-4.04.json',
								'module-album-4.03.json'
							);
							foreach ($arr_files as $file_name)
							{
								$file_path = $json_path . $file_name;
								if (file_exists($file_path))
								{
									$content = file_get_contents($file_path);
									$content = str_replace('Advanced Album', 'Album', $content);
									if ($content)
									{
										try
										{
											if ($f == fopen($file_path, "w+"))
											{
												fwrite($f, $content);
												fclose($f);
											}
										}
										catch (exception $e)
										{
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

}
?>