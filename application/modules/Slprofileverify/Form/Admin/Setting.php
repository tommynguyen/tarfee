<?php

class Slprofileverify_Form_Admin_Setting extends Engine_Form {

    public function init() {
        $this->setDescription($this->getView()->translate("GLOBAL_SETTINGS_DESCRIPTION"))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $this->addElement('File', 'badge', array(
            'label' => $this->getView()->translate("Verify Badge"),
        ));
        $this->badge->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('Text', 'member_mapping', array(
            'label' => 'Member level mapping'
        ));

        $this->addElement('Hidden', 'member_verified', array(
            'isArray' => true,
            'order' => 10000,
            'id' => 'member_verified_remove'
        ));
        
        $this->addElement('Hidden', 'member_unverified', array(
            'isArray' => true,
            'order' => 10001,
            'id' => 'member_unverified_remove'
        ));

        $this->addElement('Button', 'submit', array(
            'label' => $this->getView()->translate("Save Changes"),
            'type' => 'submit'
        ));
    }

}