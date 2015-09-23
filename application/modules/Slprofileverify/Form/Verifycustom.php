<?php

class Slprofileverify_Form_Verifycustom extends Engine_Form {

    protected $_custom_field = null;

    public function init() {
        $this->setMethod("POST")
                ->setAttrib('id', 'cus_step_verify')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        ;

        // Add subforms
        $viewer = Engine_Api::_()->user()->getViewer();
        $metaTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
        $arrVerify = $metaTbl->getVerifyInfor($viewer->user_id);

        $this->_custom_field = $customFields = new Slprofileverify_Form_Custom_Fields(array(
            'item' => $arrVerify,
            'topLevelId' => null,
            'topLevelValue' => null,
        ));

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        // badge
        $this->addElement('File', 'document', array(
            'label' => $this->getView()->translate("Upload Verification Document") . "*",
            'required' => true,
            'isArray' => true
        ));
        $this->document->addValidator('Extension', false, 'jpg,png,gif,jpeg');


        $this->addElement('Checkbox', 'copy_document', array(
            'label' => $this->getView()->translate("I confirm that this is true copy of my document"),
            'value' => false,
            'required' => true,
        ));

        $this->addElement('Button', 'finish', array(
            'label' => $this->getView()->translate("Finish"),
            'type' => 'submit',
            'ignore' => true
        ));
    }

    public function saveValues() {
        $this->_custom_field->saveValues();
    }

}