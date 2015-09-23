<?php

class Ynnotification_Form_Sound extends Engine_Form
{
 
  public function init()
  {
    $this
      ->setTitle('Sound Alert Settings')
      ->setDescription('These settings affect all members in your community.');   
    
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->addElement('Radio', 'ynnotification_user_sound_setting', array(
    		'label' => 'Play sound alert in notification message',    		
    		'multiOptions' => array(
    				1 => 'Yes',
    				0 => 'No'
    		),
    		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.user'.$viewer->getIdentity().'sound.setting', 2)? 1: 0,
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
    		'label' => 'Save Changes',
    		'type' => 'submit',
    		'ignore' => true
    ));

  }

}
