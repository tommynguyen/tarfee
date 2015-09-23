<?php
class Ynnotification_Form_Admin_Style extends Engine_Form
{
  public function init()
  {
  	    $view = Zend_Registry::get('Zend_View');
	    $view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynnotification/externals/scripts/jscolor.js');
    $this
      ->setTitle('Style Settings')
      ->setDescription('These settings affect all theme in your community.')
	  ->setAttrib('style',"width: 600px");
	  
	
	//Change style for Main Menu drop down
	// $this->addElement('dummy', 'not_massage',array(
      // 'label'=>'Notification Message',
     // ));
	 $this->addElement('Text', 'mes_background', array(
      'label' => 'Message Background color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "79B4D4",
    ));
	 $this->addElement('Text', 'text_color', array(
	 	'label' => 'Text color',
	 	'class' => 'color',
	 	'allowEmpty' => false,
	 	'value' => "",
	 ));
	
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
	// Clear submit button
    $this->addElement('Button', 'clear', array(
      'label' => 'Set Default',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}