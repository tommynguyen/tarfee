<?php

class Ynnotification_Form_Admin_Sound extends Engine_Form
{
 
  public function init()
  {
  	$this
	->addPrefixPath('Ynnotification_Form_Element', APPLICATION_PATH . '/application/modules/Ynnotification/Form/Element', 'element');
    $this
      ->setTitle('Sound Alert Settings')
      ->setDescription('These settings affect all members in your community.');   
    
	$translate = Zend_Registry::get('Zend_Translate');
	
	
	$desmp3 = "Support file mp3";
	$mp3 = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.alert', null);	
	
	if($mp3)
		$desmp3 = sprintf($translate->translate("Current file: %s"),$mp3);
	
	$wav = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.wav.alert', null);
	
	$deswav = "Support wav file for firefox";
	if($wav)
		$deswav = sprintf($translate->translate("Current file: %s"),$wav);
	
	
		
	$this->addElement('Ynnotificationbrowser', 'ynnotification_browser', array());
	
    $this->addElement('File', 'sound', array(
    		'label' => 'Choose Sound Alert (Required)',
    		'description' => $desmp3,
    		'required' => false,
    		'destination' => APPLICATION_PATH.'/public/temporary/',    		
    		'validators' => array(    							
    				array('Extension', false, 'mp3'),
    		),
    		
    ));   
	$this->sound->getDecorator("Description")->setOption("placement", "append");
	
	$this->addElement('File', 'sound_wav', array(
    		'label' => 'Choose Sound Alert (Optional)',
    		'description' => $deswav,
    		'required' => false,
    		'destination' => APPLICATION_PATH.'/public/temporary/',    		
    		'validators' => array(    							
    				array('Extension', false, 'wav'),
    		),
    		
    ));   
	$this->sound_wav->getDecorator("Description")->setOption("placement", "append");
    
    $this->addElement('Radio', 'ynnotification_sound_setting', array(
    		'label' => 'Play sound alert in notification message',    		
    		'multiOptions' => array(
    				1 => 'Yes',
    				0 => 'No'
    		),
    		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.setting', 0),
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
    		'label' => 'Save Changes',
    		'type' => 'submit',
    		'ignore' => true
    ));

  }

}
