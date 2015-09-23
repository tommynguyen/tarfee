<?php
class Slprofileverify_AdminReasonController extends Core_Controller_Action_Admin {

  public function indexAction() {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_reason');
    $this->view->reasons = Engine_Api::_()->getItemTable('slprofileverify_reason')->fetchAll();
  }
    

  public function addReasonAction()
  {

    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Slprofileverify_Form_Admin_Reason();
    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $values = $form->getValues();
    $reasonTable = Engine_Api::_()->getItemTable('slprofileverify_reason');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $reasonTable->insert(array(
        'description' => $values['description'],
      	'create_date' => new Zend_Db_Expr('NOW()'),
      ));
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('successfully')
    ));
  }

  public function deleteReasonAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->reason_id = $reason_id = $this->_getParam('id');
    $reasonTable = Engine_Api::_()->getDbtable('reasons', 'slprofileverify');
    $reason = $reasonTable->find($reason_id)->current();
    
    if( !$reason ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('successfully')
      ));
    } else {
      $reason_id = $reason->getIdentity();
    }
    
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    
    // Process
    $db = $reasonTable->getAdapter();
    $db->beginTransaction();
    
    try {
      $reason->delete();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('successfully')
    ));
  }

  public function editReasonAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->reason_id = $reason_id = $this->_getParam('id');
    $reasonTable = Engine_Api::_()->getDbtable('reasons', 'slprofileverify');
    $reason = $reasonTable->find($reason_id)->current();
    
    if( !$reason ) {
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    } else {
      $reason_id = $reason->getIdentity();
    }
    
    $form = $this->view->form = new Slprofileverify_Form_Admin_Reason();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($reason);
    
    if( !$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    // Process
    $values = $form->getValues();
    
    $db = $reasonTable->getAdapter();
    $db->beginTransaction();
    
    try {
      $reason->description = $values['description'];
      $reason->create_date = date();
      $reason->save();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh'=> 10,
      'messages' => array('successfully')
    ));
  }
}