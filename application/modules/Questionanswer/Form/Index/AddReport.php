<?php

class Questionanswer_Form_Index_AddReport extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'add_report')
      ->setTitle('Inappropriate Content')
      ->setDescription('Please complete the following form to notify the administration of this page.');
    
    //init report type   
    $this->addElement('Radio', 'type', array(
      'label' => 'What are you reporting?',
      'description' => '', 	  
      'multiOptions' => array(
        'Spam' => 'Spam',
        'Inappropriate Conten' => 'Inappropriate Content',
        'Abuse' => 'Abuse',
        'Other' => 'Other'
      ),	  
    ));
        
    //init content
    $this->addElement('Textarea', 'content', array(
    	'lable' => 'Please give us a short description of the problem.',
    	'description' => 'Please give us a short description of the problem.',
    	'required' => false,
    	'rows' 	   => "10",
    	'cols'	   => "70",    	
    	'filters' => array(
    		'StripTags',
    		new Engine_Filter_Censor(),
    		new Engine_Filter_StringLength(array('max' => '1000'))
    	),
    ));
    
    $this->addElement('hidden', 'qid', array(	
    	'value' => '',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Send report',
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