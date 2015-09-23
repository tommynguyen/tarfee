<?php

class Questionanswer_Form_Admin_Manage_EditAnswer extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'admin_answers_edit')
      ->setTitle('Edit Answer')
      ->setDescription('You can change the details of this answer here.');

    //init question
    $this->addElement('Textarea', 'content', array(
    	'lable' => 'Answer',
    	'required' => true,
    	'rows' 	   => "20",
    	'cols'	   => "50",    	
    	'filters' => array(
    		'StripTags',
    		new Engine_Filter_Censor()
    	),
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));



    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');



    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}