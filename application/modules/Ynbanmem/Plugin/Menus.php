<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynbanmem
 * @author     YouNet Company
 */

class Ynbanmem_Plugin_Menus {
	
	private $_viewer;
	
	public function showBanMembers() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'manage') ) {
	      return false;
	    }
		return true;
	}
	
    public function addBanMembers() 
    {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'add') ) {
	      return false;
	    }
		return true;
	}
	public function showNotices() 
	{
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'action') ) {
	      return false;
	    }
		return true;
	}
	public function manageUsers() 
	{
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'manage_user') ) {
	      return false;
	    }
		return true;
	}
	
	public function onMenuInitialize_CoreMiniMessages($row)
    {
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !$viewer->getIdentity() )
	    {
	      return false;
	    }
	
	    // Get permission setting
	    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
	    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
	    {
	      return false;
	    }
	
	    $message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
	    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';
	
	    return array(
	      'label' => Zend_Registry::get('Zend_Translate')->_($row->label) . ( $message_count ? ' (' . $message_count .')' : '' ),
	      'route' => 'messages_general',
	      'params' => array(
	        'action' => 'inbox'
	      )
	    );
   }



  // user_profile

  public function sendNotice($row)
  {
	// Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) ) {
      return false;
    }
	 // Get permission setting
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynbanmem', 'action');
    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
    {
      return false;
    }
   
    return array(
      'label' => "Send Notice",
      'icon' => 'application/modules/Messages/externals/images/send.png',
      'route' => 'ynbanmem_general',
      'class' => 'smoothbox',
      'params' => array(
        'action' => 'compose',
        'to' => $subject->getIdentity(),
        'format' => 'smoothbox',
      ),
    );
  }
	public function onMenuInitialize_YnbanmemManageUsers()
	{
		
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		// if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'add') ) {
	      // return false;
	    // }
	    
	     $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'ynbanmem', 'manage_user');
		    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
		    {
		      return false;
		    }
		   
		return array(
      	'route' => 'ynbanmem_general',
      	'params' => array(
      		'controller' => 'manage', 
	      	'action' => 'users',  
	      	       
      		)
    	);
	}
	public function onMenuInitialize_YnbanmemManageIps()
	{
		
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		if (! $viewer || ! $viewer->getIdentity ()) {
			return false;
		}
		// if( !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'add') ) {
	      // return false;
	    // }
		return array(
      	'route' => 'ynbanmem_general',
      	'params' => array(
      		'controller' => 'manage', 
	      	'action' => 'ips',  
	      	       
      		)
    	);
	}
}