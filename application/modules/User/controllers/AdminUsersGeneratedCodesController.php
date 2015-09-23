<?php
class User_AdminUsersGeneratedCodesController extends Core_Controller_Action_Admin {
	public function indexAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('referral_admin_main', array(),'referral_admin_main_generatedusers');
		$tableInvite = Engine_Api::_() -> getDbTable('invites', 'invite');
		$select = $tableInvite -> select() -> distinct() -> from($tableInvite->info('name'), $tableInvite->info('name').".user_id" );
		$page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}
	

}
