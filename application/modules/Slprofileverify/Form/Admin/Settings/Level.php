<?php

class Slprofileverify_Form_Admin_Settings_Level extends Engine_Form {

    public function init() {

        $this->setDescription($this->getView()->translate("MEMBER_LEVEL_SETTINGS_DESCRIPTION"))
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Prepare user levels
        $levelOptions = array();
        $arrRemove = array("admin", "moderator", "public");
        $oAuthLevelTbl = Engine_Api::_()->getItemTable('authorization_level');
        $sSelect = $oAuthLevelTbl->select()->where('type NOT IN(?)', $arrRemove);
        $oAuthLevel = $oAuthLevelTbl->fetchAll($sSelect);
        
        foreach ($oAuthLevel as $level) {
            $levelOptions[$level->level_id] = $level->getTitle();
        }

        // Element: level_id
        $this->addElement('Select', 'level_id', array(
            'label' => $this->getView()->translate("Member level"),
            'multiOptions' => $levelOptions,
            'onchange' => 'javascript:fetchLevelSettings(this.value);',
            'ignore' => true,
        ));

        // Element: verify
        $this->addElement('Radio', 'send', array(
            'label' => $this->getView()->translate("Allow send a verification request"),
            'multiOptions' => array(
                1 => $this->getView()->translate("Yes"),
                0 => $this->getView()->translate("No"),
            )
        ));

        // Add submit
        $this->addElement('Button', 'submit', array(
            'label' => $this->getView()->translate("Save Changes"),
            'type' => 'submit',
            'ignore' => true,
            'order' => 100000,
        ));
    }

}