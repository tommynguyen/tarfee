<?php

class Slprofileverify_IndexController extends Core_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
    }

    // Verification step one
    public function settingVerificationAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $send_verify = Engine_Api::_()->authorization()->isAllowed('slprofileverify', $viewer, 'send');
        if (!isset($send_verify) || $send_verify) {
            $this->view->auth = 1;
        }
        $slverifyTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
        $slverify = $slverifyTbl->getVerifyInfor($viewer->user_id);
        if ($slverify->approval == 'verified' || $viewer->isAdmin()) {
            return $this->_helper->_redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }

        $this->view->form = $form = new Slprofileverify_Form_Verifydocument();

        // Description form
        $this->view->discription = $this->view->translate('GET_ID_VERIFIED_DESCRIPTION', '<a href="javascript:void(0)" id="why-get-verify">', '<a href="javascript:void(0)" id="why-verify-safe">', '</a>');

        // Get profile picture user
        $profile_picture = $viewer->getPhotoUrl('thumb.profile');
        if ($profile_picture) {
            $form->profile_picture->setValue($profile_picture);
        } else {
            $profile_picture = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/User/externals/images/nophoto_user_thumb_profile.png';
        }
        $this->view->profile_picture = $profile_picture;

        $settingCore = Engine_Api::_()->getApi('settings', 'core');
        $enableStep = $settingCore->getSetting('sl_enable_step');
        if (!$enableStep) {
            $form->removeElement('upload');
        } else {
            $form->removeElement('submit');
        }

        // Check form validate
        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $values = $form->getValues();
            $values_file = $form->document->getTransferAdapter()->getFileInfo();
            if (count($values['field'])) {
                $valuesFields = Engine_Api::_()->fields()->getFieldsValues($viewer);
                foreach ($values['field'] as $key => $value) {
                    $parts = explode('_', $key);
                    if (count($parts) != 3)
                        continue;
                    list($parent_id, $option_id, $field_id) = $parts;
                    if (is_array($value)) {
                        $valueRows = $valuesFields->getRowsMatching(array(
                            'field_id' => $field_id,
                            'item_id' => $viewer->user_id,
                        ));

                        foreach ($valueRows as $valueRow) {
                            if (!empty($valueRow->privacy)) {
                                $prevPrivacy = $valueRow->privacy;
                            }
                            $valueRow->delete();
                        }

                        // Insert all
                        $indexIndex = 0;
                        if (is_array($value) || !empty($value)) {
                            foreach ((array) $value as $singleValue) {
                                $valueRow = $valuesFields->createRow();
                                $valueRow->field_id = $field_id;
                                $valueRow->item_id = $viewer->user_id;
                                $valueRow->index = $indexIndex++;
                                $valueRow->value = $singleValue;
                                if ($prevPrivacy) {
                                    $valueRow->privacy = $prevPrivacy;
                                }
                                $valueRow->save();
                            }
                        } else {
                            $valueRow = $valuesFields->createRow();
                            $valueRow->field_id = $field_id;
                            $valueRow->item_id = $viewer->user_id;
                            $valueRow->index = 0;
                            $valueRow->value = '';
                            if ($prevPrivacy) {
                                $valueRow->privacy = $prevPrivacy;
                            }
                            $valueRow->save();
                        }
                    } else {
                        $valueRow = $valuesFields->getRowMatching(array(
                            'field_id' => $field_id,
                            'item_id' => $viewer->user_id,
                            'index' => 0
                        ));

                        if (!$valueRow) {
                            $valueRow = $valuesFields->createRow();
                            $valueRow->field_id = $field_id;
                            $valueRow->item_id = $viewer->user_id;
                        }
                        $valueRow->value = htmlspecialchars($value);
                        $valueRow->save();
                    }
                }

                // Update displayname
                $valuesAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($viewer);
                $viewer->setDisplayName($valuesAlias);
                $viewer->save();

                // Update user slprofile
                //$slUserRow = Engine_Api::_()->getItem('slprofileverify_user', $viewer->user_id);

                $slUserTbl = Engine_Api::_()->getDbTable('users', 'slprofileverify');
                $select = $slUserTbl->select()->from($slUserTbl->info('name'))->where('user_id = ?', $viewer->user_id);
                $slUserRow = $slUserTbl->fetchRow($select);
                if (!$slUserRow) {
                    $insert = $slUserTbl->createRow();
                    $insert->user_id = $viewer->user_id;
                    $insert->value = Zend_Json::encode($values['field']);
                    $insert->save();
                } else {
                    if (count($values['field'])) {
                        $slUserRow->value = Zend_Json::encode($values['field']);
                        $slUserRow->save();
                    }
                }
            }

            // Upload photo
            $aFieldId = array();
            foreach ($values_file as $aValues) {
                if ($aValues['error'] == 0) {
                    $aFieldId[] = Engine_Api::_()->slprofileverify()->setPhotoUserIdentty($aValues);
                }
            }

            // Send request
            $approval = "default";
            if (!$enableStep) {
                $this->_sendmail($viewer);
                $this->_sendmailrequest($viewer);
                $approval = "pending";
            }

            // Update
            $aData = array(
                'approval' => $approval,
                'verified_date' => '0000-00-00 00:00:00',
                'request_date' => new Zend_Db_Expr('NOW()'),
                'file_id' => Zend_Json::encode($aFieldId),
                'file_id_cus' => 0,
                'reason' => null
            );

            if (!$slverify) {
                $aData['user_id'] = $viewer->getIdentity();
                $slverify = $slverifyTbl->createRow();
                $slverify->setFromArray($aData);
                $slverify->save();
            } else {
                $slverify->setFromArray($aData);
                $slverify->save();
            }

            $form->addNotice($this->view->translate("Document are uploaded successfully!"));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        if ($enableStep) {
            return $this->_helper->_redirector->gotoRoute(array('module' => 'slprofileverify', 'controller' => 'index', 'action' => 'custom-verification'), null, true);
        }

        $format = $this->getSuccessfully();
        $this->view->headScript()->appendScript($format);
    }

    // Verification step two
    public function customVerificationAction() {

        $viewer = Engine_Api::_()->user()->getViewer();
        // Check user is being verified
        $slVerifyTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
        $slVerify = $slVerifyTbl->getVerifyInfor($viewer->user_id);
        $settingCore = Engine_Api::_()->getApi('settings', 'core');
        if ($slVerify->approval == 'verified' || !$settingCore->sl_enable_step || $viewer->isAdmin()) {
            return $this->_helper->_redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }

        // Create form
        $this->view->form = $form = new Slprofileverify_Form_Verifycustom();
        $form->setTitle($settingCore->sl_step_name);
        $form->setDescription($settingCore->sl_exp_step);
        $form->getDecorator('Description')->setOptions(array('tag' => 'div', 'id' => 'exp-step', 'escape' => false));
        $form->document->setDescription('Explanation Document');
        $form->document->getDecorator('Description')->setOptions(array('tag' => 'div', 'escape' => false));

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                // Update
                $form->saveValues();
                $values_file = $form->document->getTransferAdapter()->getFileInfo();
                // Upload photo
                $aFieldId = array();
                foreach ($values_file as $aValues) {
                    if ($aValues['error'] == 0) {
                        $aFieldId[] = Engine_Api::_()->slprofileverify()->setPhotoUserIdentty($aValues);
                    }
                }

                $slVerify->file_id_cus = Zend_Json::encode($aFieldId);
                $slVerify->approval = 'pending';
                $slVerify->verified_date = '0000-00-00 00:00:00';
                $slVerify->request_date = new Zend_Db_Expr('NOW()');
                $slVerify->save();
                $this->_sendmail($viewer);
                $this->_sendmailrequest($viewer);
                if ($form->getSubForm('fields')->getElement('submit')) {
                    $form->getSubForm('fields')->removeElement('submit');
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $format = $this->getSuccessfully();
            $this->view->headScript()->appendScript($format);
        }
    }

    public function ajaxAction() {
        $option_id = $this->_getParam('option_id');

        // Array photo
        $arrFileID = array(array(0, 1), array(1, 1), array(2, 1), array(3, 1));
        $arrImage = array();
        $arrEnable = array(0, 1, 2, 3);

        if ($option_id) {
            $customTbl = Engine_Api::_()->getDbTable('customs', 'slprofileverify');
            $customRow = $customTbl->getCustomRow($option_id);
            if ($customRow->image) {
                $arrFileID = Zend_Json::decode($customRow->image);
                $arrEnable = array();
                foreach ($arrFileID as $key => $arrValues) {
                    $arrImage[] = $arrFileID[$key][0];
                    if ($arrValues[1] == 1) {
                        $arrEnable[] = $key;
                    }
                }
            }
        }

        $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoIdentityUrl($arrImage, null, 'custom');
        $this->view->ena_img = $arrEnable;
        $this->view->exp_document = $customRow->exp_document;
        $this->view->image_number = ($customRow->image_number) ? $customRow->image_number : 1;

        $this->_helper->layout->disableLayout();
    }

    public function _sendmail($user) {
        // Send mail
        $mailType = 'slprofileverify_sending_verify';
        $mailParams = array(
        );
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, $mailType, $mailParams);
    }

    public function _sendmailrequest($user) {
        $userTbl = Engine_Api::_()->getItemTable('user');
        $select = $userTbl->select()
                ->from($userTbl->info('name'), array('email'))
                ->join('engine4_authorization_levels', 'engine4_authorization_levels.level_id = engine4_users.level_id', array())
                ->where('type IN(?)', array('admin'));
        $user_admins = $userTbl->fetchAll($select);
        foreach ($user_admins->toArray() as $admin) {
            $mailType = 'slprofileverify_request';
            $mailParams = array(
                'host' => $_SERVER['HTTP_HOST'],
                'sender_title' => $user->getTitle(),
                'sender_link' => $user->getHref(),
            );
            Engine_Api::_()->getApi('mail', 'core')->sendSystem($admin, $mailType, $mailParams);
        }
    }

    public function getSuccessfully() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $body = '' .
                '<div>' .
                '<h3>' . $this->view->translate("Thank you") . '</h3>' .
                '<p>' . $this->view->translate("DOCUMENT_UPLOAD_SUCCESSFULLY") . '</p>' .
                '<p>' .
                '<button type="button" onclick="javascript:parent.Smoothbox.close()">' . $this->view->translate("Okay") . '</button>' .
                '</p>' .
                '</div>';
        $format = "" .
                'window.addEvent("domready", function(){ ' .
                "Smoothbox.open(" . Zend_Json::encode(str_replace(array("\r\n", "\n", "\r"), "", $body)) . ",{onClose:function(){window.location.href='" . $viewer->getHref() . "'}});" .
                "var height = $('TB_ajaxContent').get('height').toInt() - 100;" .
                "$('TB_ajaxContent').setStyle('height', height + 'px');" .
                "$('TB_ajaxContent').setStyle('padding-bottom', '12px');" .
                "$('TB_ajaxContent').setStyle('overflow', 'hidden');" .
                '});';
        return $format;
    }

    public function profileChangeAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $slverifyUserTbl = Engine_Api::_()->getDbTable('users', 'slprofileverify');
        $select = $slverifyUserTbl->select()->from($slverifyUserTbl->info('name'))->where('user_id = ?', $viewer->user_id);
        $slverifyUserRow = $slverifyUserTbl->fetchRow($select);
        $slverifyValue = Zend_Json::decode($slverifyUserRow['value']);
        $valuesFields = Engine_Api::_()->fields()->getFieldsValues($viewer);

        if ($this->getRequest()->isPost()) {

            if (isset($_POST['continue'])) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $slverifyApi = Engine_Api::_()->getApi('core', 'slprofileverify');
                    $slverifyApi->verifyUser($viewer->user_id, 'unverified');
                    $slverifyApi->sendMailVerify($viewer->user_id, null, 'change_profile');
                    $slverifyUserValue = array();
                    foreach ($slverifyValue as $key => $values) {
                        $tmp = explode("_", $key);
                        $field_id = $tmp[2];
                        $valueRow = $valuesFields->getRowMatching(array(
                            'field_id' => $field_id,
                            'item_id' => $viewer->user_id,
                            'index' => 0
                        ));
                        $slverifyUserValue[$key] = $valueRow->value;
                    }
                    $slverifyUserRow->value = Zend_Json::encode($slverifyUserValue);
                    $slverifyUserRow->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                return $this->_forward('success', 'utility', 'core', array(
                            'smoothboxClose' => true,
                            'parentRefresh' => true,
                            'messages' => array($this->view->translate("Your changes have been saved."))
                ));
            }

            if (isset($_POST['cancel'])) {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    foreach ($slverifyValue as $key => $values) {
                        $tmp = explode("_", $key);
                        $field_id = $tmp[2];
                        $valueRow = $valuesFields->getRowMatching(array(
                            'field_id' => $field_id,
                            'item_id' => $viewer->user_id,
                            'index' => 0
                        ));
                        $valueRow->value = htmlspecialchars($values);
                        $valueRow->save();
                    }
                    // Update displayname
                    $valuesAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($viewer);
                    $viewer->setDisplayName($valuesAlias);
                    $viewer->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                return $this->_forward('success', 'utility', 'core', array(
                            'smoothboxClose' => true,
                            'parentRefresh' => true,
                            'messages' => array($this->view->translate("Your have not saved."))
                ));
            }
        } else {
            if ($this->_getParam('type') == "close") {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    foreach ($slverifyValue as $key => $values) {
                        $tmp = explode("_", $key);
                        $field_id = $tmp[2];
                        $valueRow = $valuesFields->getRowMatching(array(
                            'field_id' => $field_id,
                            'item_id' => $viewer->user_id,
                            'index' => 0
                        ));
                        $valueRow->value = htmlspecialchars($values);
                        $valueRow->save();
                    }
                    // Update displayname
                    $valuesAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($viewer);
                    $viewer->setDisplayName($valuesAlias);
                    $viewer->save();
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                return $this->_helper->redirector->gotoRoute(array('module' => 'user', 'controller' => 'edit', 'action' => 'profile'), 'default', true);
            }
        }
    }

}
