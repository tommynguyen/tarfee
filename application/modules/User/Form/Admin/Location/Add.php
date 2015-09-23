<?php
class User_Form_Admin_Location_Add extends Engine_Form {
  	protected $_id = 0;
	
	public function setId($id) {
		$this->_id = $id;
	}
	
	public function getId($id) {
		return $this->_id;
	}
  	public function init() {
  		
		$level = 0;
		$location = Engine_Api::_()->getItem('user_location', $this->getId($id));
		if ($location) $level = intval($location->level) + 1;
		$item = array('Country', 'Province/State', 'City');
		$this->setTitle('Add '.$item[$level]);
    	$this->setMethod('post');

    	$label = new Zend_Form_Element_Text('title');
		$label->setLabel('Title')
      	->addValidator('NotEmpty')
      	->setRequired(true)
      	->setAttrib('class', 'text');


    	$id = new Zend_Form_Element_Hidden('id');


    	$this->addElements(array(
      	//$type,
      		$label,
      		$id
    	));
    	
		$this->addElement('Select', 'continent', array(
			'label' => 'Continent',
			'multiOptions' => array(
				'Africa' => 'Africa',
				'North America' => 'North America',
				'South America' => 'South America',
				'Asia' => 'Asia',
				'Oceania' => 'Oceania',
				'Europe' => 'Europe',
			)
		));
		
    	// Buttons
    	$this->addElement('Button', 'submit', array(
      		'label' => 'Add',
      		'type' => 'submit',
      		'ignore' => true,
      		'decorators' => array('ViewHelper')
    	));

    	$this->addElement('Cancel', 'cancel', array(
      		'label' => 'cancel',
      		'link' => true,
      		'prependText' => ' or ',
      		'href' => '',
      		'onClick'=> 'javascript:parent.Smoothbox.close();',
      		'decorators' => array(
        		'ViewHelper'
      		)
    	));
    
    	$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    	$button_group = $this->getDisplayGroup('buttons');

   		// $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  	}

  	public function setField($location) {
    	// Set up elements
    	//$this->removeElement('type');
    	$this->setTitle('Edit');
    	$this->title->setValue($location->title);
    	$this->id->setValue($location->location_id);
		if (isset($this->continent)) $this->continent->setValue($location->continent);
    	$this->submit->setLabel('Edit');

    	// @todo add the rest of the parameters
  	}
}