<?php
class Ynresponsiveevent_Installer extends Engine_Package_Installer_Module 
{
	function onInstall() 
	{
		parent::onInstall();
		$this -> _addEventSearchListingPage();
	}
	/*------ Event Search Listing Page -----*/
	protected function _addEventSearchListingPage() 
	{
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynresponsiveevent_index_event') -> limit(1);
		$info = $select -> query() -> fetch();

		// Add page if it does not exist
		if (empty($info)) 
		{
			$db -> insert('engine4_core_pages', array('name' => 'ynresponsiveevent_index_event', 'displayname' => 'YouNet Responsive Event Search Listing', 'title' => 'Event Search Listing', 'description' => 'Shows search result events.', ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// Add containers
			// Top container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynresponsiveevent.event-search-events', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '', ));

			// Main container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// Main-Right container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 2, 'params' => '', ));
			$right_id = $db -> lastInsertId('engine4_core_content');
			
			// Main-Left container
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'left', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '', ));
			$left_id = $db -> lastInsertId('engine4_core_content');

			// Main-Middle containter
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 3, 'params' => '', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			// Main-Middle Widgets
			// Search events
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynresponsiveevent.event-search-listing', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '', ));

			// Main-Right Widgets
			// News feed
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'activity.feed', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":"What news"}', ));

			// Main-Left Widgets
			// Categories
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynresponsiveevent.event-categories', 'parent_content_id' => $left_id, 'order' => 1, 'params' => '{}', ));
			// Personalize
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynresponsiveevent.event-personalize', 'parent_content_id' => $left_id, 'order' => 2, 'params' => '{}', ));
		}
	}
}
?>