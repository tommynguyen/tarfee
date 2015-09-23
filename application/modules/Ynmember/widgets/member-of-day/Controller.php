<?php
class Ynmember_Widget_MemberOfDayController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
		$limit = $this->_getParam('itemCountPerPage', 3);
		// Don't render this if friendships are disabled
	    if( !Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible ) {
	      return $this->setNoRender();
	    }
		
		$tableUser = Engine_Api::_() -> getItemTable('user');
		$select = $tableUser -> select() -> where('member_of_day = 1') -> limit(1);
		$user = $tableUser -> fetchRow($select);
		
		if(!$user)
		{
			return $this->setNoRender();
		}
		// Load fields view helpers
		$view = $this->view;
		$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> user = $user;		
	}
}