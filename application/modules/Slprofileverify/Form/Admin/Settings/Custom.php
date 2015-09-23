<?php

// SMALLADDON-145 Tan
class Slprofileverify_Form_Admin_Settings_Custom extends Engine_Form {

    public function init() {
        $this->setAttribs(array('id' => 'setting_custom', 'class' => 'global_form set-custom'));
        $this->setDescription($this->getView()->translate("CUSTOM_VERIFICATION_STEP_DESCRIPTION"));

        $this->addElement('Checkbox', 'enable_step', array(
            'label' => $this->getView()->translate("Enable custom verification step")
        ));

        $this->addElement("Text", 'step_name', array(
            'label' => $this->getView()->translate("Verification step name"),
            'required' => true,
        ));

        $this->addElement('TinyMce', 'exp_step', array(
            'label' => $this->getView()->translate("Explanation for this step"),
            'required' => true,
            'editorOptions' => array(
                'theme_advanced_buttons1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink|,hr,removeformat,cleanup,fullscreen",
                'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,forecolor,backcolor",
                'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,tablecontrols",
                'toolbar1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink|,hr,removeformat,cleanup,fullscreen",
                'toolbar2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,forecolor,backcolor",
                'toolbar3' => "formatselect,fontselect,fontsizeselect,|,table"
            ),
            'description' => $this->getView()->translate("DESCRIPTION_EXPLANATION_STEP")
        ));

        $this->addElement('Button', 'button_custom', array(
            'label' => $this->getView()->translate("Save Changes"),
            'type' => 'submit',
        ));
    }

}
