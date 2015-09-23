<?php
class Contactimporter_Form_Login extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'login_contactimporter_form',
        'class' => 'global_form',
      ))
      ->setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));

    parent::init();
	
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$type = $request->getParam('type');
	$provider = $request->getParam('provider');
	
	$label = "Email";
	if($type == 'user')
		$label = "Username";
	
    $this->addElement('Text', 'email', array(
      'label' => $label,
      'required' => true,
    ));
	
	 $this->addElement('Password', 'password', array(
      'label' => 'Password',
      'required' => true,
    ));
	
	$this -> addElement('Hidden', 'provider', array(
      'value' => $provider,
    ));
	
	$this -> addElement('Button', 'submit', array(
			'label' => 'Import Contacts',
			'type' => 'submit',
			'link' => true,
			'ignore' => true,
			'decorators' => array('ViewHelper', ),
		));
	
	$this -> addElement('Button', 'cancel', array(
		'label' => 'Cancel',
		'link' => true,
		'href' => '',
		'onclick' => 'parent.Smoothbox.close();',
		'decorators' => array('ViewHelper')
	));
	// DisplayGroup: buttons
	$this -> addDisplayGroup(array(
		'submit',
		'cancel',
	), 'buttons', array('decorators' => array(
			'FormElements',
			'DivDivDivWrapper'
		), ));
  }
}