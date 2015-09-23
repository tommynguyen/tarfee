<?php

class Ynnotification_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
      $this
      ->setTitle('Notification Settings')
      ->setDescription('These settings affect all members in your community.');
     

    $this->addElement('Text', 'ynnotification_time_deplay',array(
      'label'=>'Display notification (Seconds)',     
      'description' => 'Define in seconds when the Advanced Feed Notification box should hide.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
      'required' => true,
     
	  'validators' =>  array(
                          'Int',
                            array('Between', false, array('min' => 5, 'max' => 120)),                            
                          ),
		
     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.time.deplay', '10000')/1000,
    ));
      
    $this->addElement('Text', 'ynnotification_time_refresh',array(
    	'label'=>'Notification Refresh Time (Seconds)',
    	'description' => 'Define how many seconds before the notifications refresh.',
    	'filters' => array(
    		new Engine_Filter_Censor(),
    	),
    	'required' => true,
    	'validators' =>  array(
                          'Int',
                            array('Between', false, array('min' => 10, 'max' => 120)),                            
                          ),
		
    	'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.time.refresh', '120000')/1000,
    ));
	$translate = Zend_Registry::get('Zend_Translate');
	
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$this->addElement('Dummy', 'warning', array(
          'content' => "<img style='margin-top: -6px;'src='".$request->getBaseUrl()."/application/modules/Ynnotification/externals/images/warning.png'> ".$translate->translate("Have a low number (< 30 seconds) for this setting value could impact the performance of the system."),         
        ));
    $this->addElement('Radio', 'ynnotification_photo_notification', array(
    	'label' => 'Show photo in notification message',
    	'description' => 'Show user profile photo in notification message box.',
    	'multiOptions' => array(
    			1 => 'Yes',
    			0 => 'No'
    	),
    	'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.photo.notification', 0),
    ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}