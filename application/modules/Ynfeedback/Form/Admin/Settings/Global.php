<?php
class Ynfeedback_Form_Admin_Settings_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings')
        
        ->setDescription('YNFEEDBACK_SETTINGS_GLOBAL_DESCRIPTION');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Radio', 'comment', array(
            'label' => 'Allow guest to comment on feedback',
            'multiOptions' => array(
                1 => 'Yes, allow guest to comment on feedback.',
                0 => 'No, do not allow guest to comment on feedback.'
            ),
            'value' => 1
        ));
        
        $this->addElement('Radio', 'create', array(
            'label' => 'Allow guest to add new feedback',
            'multiOptions' => array(
                1 => 'Yes, allow guest to add new feedback.',
                0 => 'No, do not allow guest to add new feedback.'
            ),
            'value' => 1
        ));
        
		$this->addElement('Radio', 'ynfeedback_guest_merge', array(
            'label' => 'Merge feedbacks and comments of guests and registered users',
            'description' => 'If guest enters a feedback/comment with an email, then register with this one, system will merge all his/her previous feedbacks/comments to only one account',
            'multiOptions' => array(
                1 => 'Yes, allow merging feedbacks/comments of guests and registered users.',
                0 => 'No, do not merging feedbacks/comments of guests and registered users.'
            ),
            'value' => $settings->getSetting('ynfeedback_guest_add', 1)
        ));
		
		$this->addElement('Radio', 'ynfeedback_popup_style', array(
           'label' => 'Feedback popup settings',
           'multiOptions' => array(
                1 => 'Show simple popup to allow users feedback quickly.',
                0 => 'Show popup to allow users add feedback as well as display some popular and newest feedbacks.'
            ),

            'value' => $settings->getSetting('ynfeedback_popup_style', 1)
        ));
        
        $this->addElement('Integer', 'ynfeedback_max_idea', array(
            'label' => 'Maximum feedbacks are shown on list (Most popular, Newest, ...)',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(1),
            ),
            'value' => $settings->getSetting('ynfeedback_max_idea', 20),
        ));
                
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}