<?php
class User_Model_Service extends Core_Model_Item_Abstract {
    protected $_type = 'user_service';
    protected $_searchTriggers = false;
	
	public function getTitle() {
		$view = Zend_Registry::get('Zend_View');
		return $view->translate($this->title);
	}
}
