<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 6072 2010-06-02 02:36:45Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Contactimporter_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
      
    $this->_helper->redirector->gotoRoute(array('action' => 'level','controller'=>'settings','module'=>'contactimporter'));
  }
  
  public function levelAction()
  {
   
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('contactimporter_admin_main', array(), 'contactimporter_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Contactimporter_Form_Admin_Level();

    $form->level_id->setValue($level_id);
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

    if( !$this->getRequest()->isPost() )
    {
      if( null !== $level_id)
      {
       $form->populate($permissionsTable->getAllowed('contactimporter', $level_id, array_keys($form->getValues())));
      return;

        //die($settings);
      }

      return;
    }

   // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      
      $permissionsTable->setAllowed('contactimporter', $level_id, $values);
      
      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

  }

  
  
}