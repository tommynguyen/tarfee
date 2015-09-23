<?php
class Ynfeedback_Form_Note_Create extends Engine_Form
{
	public function init()
	{
		$this->setAttrib('class', 'global_form_popup');
		
		$this->addElement('Textarea', 'content', array(
			'label' => 'Add Note',	
		));	
       
		$this->addElement('Hidden', 'note_id', array(
			'value' => '0',
			'order' => 999,	
		));
		
        // Submit
        $this->addElement('Button', 'submit_btn', array(
            'label' => 'Save',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));
		
		 $this->addDisplayGroup(array('submit_btn'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	}
}