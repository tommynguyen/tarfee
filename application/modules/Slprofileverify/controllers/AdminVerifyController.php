<?php

class Slprofileverify_AdminVerifyController extends Fields_Controller_AdminAbstract {

    protected $_fieldType = 'user';
    protected $_requireProfileType = true;

    public function indexAction() {
        parent::indexAction();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_profileverify');
        
        $optionId = $this->view->topLevelOptionId; // extend by field admin
        $requiresTbl = Engine_Api::_()->getDbTable('requires', 'slprofileverify');
        $requireRow = $requiresTbl->getRequireRow($optionId);
        $arrFieldIdRight = Zend_Json::decode($requireRow->required);
        
        // Get profile question current
        $mapsTbl = Engine_Api::_()->fields()->getTable('user', 'maps');
        $mapsTblName = $mapsTbl->info('name');
        $metaTbl = Engine_Api::_()->fields()->getTable('user', 'meta');
        $metaTblName = $metaTbl->info('name');
        $select = $mapsTbl->select()->setIntegrityCheck(false)
                ->from($mapsTblName, array())
                ->join($metaTblName, $mapsTblName . '.child_id = ' . $metaTblName . '.field_id', array('field_id', 'label'))
                ->where($mapsTblName . '.option_id = ?', $optionId)
                ->where($metaTblName . '.type != ?', 'heading');
        if ($arrFieldIdRight) {
            $select->where($metaTblName . '.field_id NOT IN(?)', $arrFieldIdRight);
        }
        $this->view->listFieldMeta = $mapsTbl->fetchAll($select);

        // Render form
        $this->view->form = $form = new Slprofileverify_Form_Admin_Verifyidentity();
        if ($requireRow) {
            $form->enable_profile->setValue($requireRow->enable_profile);
            $form->exp_document->setValue($requireRow->exp_document);
            $form->image_number->setValue($requireRow->image_number);
        }

        // Array photo
        $arrFileID = array(array(0, 1), array(1, 1), array(2, 1), array(3, 1));
        $arrImage = array();
        $arrEnable = array(0, 1, 2, 3);
        if ($requireRow->image) {
            $arrFileID = Zend_Json::decode($requireRow->image);
            $arrEnable = array();
            foreach ($arrFileID as $key => $arrValues) {
                $arrImage[] = $arrFileID[$key][0];
                if ($arrValues[1] == 1) {
                    $arrEnable[] = $key;
                }
            }
        }
        $form->enable_img->setValue($arrEnable);
        $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoIdentityUrl($arrImage, 'thumb.normal', 'custom');        

        if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        $values_file = $form->file_step->getTransferAdapter()->getFileInfo();

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
            if ($requireRow) {
                $data = array(
                    'enable_profile' => $values['enable_profile'],
                    'image_number' => $values['image_number'],
                    'exp_document' => $values['exp_document'],
                    'image' => $fileImage
                );
                $where = array();
                $where[] = $requiresTbl->getDefaultAdapter()->quoteInto('option_id = ?', $optionId);
                $requiresTbl->update($data, $where);
            } else {
                $data = array(
                    'option_id' => $optionId,
                    'enable_profile' => $values['enable_profile'],
                    'image_number' => $values['image_number'],
                    'exp_document' => $values['exp_document'],
                    'image' => $fileImage,
                    'required' => ""
                );
                $requiresTbl->insert($data);
            }

            $this->view->src_img = Engine_Api::_()->slprofileverify()->getPhotoIdentityUrl($arrImageNew, 'thumb.normal', 'custom');

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }

}