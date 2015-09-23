<?php
class Advgroup_Form_Member_Request extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Request Club Membership')
      ->setDescription('Would you like to request membership in this club?')
      ->setMethod('POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    //$this->addElement('Hash', 'token');

    $this->addElement('Button', 'submit', array(
      'label' => 'Send Request',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
      'type' => 'submit'
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));


    $this->addDisplayGroup(array(
      'submit',
      'cancel'
    ), 'buttons');
  }
}