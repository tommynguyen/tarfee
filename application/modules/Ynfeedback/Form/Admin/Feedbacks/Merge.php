<?php
class Ynfeedback_Form_Admin_Feedbacks_Merge extends Engine_Form {
	public function init() {
		$this 
		  -> setTitle('Merge Feedbacks')
          -> setDescription('Please note that after merging feedbacks, all original feedbacks will be deleted permanantly.')
          -> setAttrib('class', 'global_form');
        
		//Heading
		$this ->addElement('heading', 'heading_1', array(
			'label' => '1. Search feedback',
		));
		
		$this -> addElement('Text', 'to', array(
			'autocomplete' => 'off',
			'label' => 'Feedback'
		));
		Engine_Form::addDefaultDecorators($this -> to);

		// Init to Values
		$this -> addElement('Hidden', 'toValues', array(
			'style' => 'margin-top:-5px',
			'order' => 2,
			'filters' => array('HtmlEntities'),
		));
		Engine_Form::addDefaultDecorators($this -> toValues);
		
		//Heading
		$this ->addElement('heading', 'heading_2', array(
			'label' => '2. Select feedback to use for title and description',
		));
		
		//list ideas
		 $this->addElement('Select', 'listFeedback', array(
		  'required'  => true,
	      'allowEmpty'=> false,
	      'label' => 'Choose Feedback'
	    ));
		$this -> listFeedback ->setRegisterInArrayValidator(false);
		
		//title
	    $this->addElement('Text', 'title', array(
	      'label' => 'Title',
	      'description' => 'Edit if necessary',
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
		
		//description
		$this->addElement('Textarea', 'description', array(
	        'label' => 'Description',
	        'description' => 'Edit if necessary',
	        'allowEmpty' => false,
	      	'required' => true,
	        'filters' => array(
	        	new Engine_Filter_HtmlSpecialChars(),
	        	new Engine_Filter_Censor(),
	            new Engine_Filter_EnableLinks(),
       		 ),
	        'validators' => array(
		        array('NotEmpty', true),
			),
	    ));
		
		//Heading
		$this ->addElement('heading', 'heading_5', array(
			'description' => 'All settings of this feedback will be used for the new one',
		));
		
		//Heading
		$this ->addElement('heading', 'heading_3', array(
			'label' => '3. Select feedback owner',
		));
		
		//list owners
		 $this->addElement('Select', 'listOwner', array(
		  'required'  => true,
		  'label' => 'Choose Owner',
	      'allowEmpty'=> false,
	      'description' => 'Other owners will be co-authors of this feedback',
	    ));
		$this -> listOwner -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
		$this -> listOwner ->setRegisterInArrayValidator(false);		
		
		//Heading
		$this ->addElement('heading', 'heading_4', array(
			'label' => '4. Feedback options',
		));
		
		$this->addElement('Checkbox', 'send_notification', array(
	      'label' => 'Send notifications to inform owner and co-authors about new feedback',
	      'checkedValue' => '1',
	      'uncheckedValue' => '0',
	      'value' => 1,
	    ));
		
		$this->addElement('Checkbox', 'move_activity', array(
	      'label' => 'Move all activities from old feedbacks to this new one (including likes, comments, followers, votes)',
	      'checkedValue' => '1',
	      'uncheckedValue' => '0',
	      'value' => 1,
	    ));
		
		$this->addElement('Checkbox', 'move_material', array(
	      'label' => 'Move all materials from old feedbacks to this new one (including files and screenshots)',
	      'checkedValue' => '1',
	      'uncheckedValue' => '0',
	      'value' => 1,
	    ));
		
		// Buttons
	    $this->addElement('Button', 'submit_button', array(
	      'label' => 'Save',
	      'onclick' => 'removeSubmit()',
	      'type' => 'submit',
	      'ignore' => true,
	      'values' => 1,
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
		
	   $this->addElement('Cancel', 'cancel', array(
	      'label' => 'cancel',
	      'link' => true,
	      'prependText' => ' or ',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	    ));
	
	    $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
	      'decorators' => array(
	        'FormElements',
	        'DivDivDivWrapper',
	      ),
	    ));
	}

}
