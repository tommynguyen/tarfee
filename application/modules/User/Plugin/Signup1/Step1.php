<?php

class User_Plugin_Signup1_Step1 extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'step1';

  protected $_formClass = 'User_Form_Signup1_Step1';

  protected $_script = array('signup/form/account.tpl', 'user');

  public $email = null;
	
  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    if( $this->getForm()->isValid($request->getPost()) )
    {
      $values = $this->getForm()->getValues();
      $this->getSession()->data = $values;
      $this->setActive(false);
      $this->onSubmitIsValid();
	  
	  if( isset($values['code']) ) 
	  {
		  $_SESSION['ref_code'] = $values['code'];
	  }
      return true;
    }

    // Form was not valid
    else
    {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }
  }
}
