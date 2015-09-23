<?php
class User_Form_Recommendation_Write extends Engine_Form {
	protected $_user = null;
	
	public function getUser() {
		return $this -> _user;
	}
	
	public function setUser($user)
	{
		$this -> _user = $user;
	} 
	public function init() {
		$user = $this->getUser();
		$view = Zend_Registry::get('Zend_View');
		$this 
		  -> setTitle('Write Recommendation')
		  -> setDescription($view->translate('Write recommendation for %s', $user))
          -> setAttrib('class', 'global_form_popup');
        
		$this->loadDefaultDecorators();
		$this->getDecorator('Description')->setOption('escape', false);  
		$this -> addElement('Textarea', 'content', array(
			'required' => true,
			'allowEmpty' => false
		));

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
