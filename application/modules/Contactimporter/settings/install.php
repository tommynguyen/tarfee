<?php
    /**
    * SocialEngine
    *
    * @category   Application_Extensions
    * @package    Contactimporter
    * @copyright  Younet
    * @license    http://www.socialengine.net/license/
    * @version    $Id: Bootstrap.php 7244 2010-09-28 01:49:53Z son $
    * @author     Son
    */

    /**
    * @category   Application_Extensions
    * @package    Contactimporter
    * @copyright  Younet
    * @license    http://www.socialengine.net/license/
    */
    class Contactimporter_Installer extends Engine_Package_Installer_Module
    {
        function onInstall()
        {
            //
            // install content areas
            //
            $db     = $this->getDb();
            $select = new Zend_Db_Select($db);


            $select
            ->from('engine4_core_modules')              
            ->where('name = ?', 'contactimporter')
            ;
            $info = $select->query()->fetch();

            if( empty($info) ) {
                $db->insert('engine4_core_modules', array(
                'name' => 'contactimporter',
                'title'    => 'Contactimporter',
                'description'    => 'Contact Importer',
                'version' => "4.0.1",
                'enabled'   => 1,
                'type'  => 'extra',
                ));
                $db->insert('engine4_core_menuitems', array(
                'name' => 'core_main_contactimporter',
                'module'    => 'contactimporter',
                'label'    => 'Inviter',
                'plugin' => "",
                'params'   => '{"route":"default","module":"contactimporter"}',
                'menu'  => 'core_main',
                'submenu'  => '',
                'order'  => '99',
                ));	  
            }


            // contactimporter.homepage-inviter

            // member homepage
            $select = new Zend_Db_Select($db);
            $select
            ->from('engine4_core_pages')
            ->where('name = ?', 'user_index_home')
            ->limit(1);
            $page_id = $select->query()->fetchObject()->page_id;

            // Check if it's already been placed
            $select = new Zend_Db_Select($db);
            $select
            ->from('engine4_core_content')
            ->where('page_id = ?', $page_id)
            ->where('type = ?', 'widget')
            ->where('name = ?', 'contactimporter.homepage-inviter')
            ;

            $info = $select->query()->fetch();
            if( empty($info) ) {
                // container_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                ->from('engine4_core_content')
                ->where('page_id = ?', $page_id)
                ->where('type = ?', 'container')
                ->limit(1);

                $container_id = $select->query()->fetchObject()->content_id;

                // middle_id (will always be there)
                $select = new Zend_Db_Select($db);
                $select
                ->from('engine4_core_content')
                ->where('parent_content_id = ?', $container_id)
                ->where('type = ?', 'container')
                ->where('name = ?', 'middle')
                ->limit(1);
                $middle_id = $select->query()->fetchObject()->content_id;

                // tab on profile
                $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type'    => 'widget',
                'name'    => 'contactimporter.homepage-inviter',
                'parent_content_id' => $middle_id,
                'params'	=> '{"title":"Import Your Contacts"}',
                'order'   => 1,
                ));
            }
            parent::onInstall();
			$this->_addInviterHomePage();
			$this->_addInviterHomQueueEmailPage();
			$this->_addInviterHomQueueMessagePage();
			$this->_addInviterHomPendingInvitationPage();
        }
		function onDisable() 
		{
		    $db = $this->getDb();
		    $db ->query("UPDATE `engine4_user_signup` SET `class` = 'User_Plugin_Signup_Invite' WHERE `class` = 'Contactimporter_Plugin_Signup_Invite';");
		    parent::onDisable();
		  
		}
		function onEnable() 
		{
	        $db = $this->getDb();
	        $db ->query("UPDATE `engine4_user_signup` SET `class` = 'Contactimporter_Plugin_Signup_Invite' WHERE `class` = 'User_Plugin_Signup_Invite';");
   			parent::onEnable();
  		}
		
		protected function _addInviterHomePage()
		{
		    $db = $this->getDb();
		
		    // Inviter Home Page
		    $page_id = $db->select()
		      ->from('engine4_core_pages', 'page_id')
		      ->where('name = ?', 'contactimporter_index_import')
		      ->limit(1)
		      ->query()
		      ->fetchColumn();
		    
		    // insert if it doesn't exist yet
		    if( !$page_id ) 
		    {
		      // Insert page
		      $db->insert('engine4_core_pages', array(
		        'name' => 'contactimporter_index_import',
		        'displayname' => 'Inviter Home Page',
		        'title' => 'Inviter Home Page',
		        'description' => 'Contact Inviter Home Page',
		        'custom' => 0,
		      ));
		      $page_id = $db->lastInsertId();
		      
		      // Insert top
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'top',
		        'page_id' => $page_id,
		        'order' => 1,
		      ));
		      $top_id = $db->lastInsertId();
		      
		      // Insert main
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'main',
		        'page_id' => $page_id,
		        'order' => 2,
		      ));
		      $main_id = $db->lastInsertId();
		      
		      // Insert top-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_id,
		      ));
		      $top_middle_id = $db->lastInsertId();
		      
		      
			  // Insert main-right
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'right',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_id,
		        'order' => 1,
		      ));
		      $main_right_id = $db->lastInsertId();
			  
			  // Insert main-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_id,
		        'order' => 2,
		      ));
		      $main_middle_id = $db->lastInsertId();
			  
		      // Insert menu
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.menu',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_middle_id,
		        'order' => 1,
		      ));
		      
		      // Insert content
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'core.content',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 1,
		      ));
		      
			  // Insert statistics
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.statistics',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_right_id,
		        'order' => 1,
		        'params' => '{"title":"Inviter Statistics"}',
		      ));
			  
			  // Insert statistics
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.top-inviters',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_right_id,
		        'order' => 2,
		        'params' => '{"title":"Top Inviters"}',
		      ));
			  
		    }
		}
		
		protected function _addInviterHomQueueEmailPage()
		{
			$db = $this->getDb();
		
		    // Queue email page
		    $page_id = $db->select()
		      ->from('engine4_core_pages', 'page_id')
		      ->where('name = ?', 'contactimporter_index_queue-email')
		      ->limit(1)
		      ->query()
		      ->fetchColumn();
		    
		    // insert if it doesn't exist yet
		    if( !$page_id ) {
		      // Insert page
		      $db->insert('engine4_core_pages', array(
		        'name' => 'contactimporter_index_queue-email',
		        'displayname' => 'Inviter Queue Emails Page',
		        'title' => 'Inviter Queue Emails Page',
		        'description' => 'Inviter Queue Emails Page',
		        'custom' => 0,
		      ));
		      $page_id = $db->lastInsertId();
			  
			  // Insert top
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'top',
		        'page_id' => $page_id,
		        'order' => 1,
		      ));
		      $top_id = $db->lastInsertId();
		      
		      // Insert main
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'main',
		        'page_id' => $page_id,
		        'order' => 2,
		      ));
		      $main_id = $db->lastInsertId();
			  
			  // Insert top-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_id,
		      ));
		      $top_middle_id = $db->lastInsertId();
			  
			  // Insert main-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_id,
		        'order' => 2,
		      ));
		      $main_middle_id = $db->lastInsertId();
			 
			  // Insert menu
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.menu',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_middle_id,
		        'order' => 1,
		      ));
			  
			  // Insert content
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'core.content',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 1,
		      ));
			}
		}
		
		
		protected function _addInviterHomQueueMessagePage()
		{
			$db = $this->getDb();
		
		    // Queue Message page
		    $page_id = $db->select()
		      ->from('engine4_core_pages', 'page_id')
		      ->where('name = ?', 'contactimporter_index_queue-message')
		      ->limit(1)
		      ->query()
		      ->fetchColumn();
		    
		    // insert if it doesn't exist yet
		    if( !$page_id ) {
		      // Insert page
		      $db->insert('engine4_core_pages', array(
		        'name' => 'contactimporter_index_queue-message',
		        'displayname' => 'Inviter Queue Messages Page',
		        'title' => 'Inviter Queue Messages Page',
		        'description' => 'Inviter Queue Messages Page',
		        'custom' => 0,
		      ));
		      $page_id = $db->lastInsertId();
			  
			  // Insert top
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'top',
		        'page_id' => $page_id,
		        'order' => 1,
		      ));
		      $top_id = $db->lastInsertId();
		      
		      // Insert main
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'main',
		        'page_id' => $page_id,
		        'order' => 2,
		      ));
		      $main_id = $db->lastInsertId();
			  
			  // Insert top-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_id,
		      ));
		      $top_middle_id = $db->lastInsertId();
			  
			  // Insert main-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_id,
		        'order' => 2,
		      ));
		      $main_middle_id = $db->lastInsertId();
			 
			  // Insert menu
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.menu',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_middle_id,
		        'order' => 1,
		      ));
			  
			  // Insert content
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'core.content',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 1,
		      ));
			}
		}
		
		protected function _addInviterHomPendingInvitationPage()
		{
			$db = $this->getDb();
		
		    // pending invitations page
		    $page_id = $db->select()
		      ->from('engine4_core_pages', 'page_id')
		      ->where('name = ?', 'contactimporter_index_pending-invitation')
		      ->limit(1)
		      ->query()
		      ->fetchColumn();
		    
		    // insert if it doesn't exist yet
		    if( !$page_id ) {
		      // Insert page
		      $db->insert('engine4_core_pages', array(
		        'name' => 'contactimporter_index_pending-invitation',
		        'displayname' => 'Inviter Pending Invitations Page',
		        'title' => 'Inviter Pending Invitations Page',
		        'description' => 'Inviter Pending Invitations Page',
		        'custom' => 0,
		      ));
		      $page_id = $db->lastInsertId();
			  
			  // Insert top
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'top',
		        'page_id' => $page_id,
		        'order' => 1,
		      ));
		      $top_id = $db->lastInsertId();
		      
		      // Insert main
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'main',
		        'page_id' => $page_id,
		        'order' => 2,
		      ));
		      $main_id = $db->lastInsertId();
			  
			  // Insert top-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_id,
		      ));
		      $top_middle_id = $db->lastInsertId();
			  
			  // Insert main-middle
		      $db->insert('engine4_core_content', array(
		        'type' => 'container',
		        'name' => 'middle',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_id,
		        'order' => 2,
		      ));
		      $main_middle_id = $db->lastInsertId();
			 
			  // Insert menu
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'contactimporter.menu',
		        'page_id' => $page_id,
		        'parent_content_id' => $top_middle_id,
		        'order' => 1,
		      ));
			  
			  // Insert content
		      $db->insert('engine4_core_content', array(
		        'type' => 'widget',
		        'name' => 'core.content',
		        'page_id' => $page_id,
		        'parent_content_id' => $main_middle_id,
		        'order' => 1,
		      ));
			}
		}
		
    }
?>