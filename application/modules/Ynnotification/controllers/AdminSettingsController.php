<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Adv Notification
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminSettingsController.php
 * @author     Luan Nguyen
 */
class Ynnotification_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynnotification_admin_main', array(), 'ynnotification_admin_main_settings');
  }
  public function indexAction()
  {
  	$this->view->form = $form = new Ynnotification_Form_Admin_Global();
  	if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
  	{
  		$values = $form->getValues();  		
  		
  		foreach ($values as $key => $value){
  			if($key=='ynnotification_photo_notification')
			{
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
			}
			
			else {
				Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value*1000);
			}
  			
  		}
  		
  		$form->addNotice('Your changes have been saved.');
  	}
  }
 
}
