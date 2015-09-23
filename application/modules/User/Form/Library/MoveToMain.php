<?php
class User_Form_Library_MoveToMain extends Engine_Form
{
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	
    $this->setTitle('Move Video');
    $this->setAttrib('class', 'global_form_popup');
    $this->setDescription('Are you sure that you want to move this video?');
	
	$move_type = array('main' => 'Main');
	if (Engine_Api::_()->user()->canTransfer()) {
		$move_type['group'] = 'Club';
	}
	
	$this->addElement('Select', 'move_type', array(
		'label' => 'Move To',
		'multiOptions' => $move_type
	));
	
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Move',
      'onclick' => 'removeSubmit()',
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
    
	 $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
