<?php

class Ynevent_AdminFieldsController extends Fields_Controller_AdminAbstract {
	protected $_fieldType = 'event';

	protected $_requireProfileType = false;

	public function indexAction() {
		// Make navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_fields');
		parent::indexAction();
	}

	public function fieldCreateAction() {
		parent::fieldCreateAction();
		// remove stuff only relavent to profile questions
		$form = $this -> view -> form;
		if ($form) {
			//$form->removeElement('search');
			//$form->removeElement('display');
			//$form->removeElement('show');
		}
	}

	public function fieldEditAction() {
		parent::fieldEditAction();
		// remove stuff only relavent to profile questions
		$form = $this -> view -> form;

		if ($form) {
			//$form->removeElement('search');
			//$form->removeElement('display');
			//$form->removeElement('show');
		}
	}

}
?>
