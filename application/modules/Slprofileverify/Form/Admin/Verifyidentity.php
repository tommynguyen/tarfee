<?php

// SMALLADDON-145 Tan
class Slprofileverify_Form_Admin_Verifyidentity extends Engine_Form {

    public function init() {
        $this->setAttribs(array('id' => 'verify_identity', 'class' => 'global_form set-custom'));
        $this->addElement('Radio', 'enable_profile', array(
            'label' => $this->getView()->translate("Profile picture"),
            'required' => true,
            'multiOptions' => array(
                '1' => $this->getView()->translate("Required"),
                '0' => $this->getView()->translate("Not required")
            ),
            'value' => 1
        ));

        $this->addElement('MultiCheckbox', 'enable_img', array(
            'label' => $this->getView()->translate("Sample document"),
            'required' => true,
            'notEmpty' => true,
            'multiOptions' => array(
                '0' => '',
                '1' => '',
                '2' => '',
                '3' => '',
            ),
        ));

        $this->addElement('File', 'file_step', array(
            'label' => $this->getView()->translate("Sample document")
        ));
        $this->file_step->setMultiFile(4);
        $this->file_step->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('Text', 'image_number', array(
            'label' => 'Number of document',
            'value' => 1,
            'required' => true,
            'validators' => array(
                'notEmpty',
                array('GreaterThan', false, array(0)),
            ),
        ));

        $this->addElement('TinyMce', 'exp_document', array(
            'label' => $this->getView()->translate("Explanation for sample document"),
            'required' => true,
            'editorOptions' => array(
                'theme_advanced_buttons1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink|,hr,removeformat,cleanup,fullscreen",
                'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,forecolor,backcolor",
                'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,tablecontrols",
                'toolbar1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink|,hr,removeformat,cleanup,fullscreen",
                'toolbar2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,forecolor,backcolor",
                'toolbar3' => "formatselect,fontselect,fontsizeselect,|,table"
            ),
        ));

        $this->addElement('Button', 'verify_identity', array(
            'label' => $this->getView()->translate("Save Changes"),
            'type' => 'submit',
        ));
    }

}
