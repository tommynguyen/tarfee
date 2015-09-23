<?php
class Ynevent_Widget_ListingEventsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params = $request -> getParams();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$ids = array ();
		// Do the show thingy
		if (isset($params ['by_authors'] ) && in_array('networks', $params['by_authors'])) 
		{
			// Get an array of user ids
			
			$network_table = Engine_Api::_()->getDbtable('membership', 'network');
      		$network_select = $network_table->select()->where('user_id = ?', $viewer -> getIdentity());
      		$networks = $network_table->fetchAll($network_select);
			foreach($networks as $network)
			{
				$network_select = $network_table->select()->where('resource_id = ?', $network -> resource_id) -> where("active = 1");
      			$users = $network_table->fetchAll($network_select);
				foreach ( $users as $user ) {
					$ids [] = $user->user_id;
				}
			}
			
		}
		if (isset($params ['by_authors'] ) && in_array('professional', $params['by_authors'])) 
		{
			$userIds = Engine_Api::_() -> user() -> getProfessionalUsers();
			foreach ($userIds as $id) {
				$ids [] = $id;
			}
		}
		
		$params ['users'] = $ids;
		
		// Get paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('event') -> getEventPaginator($params);
		$paginator -> setCurrentPageNumber($request -> getParam('page'));
	}
}
?>