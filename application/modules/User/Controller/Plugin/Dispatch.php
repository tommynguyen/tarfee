<?php

class User_Controller_Plugin_Dispatch extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		
		$full_name = implode('.', array(
	  		$request -> getModuleName(),
	  		$request -> getControllerName(),
	  		$request -> getActionName()
		));
		
		$view  =  Zend_Registry::get('Zend_View');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!empty($viewer->deactive) && ($full_name != 'user.settings.activate') && ($full_name != 'user.auth.logout')) {
			$url = $view->url(array('controller' => 'settings', 'action' => 'activate'),'user_extended', true);
			header('location:' . $url);
			exit;
		}
		
		$key = 'user_predispatch_url:' . $module . '.' . $controller . '.' . $action;
		if (isset($_SESSION[$key]) && $_SESSION[$key]) {
			$url = $_SESSION[$key];
			header('location:' . $url);
			unset($_SESSION[$key]);
			@session_write_close();
			exit ;
		}
	}
}
