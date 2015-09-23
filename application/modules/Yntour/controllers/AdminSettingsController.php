<?php


class Yntour_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('yntour_admin_main', array(), 'yntour_admin_main_settings');
  }
  
  public function indexAction()
  {
    $this->view->form = new Yntour_Form_Admin_Global();
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('tours', 'yntour')->getAdapter();
      $db->beginTransaction();
      try {
        $this->view->form->saveValues();
        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        throw $e;
      }
      $this->view->form->addNotice('Your changes have been saved.');
    }

  }
}