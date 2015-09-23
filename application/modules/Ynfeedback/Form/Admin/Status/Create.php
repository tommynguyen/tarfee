<?php
class Ynfeedback_Form_Admin_Status_Create extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Add Status')
          -> setAttrib('class', 'global_form_popup');
          
        $this-> addElement('Text', 'title', array(
            'label' => 'Status',
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
        
        $this->addElement('Heading', 'color', array(
            'label' => 'Color',
            'value' => '<input type="color" id="color" name="color"/>'
        ));
		
		$this -> addElement('Button', 'submit_btn', array(
			'label' => 'Add',
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
