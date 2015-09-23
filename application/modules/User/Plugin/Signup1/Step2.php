<?php

class User_Plugin_Signup1_Step2 extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'step2';

  protected $_formClass = 'User_Form_Signup1_Step2';

  protected $_script = array('signup/form/account.tpl', 'user');

  public $email = null;

}
