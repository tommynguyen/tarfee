<?php
class User_Form_Admin_Youtube_Global extends Engine_Form {
    public function init() {
        $this
        ->setTitle('Global Settings');
        
        $translate = Zend_Registry::get('Zend_Translate');
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $this->addElement('Text', 'user_youtube_clientid', array(
            'label' => 'ClientId',
            'value' => $settings->getSetting('user_youtube_clientid', ""),
        ));
        
        $this->addElement('Text', 'user_youtube_secret', array(
            'label' => 'Client Secret',
            'value' => $settings->getSetting('user_youtube_secret', ""),
        ));
		
		$this->addElement('Radio', 'user_youtube_allow', array(
	      'label' => 'Allow Videos to YouTube Channel?',
	      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user_youtube_allow', 0),
	      'multiOptions' => array(
	        '1' => 'Yes, allow videos to be uploaded to specific YouTube channel.',
	        '0' => 'No, disallow videos to be uploaded to specific YouTube channel.',
	      ),
	    ));
		
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));
    }
}