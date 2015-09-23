<?php
class User_Form_Playercard_Delete extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Delete Player Card')
      ->setDescription('Are you sure you want to delete this player card?')
      ;
      
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'type' => 'link',
      'link' => true,
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}