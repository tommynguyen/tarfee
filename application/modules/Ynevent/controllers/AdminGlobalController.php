<?php

class Ynevent_AdminGlobalController extends Core_Controller_Action_Admin {
	public function init()
	{
	  	Zend_Registry::set('admin_active_menu', 'ynwiki_admin_main_global');
	}	
	
	public function indexAction(){
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_admin_main', array(), 'ynevent_admin_main_global');
		
		$this -> view -> form = $form = new Ynevent_Form_Admin_Global();		
				
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams()))
		{
			$values = $form -> getValues();

			$settings = Engine_Api::_() -> getApi('settings', 'core');
			foreach ($values as $key => $value)
			{
				
				if($key == 'ynevent_instance')
				{
					$value = round($value,2);
					if($value <= 0)
						$value = 50;					
				}									
				$settings -> setSetting(str_replace('_', '.', $key), $value);
				
			}
						
			$form -> addNotice('Your changes have been saved.');

		}		
	}
}