<?php

class User_Form_EmailToFriend extends Engine_Form
{
	
	public function init() {
		// Init settings object
		$translate = Zend_Registry::get('Zend_Translate');

		// Init form
		$this -> setTitle('Email To Friend') -> setDescription('Send message to your friend.') -> setLegend('');
		$this -> setAttrib('class', 'global_form_popup');
		
		//Email
	    $this->addElement('Text', 'email', array(
	      'label' => '*Email',
	      'allowEmpty' => false,
	      'required' => true,
	      'validators' => array(
	        array('NotEmpty', true),
	      ),
	      'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	      ),
	    ));
		$this -> email -> setAttrib('required', true);
	

		// Init custom message
		$this -> addElement('Textarea', 'message', array(
			'label' => '*Message',
			'style' => 'width:450px',
			'required' => true,
			'allowEmpty' => false,
			'filters' => array(new Engine_Filter_Censor(), )
		));
		$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));
		$this -> message -> setAttrib('required', true);

		$this -> addElement('Button', 'submit', array(
			'label' => 'Send',
			'type' => 'submit',
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'submit';
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));
		$buttons[] = 'cancel';

		$this -> addDisplayGroup($buttons, 'buttons');
		$button_group = $this -> getDisplayGroup('buttons');
	}


}
