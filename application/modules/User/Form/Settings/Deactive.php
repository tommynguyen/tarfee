<?php
class User_Form_Settings_Deactive extends Engine_Form {
  public function init()
  {
    $this
      ->setTitle('Deactivate Account')
      ->setDescription('Are you sure you want to deactivate your account?')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Element: token
    $this->addElement('Hash', 'token');

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Yes, Deactivate My Account',
      'type' => 'submit',
      'ignore' => true,
      //'style' => 'color:#D12F19;',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons');
    
    return $this;
  }
}