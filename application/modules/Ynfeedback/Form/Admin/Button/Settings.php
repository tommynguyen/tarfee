<?php
class Ynfeedback_Form_Admin_Button_Settings extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Feedback Button Settings');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Radio', 'ynfeedback_button_type', array(
            'label' => 'Feedback Button Type',
            'multiOptions' => array(
                1 => 'Use Text.',
                2 => 'Use Icon.'
            ),
            'value' => $settings->getSetting('ynfeedback_button_type', 1)
        ));
        
        $this->addElement('File', 'icon', array(
            'label' => 'Browse Image'
        ));
        $this -> icon -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
        
        $this->addElement('Text', 'ynfeedback_button_text', array(
            'label' => 'Feedback Button Text',
            'description' => 'You are free to change the text of feedback button to whatever you like.',
            'value' => $settings->getSetting('ynfeedback_button_text', 'Feedback'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        $color = $settings->getSetting('ynfeedback_button_textcolor', '#FFFFFF');
        $this->addElement('Heading', 'ynfeedback_button_textcolor', array(
            'label' => 'Text Color',
            'value' => '<input value="'.$color.'" type="color" id="textcolor" name="textcolor"/>'
        ));
        
        $color = $settings->getSetting('ynfeedback_button_buttoncolor', '#2BA8E2');
        $this->addElement('Heading', 'ynfeedback_button_buttoncolor', array(
            'label' => 'Button Color',
            'value' => '<input value="'.$color.'" type="color" id="buttoncolor" name="buttoncolor"/>'
        ));
        
        $this->addElement('Text', 'ynfeedback_button_hovertext', array(
            'label' => 'Feedback Button Text When Hover',
            'description' => 'When the user hovers over the button, it slides out revealing the text you\'ve defined.',
            'value' => $settings->getSetting('ynfeedback_button_hovertext', 'Feedback'),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        
        $color = $settings->getSetting('ynfeedback_button_hovertextcolor', '#FFFFFF');
        $this->addElement('Heading', 'ynfeedback_button_hovertextcolor', array(
            'label' => 'Hover Text Color',
            'value' => '<input value="'.$color.'" type="color" id="hovertextcolor" name="hovertextcolor"/>'
        ));
        
        $color = $settings->getSetting('ynfeedback_button_hoverbuttoncolor', '#2BA8E2');
        $this->addElement('Heading', 'ynfeedback_button_hoverbuttoncolor', array(
            'label' => 'Hover Button Color',
            'value' => '<input value="'.$color.'" type="color" id="hoverbuttoncolor" name="hoverbuttoncolor"/>'
            
        ));
        
        $this->addElement('Radio', 'ynfeedback_button_left', array(
            'label' => 'Feedback Button Position',
            'multiOptions' => array(
                1 => 'Left positioned',
                2 => 'Right positioned'
            ),
            'value' => $settings->getSetting('ynfeedback_button_left', 1),
        ));
        
        $position = $settings->getSetting('ynfeedback_button_position', 50);
        $this->addElement('Dummy', 'position', array(
            'decorators' => array( 
                array('ViewScript', 
                    array(
                        'viewScript' => '_button_position.tpl', 
                        'position' => $position
                    )
                )
            ), 
        ));
        
        $this->addElement('Hidden', 'ynfeedback_button_position', array(
            'value' => $position
        ));
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}