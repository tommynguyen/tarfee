<?php

class Ynevent_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{

	public function indexAction()
	{
		// Get navigation
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		$p = Zend_Controller_Front::getInstance() -> getRequest() -> getParams();
		$filter = !empty($p['filter']) ? $p['filter'] : '';
		if($controller == 'index' && $action == 'calendar')
		{
			$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main',  array(), 'ynevent_main_manage');
		}
		else if($filter == 'past' && empty($p['start_date']) && empty($p['end_date']))
		{
			$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main',  array(), 'ynevent_main_past');
		}
		else if($filter == 'future' && empty($p['start_date']) && empty($p['end_date']))
		{
			$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main',  array(), 'ynevent_main_upcoming');
		}
		else
		{
			$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main');
		}
		
		if(!isset($p['parent_type']))
			$p['parent_type'] = '';
		$this -> view -> parent_type = $p['parent_type'];

	}

}
