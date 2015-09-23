<?php
class Ynfeedback_AdminStatusController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_main_status');
    }
        
    public function indexAction() {
        $table = Engine_Api::_()->getDbTable('status', 'ynfeedback');
        $statusList = $table->fetchAll($table->select());
        $this->view->paginator = $statusList;
    }
    
    public function createAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->form = $form = new Ynfeedback_Form_Admin_Status_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
        	$form -> color -> setValue('<input value="'. $form->getValue('color') .'" type="color" id="color" name="color"/>');
            return;
        }
        
        $success = FALSE;
        
        $values = $form->getValues();
        
        $db = Engine_Api::_()->getDbtable('status', 'ynfeedback')->getAdapter();
        $db->beginTransaction();
        
        try {
            $table = Engine_Api::_()->getDbtable('status', 'ynfeedback');
            $status = $table->createRow();
            $status->title = $values['title'];
			$status->color = $this ->_getParam('color');
            $status->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => true, 
                'messages' => 'Add Status sucessful.'));
        }
    }
    
    public function editAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id', null);
        if (!$id) return;
        $status = Engine_Api::_()->getItem('ynfeedback_status', $id);
        if (!$status) return;
        $this->view->form = $form = new Ynfeedback_Form_Admin_Status_Edit();
        $form->title->setValue($status->title);
		$form -> color -> setValue('<input value="'. $status -> color .'" type="color" id="color" name="color"/>');
        if(!$this->getRequest()->isPost()) {
            return;
        }
        
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $success = FALSE;
        
        $values = $form->getValues();
        
        $db = Engine_Api::_()->getDbtable('status', 'ynfeedback')->getAdapter();
        $db->beginTransaction();
        
        try {
            $status->title = $values['title'];
			$status->color = $this ->_getParam('color');
            $status->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        if ($success) {
            return $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true, 
                'parentRefresh' => true, 
                'messages' => 'Edit Status sucessful.'));
        }
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        if ($id <= 1) {
            return;
        }
        
        $this->view->form = $form = new Ynfeedback_Form_Admin_Status_Delete();
        $list = Engine_Api::_()->getItemTable('ynfeedback_status')->getStatusList();
        unset($list[$id]);
        $form->move_status->addMultiOptions($list);
        $form->id->setValue($id);
        // Check post
        if( $this->getRequest()->isPost()) {
            
            if( !$form->isValid($this->getRequest()->getPost()) ) {
                return;
            }
            
            $values = $form->getValues();
            
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                Engine_Api::_()->getItemTable('ynfeedback_idea')->changeStatus($id, $values['move_status']);
                $status = Engine_Api::_()->getItem('ynfeedback_status', $id);
                $status->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This status has been deleted.')
            ));
        }
    }
}
