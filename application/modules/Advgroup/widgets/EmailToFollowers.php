<?php

class Advgroup_Form_EmailToFollowers extends Engine_Form
{
	protected $_group;
	
	public function getGroup(){
		return $this ->_group;
	}
	
	public function setGroup($group){
		$this ->_group = $group;
	}
	
	public $invalid_emails = array();

	public $emails_sent = 0;
	
	public function init() {
		// Init settings object
		$translate = Zend_Registry::get('Zend_Translate');

		// Init form
		$this -> setTitle('Email To Friends') -> setDescription('Send message to your followers.') -> setLegend('');
		$this -> setAttrib('class', 'global_form_popup');
		
		
		$followerTable = Engine_Api::_() -> getDbTable('follow', 'advgroup');
		$followers = $followerTable -> getUserFollow($this ->_group -> getIdentity());
		$arrValue = array();
		foreach($followers as $follower) {
			$user = Engine_Api::_() -> getItem('user', $follower -> user_id);
			$arrValue[$follower -> user_id] =  $user -> getTitle();
		}
		
		$multi = new Zend_Form_Element_Multiselect('followers');
		$multi->setMultiOptions($arrValue);
		$this->addElement($multi);
		

		// Init custom message
		$this -> addElement('Textarea', 'message', array(
			'label' => 'Custom Message',
			'style' => 'width:450px',
			'required' => true,
			'allowEmpty' => false,
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
