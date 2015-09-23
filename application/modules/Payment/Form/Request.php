<?php
class Payment_Form_Request extends Engine_Form
{
  public function init()
  {
    // Init form
    $this->setTitle('Request Upgrade Membership');
    $this->setDescription($description);
    $this->setAttrib('id', 'payment_form_subscribe');
	$this->setAttrib('method', 'post');
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    $this->addElement('Text', 'first_name', array(
      'label' => 'First Name',
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => 1,
    ));
	$this -> first_name -> setAttrib('required', true);
	
	$this->addElement('Text', 'last_name', array(
      'label' => 'Last Name',
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => 2,
    ));
	$this -> last_name -> setAttrib('required', true);
	
	// init email
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => 3,
    ));
    $this->email->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
	$this -> email -> setAttrib('required', true);
	
	// init email
    $this->addElement('Text', 'emailconf', array(
      'label' => 'Confirm Email',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress'
      ),
      'tabindex' => 4,
    ));
    $this->emailconf->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
	$this->emailconf -> setAttrib('required', true);
	
	$specialValidator = new Engine_Validate_Callback(array($this, 'checkEmailConfirm'), $this->email);
    $specialValidator->setMessage('Email did not match', 'invalid');
    $this->emailconf->addValidator($specialValidator);
	
	$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
	$countriesAssoc = array(''=>'') + $countriesAssoc;

	$this->addElement('Select', 'country_id', array(
		'label' => 'Country',
		'multiOptions' => $countriesAssoc,
		'required' => true,
		'tabindex' => 5,
	));
	$this -> country_id -> setAttrib('required', true);

	$this->addElement('Select', 'province_id', array(
		'label' => 'Province/State',
		'tabindex' => 6,
	));

	$this->addElement('Select', 'city_id', array(
		'label' => 'City',
		'tabindex' => 7,
	));
	
	$this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'required' => false,
      'allowEmpty' => true,
      'tabindex' => 8,
    ));
	
	$this->addElement('Textarea', 'about', array(
      'label' => 'More about you',
      'required' => true,
      'allowEmpty' => false,
      'tabindex' => 9,
    ));	
	$this -> about -> setAttrib('required', true);
	
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Request',
      'type' => 'submit',
      'ignore' => true,
      'tabindex' => 10,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
  public function checkEmailConfirm($value, $emailElement)
  {
    return ( $value == $emailElement->getValue() );
  }

}
