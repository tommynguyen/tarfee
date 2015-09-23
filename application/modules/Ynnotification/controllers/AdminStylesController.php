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
class Ynnotification_AdminStylesController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynnotification_admin_main', array(), 'ynnotification_admin_main_styles');
  }
  public function indexAction()
  {
    $this->view->form = $form = new Ynnotification_Form_Admin_Style();
	$values = Engine_Api::_()->getApi('settings', 'core')->getSetting('avdnotification.customcssobj', 0);
	$values = Zend_JSON::decode($values);
	if($values)
		$form->populate($values);
    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
	     $values = $form->getValues();
		 if(isset($_POST['submit']))
		 {
		    // $str = $this->view->partial("_css.tpl");
			 $arr_keys = array_keys($values);
			 $arr_values = array_values($values);
			 $arr_keys = array_map(array($this ,'map'), $arr_keys);
			 $str = str_replace($arr_keys, $arr_values, $str);
			 Engine_Api::_()->getApi('settings', 'core')->setSetting('avdnotification.customcssobj', Zend_JSON::encode($values));
			 Engine_Api::_()->getApi('settings', 'core')->setSetting('avdnotification', $str);
		     $form->addNotice('Your changes have been saved.');
		 }
		else if(isset($_POST['clear']))
		{
			Engine_Api::_()->getApi('settings', 'core')->setSetting('avdnotification.customcssobj', '');
			Engine_Api::_()->getApi('settings', 'core')->setSetting('avdnotification', '');
			$form->populate(array('mes_background' => '79B4D4',
								'text_color'=> '',
								));
			$form->addNotice('You have set default styles.');

		}
    }
  }
  static public function map($a)
  {
  		return "[{$a}]";
  }
}
