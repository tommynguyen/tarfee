<?php
class Ynfeedback_Form_Admin_Status_Delete extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Add Status')
          -> setAttrib('class', 'global_form_popup');
          
        $this-> addElement('Select', 'move_status', array(
            'label' => 'Move existing feedbacks to another status',
        ));
        
        $this-> addElement('hidden', 'id', array(
        ));
        
		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Delete',
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
