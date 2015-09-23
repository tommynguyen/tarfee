<?php
class User_Form_Admin_Service_Add extends Engine_Form {
  	protected $_field;

  	public function init() {
    	$this->setMethod('post');

    	$label = new Zend_Form_Element_Text('title');
		$label->setLabel('Title')
      	->addValidator('NotEmpty')
      	->setRequired(true)
      	->setAttrib('class', 'text');


    	$id = new Zend_Form_Element_Hidden('id');


    	$this->addElements(array(
      	//$type,
      		$label,
      		$id
    	));
    
    	// Buttons
    	$this->addElement('Button', 'submit', array(
      		'label' => 'Add Service',
      		'type' => 'submit',
      		'ignore' => true,
      		'decorators' => array('ViewHelper')
    	));

    	$this->addElement('Cancel', 'cancel', array(
      		'label' => 'cancel',
      		'link' => true,
      		'prependText' => ' or ',
      		'href' => '',
      		'onClick'=> 'javascript:parent.Smoothbox.close();',
      		'decorators' => array(
        		'ViewHelper'
      		)
    	));
    
    	$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    	$button_group = $this->getDisplayGroup('buttons');

   		// $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  	}

  	public function setField($service) {
    	$this->_field = $service;

    	// Set up elements
    	//$this->removeElement('type');
    	$this->title->setValue($service->title);
    	$this->id->setValue($service->service_id);
    	$this->submit->setLabel('Edit');

    	// @todo add the rest of the parameters
  	}
}