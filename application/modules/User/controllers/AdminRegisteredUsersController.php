<?php
class User_AdminRegisteredUsersController extends Core_Controller_Action_Admin {
	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('referral_admin_main', array(),'referral_admin_main_registeredusers');
		$tableInvite = Engine_Api::_() -> getDbTable('invites', 'invite');
		$select = $tableInvite -> select() -> where("user_id <> 0");
		$page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}

}
