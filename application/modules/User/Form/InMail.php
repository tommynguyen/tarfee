<?php

class User_Form_InMail extends Engine_Form {

	public function init() {
		// Init settings object
		$translate = Zend_Registry::get('Zend_Translate');
		
		// Init form
		$this -> setTitle('Email To User') -> setDescription('Send this email to your friends.') -> setLegend('');
		$this -> setAttrib('class', 'global_form_popup');

		// Init custom message
		$this -> addElement('Textarea', 'message', array(
			'label' => 'Custom Message',
			'style' => 'width:450px',
			'required' => false,
			'allowEmpty' => true,
			'filters' => array(new Engine_Filter_Censor(), )
		));
		$this -> message -> getDecorator('Description') -> setOptions(array('placement' => 'APPEND'));

		$this -> addElement('Button', 'submit', array(
			'label' => 'Send Emails',
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
