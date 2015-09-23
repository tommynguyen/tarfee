<?php

class Questionanswer_Form_Admin_Manage_DeleteQuestion extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Question')
      ->setDescription('Are you sure you want to Delete this question?');


    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Remove Question',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}