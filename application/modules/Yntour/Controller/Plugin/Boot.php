<?php

class Yntour_Controller_Plugin_Boot extends Zend_Controller_Plugin_Abstract
{

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
    	$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		{
			return;
		}
        $module =  $request->getModuleName();
        $controlelr =$request->getControllerName();
        $action =  $request->getActionName();
        
        // global_page_socialstore-product-index
        $bodyId = 'global_page_'. $module. '-'. $controlelr . '-'. $action;
        $path =  $request->getPathInfo();
        $result =  Engine_Api::_()->yntour()->getTour($path, $bodyId);
        $enabled =  isset($result['enable'])? $result['enable']: false;
        Zend_Registry::set('YNTOUR_ENABLED', $enabled);
        $view =  Zend_Registry::get('Zend_View');
        $view->headScript()->appendScript('en4.yntour.init('.Zend_Json::encode($result).')');
                    
    }

}
