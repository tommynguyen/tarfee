<?php

class Slprofileverify_AdminSettingController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_setting');

        $this->view->form = $form = new Slprofileverify_Form_Admin_Setting();
        $settingsCore = Engine_Api::_()->getApi('settings', 'core');

        // get photo badge
        $photo_badge = $settingsCore->getSetting('sl_verify_badge', 0);
        $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($photo_badge, null, 'pBadge');
        // end
        // Form submit
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                // Setting group member
                $aMemberVerified = "";
                $aMemberUnverified = "";
                if (is_array($values['member_verified']) && is_array($values['member_unverified'])) {
                    $aMemberVerified = Zend_Json::encode($values['member_verified']);
                    $aMemberUnverified = Zend_Json::encode($values['member_unverified']);
                }

                $settingsCore->setSetting('group_member_verified', $aMemberVerified);
                $settingsCore->setSetting('group_member_unverified', $aMemberUnverified);
                // Add photo
                if (!empty($values['badge'])) {
                    $photo_id = Engine_Api::_()->slprofileverify()->setPhotoVerification($form->badge, 'slprofileverify', 'pBadge');
                    if (!$photo_id) {
                        $photo_id = 0;
                    }
                    $settingsCore->setSetting('sl_verify_badge', $photo_id);
                    $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($photo_id, null, 'pBadge');
                }

                // commit
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $form->addNotice("Your changes have been saved.");
        }

        // Get group member mapping
        $sGroupVerified = $settingsCore->getSetting('group_member_verified', null);
        $this->view->aGroupVerified = $aGroupVerified = Zend_Json::decode($sGroupVerified);
        $sGroupUnverified = $settingsCore->getSetting('group_member_unverified', null);
        $this->view->aGroupUnverified = $aGroupUnverified = Zend_Json::decode($sGroupUnverified);

        // member mapping
        $aMemberVerified = array();
        $aMemberUnverified = array();
        $arrRemove = array("admin", "moderator", "public");
        $oAuthLevelTbl = Engine_Api::_()->getItemTable('authorization_level');
        $sSelectVerified = $oAuthLevelTbl->select()->where('type NOT IN(?)', $arrRemove);
        foreach ($oAuthLevelTbl->fetchAll($sSelectVerified) as $level) {
            if (!in_array($level->level_id, $aGroupVerified)) {
                $aMemberVerified[$level->level_id] = $level->getTitle();
            }
        }
        $this->view->aMemberVerified = $aMemberVerified;

        $sSelectUnverified = $oAuthLevelTbl->select()->where('type NOT IN(?)', $arrRemove);
        foreach ($oAuthLevelTbl->fetchAll($sSelectUnverified) as $level) {
            if (!in_array($level->level_id, $aGroupUnverified)) {
                $aMemberUnverified[$level->level_id] = $level->getTitle();
            }
        }
        $this->view->aMemberUnverified = $aMemberUnverified;
    }

    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_level');

        // Get level id
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }
        $id = $level->level_id;

        // Make form
        $this->view->form = $form = new Slprofileverify_Form_Admin_Settings_Level();
        $form->level_id->setValue($id);

        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');

        $form->populate($permissionsTable->getAllowed('slprofileverify', $id, array_keys($form->getValues())));

        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $permissionsTable->setAllowed('slprofileverify', $id, $values);
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice("Your changes have been saved.");
    }

}