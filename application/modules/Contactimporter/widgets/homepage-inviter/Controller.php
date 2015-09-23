<?php
class Contactimporter_Widget_HomepageInviterController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {   
		if($this->_getParam('max') != '')
		{       
			$limit = $this->_getParam('max');
			if ($limit <=0)
			{
				$limit = 10;
			}
		}
		else
		{
			$limit = 10; 
		}
        $table = Engine_Api::_()->getDbtable('providers', 'Contactimporter');
        $select = $table->select();
        $select->where('enable = ?', 1 )
        		-> order('order', 'ASC')
				->limit($limit);
        $services = $table->fetchAll($select);
        $this->view->step = $step = "get_contact";
        $this->view->providers = $services;
		
		// get facebook API
		if (Engine_Api::_() -> hasModuleBootstrap('socialbridge'))
		{
			$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
			$select = $apiSetting->select()->where('api_name = ?', 'facebook');
			$provider = $apiSetting->fetchRow($select);
			if($provider)
			{
				$api_params = unserialize($provider -> api_params);
				$this -> view -> facebookAPI = $api_params['key'];
			}
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$link = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $viewer->getIdentity()), 'contactimporter_ref');
		$this -> view -> invite_link = $link;
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> default_message = $settings -> getSetting('invite.message');
    }
}
