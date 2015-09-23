<?php
class Ynevent_Form_Check extends Engine_Form
{
  public function init()
  {
	$this
	  ->setTitle('Edit apply for')
	  ->setAttrib('id', 'ynevent_kind')
	  ->setDescription('Please choose the type of event to edit')
	  ->setAttrib('class', 'global_form_popup ynevent_repeat_form')	
	  ->setAttrib('style', 'width:250px;')	
      ->setMethod('POST')	  
      ->setAction($_SERVER['REQUEST_URI'])
      ;
	    
	$this -> addElement('Radio', 'apply_for', array(           
            'multiOptions' => array(
                '0' => 'Only this event',
                '1' => 'All events',
                '2' => 'Following events',
            ),
            'value' => 0,   
            'decorators' => array('ViewHelper')
        ));
		
	// Buttons
    $this->addElement('Button', 'Save', array(
      'label' => 'Confirm',
      'type' => 'button',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
      'onclick'=>'myselect();',
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(     
      'link' => true,
      'label' => 'Cancel',
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	$this->addDisplayGroup(array('Save', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}