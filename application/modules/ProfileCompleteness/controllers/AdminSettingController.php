<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ProfileCompleteness_AdminSettingController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('profilecompleteness_admin_main', array(), 'profilecompleteness_admin_main_setting');

        $this->view->form = $form = new ProfileCompleteness_Form_Admin_Manage_Setting();

        $table = Engine_Api::_()->getDbtable('weights', 'profileCompleteness');
		$values = array();
		
		// get profile photo weight
        $values['usernameweight'] = $table -> getGlobalWeight(-4);
		
		// get profile photo weight
        $values['photoweight'] = $table -> getGlobalWeight(0);
		
		// get sport like/follow weight
        $values['sportlikeweight'] = $table -> getGlobalWeight(-1);
		
		// get club follow weight
        $values['clubfollowweight'] = $table -> getGlobalWeight(-2);
		
		// get video upload weight
        $values['videouploadweight'] = $table -> getGlobalWeight(-3);
		
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('w' => 'engine4_profilecompleteness_settings'));
        $row = $table->fetchRow($select);
        $values['view'] = $row->view;
        $values['color'] = $row->color;
        $form->populate($values);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) 
        {
            $table = Engine_Api::_()->getDbtable('weights', 'profileCompleteness');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try 
            {
            	$values = $form->getValues();
				
				$table -> setGlobalWeight(0, $values['photoweight']);
				$table -> setGlobalWeight(-1, $values['sportlikeweight']);
				$table -> setGlobalWeight(-2, $values['clubfollowweight']);
				$table -> setGlobalWeight(-3, $values['videouploadweight']);
				$table -> setGlobalWeight(-4, $values['usernameweight']);
				
                $select = $table->select()->setIntegrityCheck(false);
                $table = Engine_Api::_()->getDbtable('settings', 'profileCompleteness');
                $select->from(array('w' => 'engine4_profilecompleteness_settings'));
                $row = $table->fetchRow($select);
                $row->color = $values['color'];
                $row->view = $values['view'];
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                throw $e;
            }
            $form->addNotice('Your changes have been saved.');
        }
    }
}

?>
