<?php

class User_Form_Signup1_Fields extends Engine_Form
{
  public function init()
  {
  	$this -> setAttrib('id', 'account_skip');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_signup1', true));
  }
}