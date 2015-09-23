<?php
class Ynfeedback_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_settings_global');
         $settings = Engine_Api::_()->getApi('settings', 'core');
         $this->view->form = $form = new Ynfeedback_Form_Admin_Settings_Global();
         
         $public_id = 5;
         $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
         $form->populate($permissionsTable->getAllowed('ynfeedback_idea', $public_id, array_keys($form->getValues())));
         
         if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $settings->setSetting('ynfeedback_guest_merge', $values['ynfeedback_guest_merge']);
            $settings->setSetting('ynfeedback_max_idea', $values['ynfeedback_max_idea']);
			$settings->setSetting('ynfeedback_popup_style', $values['ynfeedback_popup_style']);
            $permissionValues = array_slice($values, 0, 2);
            $permissionsTable->setAllowed('ynfeedback_idea', $public_id, $permissionValues);
            
            $form->addNotice('Your changes have been saved.'); 
        }
    }
    
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_settings_level');

        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } 
        else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
    
        // Make form
        $this->view->form = $form = new Ynfeedback_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        $form->populate($permissionsTable->getAllowed('ynfeedback_idea', $id, array_keys($form->getValues())));
        
        if ($level->type != 'public') {
            $numberFieldArr = Array('max_screenshot', 'max_file', 'max_screenshotsize', 'max_filesize');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynfeedback_idea', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynfeedback_idea')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
            	$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
				
                $select = $typeTbl->select()->where('module = ?', 'ynfeedback')->where('action_type = ?', 'ynfeedback_new')->limit(1);
                $type = $typeTbl -> fetchRow($select);
                
                if(!$type) {
                    $type = $typeTbl->createRow();
                    $type->module = 'ynfeedback';
                    $type->action_type = 'ynfeedback_new';
                    $type->group = 'earn';
                    $type->content = 'Creating %s feedbacks';
                    $type->credit_default = 5;
                    $type->link_params = '{"route":"ynfeedback_general","action":"create"}';
                    $type->save();
                }
				
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type->type_id)
                    ->limit(1);
                $credit = $creditTbl->fetchRow($select);
                if(!$credit) {
                    $credit = $creditTbl->createRow();
                }
                else 
                {
                    $form->first_amount->setValue($credit->first_amount);
                    $form->first_credit->setValue($credit->first_credit);
                    $form->credit->setValue($credit->credit);
                    $form->max_credit->setValue($credit->max_credit);
                    $form->period->setValue($credit->period);
                } 
            }
        }

        // Check post
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        // Check validitiy
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        // Process
        if ($level->type != 'public') {
            
            $checkArr = array('auth_view', 'auth_comment');
            foreach ($checkArr as $check) {
                if(empty($values[$check])) {
                    unset($values[$check]);
                    $form->$check->setValue($permissionsTable->getAllowed('ynfeedback_idea', $id, $check));
                }
            }
            
            try {
                //credit
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $creditValues = array_slice($values, 0, 5);
                    $permissionValues = array_slice($values, 5);
                    $credit->level_id = $id;
                    $credit->type_id = $type->type_id;
                    $credit->setFromArray($creditValues);
                    $credit->save();
                }
                else {
                    $permissionValues = $values;
                }
                $permissionsTable->setAllowed('ynfeedback_idea', $id, $permissionValues);
                 // Commit
                $db->commit();
            }
        
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        else {
            try {
                
                $permissionsTable->setAllowed('ynfeedback_idea', $id, $values);
                $db->commit();
            }
            
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        
        $form->addNotice('Your changes have been saved.'); 
    }
}