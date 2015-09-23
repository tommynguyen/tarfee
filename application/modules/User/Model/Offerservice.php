<?php
class User_Model_Offerservice extends Core_Model_Item_Abstract {
    protected $_type = 'user_offerservice';
    protected $_parent_type = 'user';
    protected $_searchTriggers = false;
	
	public function getTitle() {
		$view = Zend_Registry::get('Zend_View');
		if($this -> title && $this -> service_id == 9)
		{
			return $view->translate($this -> title);
		}
		$service = Engine_Api::_()->getItem('user_service', $this->service_id);
		if ($service) 
		{
			return $view->translate($service->getTitle());
		}
		return $view->translate('Unknown Service');
	}
}
