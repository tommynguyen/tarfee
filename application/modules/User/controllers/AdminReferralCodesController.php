<?php
class User_AdminReferralCodesController extends Core_Controller_Action_Admin {
	public function generatedAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('referral_admin_main', array(),'referral_admin_main_referralcodes');
		$tableInvite = Engine_Api::_() -> getDbTable('invites', 'invite');
		$select = $tableInvite -> select();
		$page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}

}
