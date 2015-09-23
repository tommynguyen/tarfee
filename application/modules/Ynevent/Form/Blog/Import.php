<?php
class Ynevent_Form_Blog_Import extends Engine_Form
{
  public function init()
  {
	
	// Init to Values
        $this -> addElement('Hidden', 'toValues', array(
            'label' => 'Blog Title',
            'style' => 'margin-top:-5px',
            'order' => 0,
            'validators' => array('NotEmpty'),
            'filters' => array('HtmlEntities'),
        ));
		
        Engine_Form::addDefaultDecorators($this -> toValues);
        
        $this -> addElement('Text', 'to', array(
            'label' => 'Blog Title',
			'autocomplete' => 'off',
            'order' => 1, 
            'required' => true,
            'allowEmpty' => false,
            'description' => 'Start typing a blog title to see a list of suggestions'
		));
		Engine_Form::addDefaultDecorators($this -> to);
        $this->to->getDecorator("Description")->setOption("placement", "append");   
		$this -> to -> setAttrib('required', true);
		
		$this -> addElement('Dummy', 'import', array(
			'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_import.tpl',
				'class' => 'form element',
			)
			)), 
		));
		

		$this -> addElement('Button', 'submit', array(
			'label' => 'Import',
			'type' => 'submit',
			'ignore' => true,
            'decorators' => array(
            'ViewHelper',
            ),
		));
		

		 $this->addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'href' => '',
			'onclick' => 'parent.Smoothbox.close();',
			'decorators' => array(
				'ViewHelper'
			)
		));
         // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
  }
}