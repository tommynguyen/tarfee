<?php

class Slprofileverify_AdminFieldsController extends Fields_Controller_AdminAbstract {

    protected $_fieldType = 'slprofileverify';
    protected $_requireProfileType = true;

    public function indexAction() {
        parent::indexAction();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_custom');

        // Make form cus
        $this->view->form = $form = new Slprofileverify_Form_Admin_Settings_Custom();
        $this->view->form_field = $form_field = new Slprofileverify_Form_Admin_Settings_Field();
        $customTbl = Engine_Api::_()->getDbTable('customs', 'slprofileverify');
        $settingCore = Engine_Api::_()->getApi('settings', 'core');
        $option_id = $this->view->topLevelOptionId;
        $customRow = $customTbl->getCustomRow($option_id);

        // Populate form
        $arrayValues = array(
            'enable_step' => $settingCore->getSetting('sl_enable_step', 0),
            'step_name' => $settingCore->getSetting('sl_step_name', ""),
            'exp_step' => $settingCore->getSetting('sl_exp_step', "")
        );
        $form->populate($arrayValues);

        // Array photo
        $arrFileID = array(array(0, 1), array(1, 1), array(2, 1), array(3, 1));
        $arrImage = array();
        $arrEnable = array(0, 1, 2, 3);
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
        $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoIdentityUrl($arrImage, 'thumb.normal', 'custom');

        // Image enable
        $form_field->enable_img->setValue($arrEnable);
        if ($customRow) {
            $form_field->exp_document->setValue($customRow->exp_document);
            $form_field->image_number->setValue($customRow->image_number);
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (isset($_POST['button_custom'])) {
            if (!$form->isValid($this->getRequest()->getPost())) {
                return;
            }
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $values = $form->getValues();
                $settingCore->setSetting('sl_enable_step', $values['enable_step']);
                $settingCore->setSetting('sl_step_name', $values['step_name']);
                $settingCore->setSetting('sl_exp_step', $values['exp_step']);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $form->addNotice('Your changes have been saved.');
        }


        if (isset($_POST['button_custom_field'])) {
            if (!$form_field->isValid($this->getRequest()->getPost())) {
                return;
            }

            $values = $form_field->getValues();
            $values_file = $form_field->file_step->getTransferAdapter()->getFileInfo();

            // Begin transaction
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $arrImageNew = array();
                foreach ($values_file as $key => $value) {
                    $posId = explode('_', $key);
                    if ($value['error'] == 0) {
                        $photo_id = Engine_Api::_()->slprofileverify()->setPhotoVerification($values_file[$key], 'slprofileverify');
                        if (!$photo_id) {
                            $photo_id = 0;
                        }
                        $arrFileID[$posId[2]][0] = $photo_id;
                    }
                    $arrImageNew[] = $arrFileID[$posId[2]][0];
                    $arrFileID[$posId[2]][1] = 0;
                }
                foreach ($values['enable_img'] as $value) {
                    $arrFileID[$value][1] = 1;
                }

                $fileImage = Zend_Json::encode($arrFileID);
                if ($customRow) {
                    $data = array(
                        'image_number' => $values['image_number'],
                        'exp_document' => $values['exp_document'],
                        'image' => $fileImage
                    );
                    $where = array();
                    $where[] = $customTbl->getDefaultAdapter()->quoteInto('option_id = ?', $option_id);
                    $customTbl->update($data, $where);
                } else {
                    $data = array(
                        'option_id' => $option_id,
                        'image_number' => $values['image_number'],
                        'exp_document' => $values['exp_document'],
                        'image' => $fileImage
                    );
                    $customTbl->insert($data);
                }

                $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoIdentityUrl($arrImageNew, 'thumb.normal', 'custom');

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $form_field->addNotice('Your changes have been saved.');
        }
    }

}