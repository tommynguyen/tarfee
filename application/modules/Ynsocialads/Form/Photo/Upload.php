<?php
class Ynsocialads_Form_Photo_Upload extends Engine_Form
{
	public function init()
	{
		// Init form
		$this 
		-> setTitle('Add More Photos') 
		-> setAttrib('id', 'form-upload') 
		-> setAttrib('class', 'global_form event_form_upload') 
		-> setAttrib('name', 'albums_create') 
		-> setAttrib('enctype', 'multipart/form-data') 
		-> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));
		
		$this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_Html5Upload.tpl',
						'class' => 'form element',
					)
				)), ));
		$this -> addElement('Hidden', 'ad_id', array('order' => 1));
		$this -> addElement('Hidden', 'html5uploadfileids', array(
			'value' => '',
			'order' => 2
		));

		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Photos',
			'type' => 'submit',
		));
	}

}
