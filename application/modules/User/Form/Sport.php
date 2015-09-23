<?php
class User_Form_Sport extends Engine_Form {
	protected $_count = 0;
	
	public function getCount() {
		return $this->_count;
	}
	
	public function setCount($count) {
		$this->_count = $count;	
	}
	
	public function init() {
		$view = Zend_Registry::get('Zend_View');
		$description = $view->translate('Search sport which will be added as your sport. You can add up to %s sport(s).', (2- $this->getCount()));
		$this 
		  -> setTitle('Add Sports')
          -> setDescription($description)
          -> setAttrib('class', 'global_form_popup');
          
		$this -> addElement('Text', 'to', array('autocomplete' => 'off'));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 1,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);

		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Submit',
			'type' => 'submit',
			'order' => 3,
			'ignore' => true,
			'decorators' => array('ViewHelper')
		));
		$onclick = 'parent.Smoothbox.close();';
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'order' => 4,
			'link' => true,
			'prependText' => ' or ',
			'onclick' => $onclick,
			'decorators' => array('ViewHelper')
		));

		$this -> addDisplayGroup(array(
			'submit_btn',
			'cancel'
		), 'buttons');
	}

}
