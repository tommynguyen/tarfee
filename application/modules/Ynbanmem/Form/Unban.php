
<?php

/**
 * @category   
 * @package    
 * @copyright  
 * @license    
 */
class Ynbanmem_Form_Unban extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Unban')
      ->setDescription('Are you sure you want to unban this User/IP?');


    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Unban',
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