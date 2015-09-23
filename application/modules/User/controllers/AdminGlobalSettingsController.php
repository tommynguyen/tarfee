<?php
class User_AdminGlobalSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('player_admin_main', array(), 'player_admin_main_globalsettings');
  }
  public function indexAction()
  {
  	// Make form
    $this->view->form = $form = new User_Form_Admin_Player_Global();

    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();
	// Okay, save
    foreach( $values as $key => $value ) {
      Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
    }
    $form->addNotice('Your changes have been saved.');
  }
}
