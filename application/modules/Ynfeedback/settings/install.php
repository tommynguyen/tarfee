<?php
class Ynfeedback_Installer extends Engine_Package_Installer_Module {
    public function onInstall() {
    	$this -> _addHomePage();
        $this -> _addCreatePage();
		$this -> _addEditPage();
		$this -> _addManageScreenshotsPage();
		$this -> _addManageFilesPage();
        $this -> _addListingPage();
        $this -> _addManagePage();
        $this -> _addManageFollowPage();
        $this -> _addDetailPage();
		parent::onInstall();
    }
    
	protected function _addHomePage() 
	{
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_index_index')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_index_index',
                'displayname' => 'Feedback Home Page',
                'title' => 'Feedback Home Page',
                'description' => 'Feedback Home Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            
            //Insert feedback-search
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert feedback quick menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.browse-menu-quick',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
            
            //Insert most-voted-feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.most-voted-feedback',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Most Voted Feedback"}',
            ));
            
            //Insert most-liked-feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.most-liked-feedback',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 4,
                'params' => '{"title":"Most Liked Feedback"}',
            ));
            
            //Insert most-discussed-feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.most-discussed-feedback',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 5,
                'params' => '{"title":"Most Discussed Feedback"}',
            ));
            
            //Insert most-followed-feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.most-followed-feedback',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 6,
                'params' => '{"title":"Most Followed Feedback"}',
            ));
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
            //Insert view poll
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.view-poll',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            )); 
            
            //Insert highlight Feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.highlight-feedback',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
                'params' => '{"title":"Highlight Feedback","itemCountPerPage":8}',
            )); 
            
            //Insert categories feedback
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.middle-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 3,
                'params' => '{"title":"Browse by Category","itemCountPerPage":9}',
            )); 
            
            //Insert listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 4,
            	'params' => '{}',
            ));
        }
    }
    
    protected function _addDetailPage()
    {
    	$db = $this->getDb();
    	$page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_idea_view')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
    	if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_idea_view',
                'displayname' => 'Feedback Detail Page',
                'title' => 'Feedback Detail Page',
                'description' => 'Feedback Detail Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));     

            //Insert comment
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-comment',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 2,
            ));
        }		          
    }
    
	protected function _addManageFilesPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_idea_manage-files')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_idea_manage-files',
                'displayname' => 'Feedback Manage Files Page',
                'title' => 'Feedback Manage Files Page',
                'description' => 'Feedback Manage Files Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert photo
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-photo',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            )); 
            
            //Insert options
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-options',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));                   
        }
    }
	
	protected function _addManageScreenshotsPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_idea_manage-screenshots')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_idea_manage-screenshots',
                'displayname' => 'Feedback Manage Screenshots Page',
                'title' => 'Feedback Manage Screenshots Page',
                'description' => 'Feedback Manage Screenshots Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert photo
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-photo',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            )); 
            
            //Insert options
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-options',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));                         
        }
    }
	
	protected function _addEditPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_idea_edit')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_idea_edit',
                'displayname' => 'Feedback Edit Page',
                'title' => 'Feedback Edit Page',
                'description' => 'Feedback Edit Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-left
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_left_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
			
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));     
			
			//Insert Feedback Profile Photo
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-photo',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 1,
            ));
            
            //Insert Feedback Profile Options
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.profile-options',
                'page_id' => $page_id,
                'parent_content_id' => $main_left_id,
                'order' => 2,
            ));                    
        }
    }
	
    protected function _addCreatePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_index_create')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_index_create',
                'displayname' => 'Feedback Create Page',
                'title' => 'Feedback Create Page',
                'description' => 'Feedback Create Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));                         
        }
    }
	
    protected function _addListingPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_index_listing')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_index_listing',
                'displayname' => 'Feedback Listing Page',
                'title' => 'Feedback Listing Page',
                'description' => 'Feedback Listing Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert listing widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-listing',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert menu quick widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.browse-menu-quick',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));   
            //Insert browse by categories widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.right-categories',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
                'params' => '{"title":"Browse by Category","itemCountPerPage":10}',
            ));                                    
        }
    }

    protected function _addManagePage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_index_manage')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_index_manage',
                'displayname' => 'Feedback Manage Page',
                'title' => 'Feedback Manage Page',
                'description' => 'Feedback Manage Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
            
            //Insert menu quick widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.browse-menu-quick',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));                           
        }
    }

    protected function _addManageFollowPage() {
        $db = $this->getDb();
        
        $page_id = $db->select()
            ->from('engine4_core_pages', 'page_id')
            ->where('name = ?', 'ynfeedback_index_manage-follow')
            ->limit(1)
            ->query()
            ->fetchColumn();
            
        if(!$page_id) {
            $db->insert('engine4_core_pages', array(
                'name' => 'ynfeedback_index_manage-follow',
                'displayname' => 'Feedback Manage Follow Page',
                'title' => 'Feedback Manage Follow Page',
                'description' => 'Feedback Manage Follow Page',
                'custom' => 0
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
            
            //Insert main
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => 2,
            ));
            $main_id = $db->lastInsertId();
            
            //Insert top-middle
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
            
            //Insert main-right
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_id,
                'order' => 1,
            ));
            $main_right_id = $db->lastInsertId();
            
            //Insert menu
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.main-menu',
                'page_id' => $page_id,
                'parent_content_id' => $top_middle_id,
                'order' => 1,
            ));
            
            //Insert content
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'core.content',
                'page_id' => $page_id,
                'parent_content_id' => $main_middle_id,
                'order' => 1,
            ));
            
            //Insert manage menu widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-manage-menu',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 1,
            ));
            
            //Insert search widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.feedback-search',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 2,
            ));
            
            //Insert menu quick widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'ynfeedback.browse-menu-quick',
                'page_id' => $page_id,
                'parent_content_id' => $main_right_id,
                'order' => 3,
            ));                           
        }
    }
}