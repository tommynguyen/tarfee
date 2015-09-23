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
class Ynnotification_AdminSoundsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynnotification_admin_main', array(), 'ynnotification_admin_main_sounds');
  }
  public function indexAction()
  {
  	$translate = Zend_Registry::get('Zend_Translate');
  	$this->view->form = $form = new Ynnotification_Form_Admin_Sound();			   
  	if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
  	{
  		//$sound_str = 'sound_alert';  		
  		
  		$values = $form->getValues(); 
		
  		if(!empty($values['sound'])) 
  		{
  			$desmp3 = sprintf($translate->translate("Current file: %s"),$values['sound']);
			  	
  			Engine_Api::_()->getApi('settings', 'core')->setSetting('ynnotification.sound.alert', $values['sound']);
			$form->getElement("sound")->setDescription($desmp3);
		}
		
		if(!empty($values['sound_wav'])) 
  		{
  			$deswav = sprintf($translate->translate("Current file: %s"),$values['sound_wav']);
			  			
  			Engine_Api::_()->getApi('settings', 'core')->setSetting('ynnotification.sound.wav.alert', $values['sound_wav']);
			$form->getElement("sound_wav")->setDescription($deswav);
		}
		
		
		Engine_Api::_()->getApi('settings', 'core')->setSetting('ynnotification.sound.setting', $values['ynnotification_sound_setting']);
		
  		
  		$form->addNotice('Your changes have been saved.');		
		
  	}
  }
 
}
