<?php
class Ynresponsiveevent_Widget_EventMainMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	if(YNRESPONSIVE_ACTIVE != 'ynresponsive-event')
	{
		return $this -> setNoRender(true);
	}	
    //Logo
    $this->view->logo = $this->_getParam('logo', false);
	$this->view->logo_link = $this->_getParam('logo_link', false);
	$this->view->site_name = $this->_getParam('site_name', false);
	$this->view->site_link = $this->_getParam('site_link', false);
  	
    $this->view->navigationMain = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('core_main');
    
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $require_check = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.browse', 1);
    
    if(!$require_check && !$viewer->getIdentity()){
      $navigation->removePage($navigation->findOneBy('route','user_general'));
    }
  }
  public function getCacheKey()
  {
  }
}