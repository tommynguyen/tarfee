<?php
class Tfcampaign_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('tfcampaign_admin_main', array(), 'tfcampaign_admin_settings_global');
         
        // Make form
	    $settings = Engine_Api::_()->getApi('settings', 'core');
	     $this->view->form = $form = new Tfcampaign_Form_Admin_Settings_Global();
	     if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
	        $values = $form->getValues();
	        foreach ($values as $key => $value) {
	            $settings->setSetting($key, $value);
	        }
	        $form->addNotice('Your changes have been saved.'); 
	    }
    }
    
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('tfcampaign_admin_main', array(), 'tfcampaign_admin_settings_level');

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
        $this->view->form = $form = new Tfcampaign_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('tfcampaign_campaign', $id, array_keys($form->getValues())));
        
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
            
            $checkArr = array('auth_view');
            foreach ($checkArr as $check) {
                if(empty($values[$check])) {
                    unset($values[$check]);
                    $form->$check->setValue($permissionsTable->getAllowed('tfcampaign_campaign', $id, $check));
                }
            }
            try {
                $permissionValues = $values;
                $permissionsTable->setAllowed('tfcampaign_campaign', $id, $permissionValues);
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
                $permissionsTable->setAllowed('tfcampaign_campaign', $id, $values);
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