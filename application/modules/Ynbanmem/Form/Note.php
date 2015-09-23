<?php

/**
 * @category   
 * @package    
 * @copyright  
 * @license    
 */
class Ynbanmem_Form_Note extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('User Notes')
      ->setDescription('');
    // init note
    $this -> addElement('textarea', 'note', array(
			'label' => '*Add a simple note for user.',
			 'required' => false,
			'allowEmpty' => true,
		));

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Update',
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
    //$this->addDisplayGroup(array('submit', 'cancel'));
    //$button_group = $this->getDisplayGroup('buttons');

    // Set default action
    //$this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}