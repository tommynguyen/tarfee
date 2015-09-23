<?php
class User_Form_Photo_Upload extends Engine_Form
{
	public function init()
	{
		$this -> setAttrib("class", "global_form_popup");
		// Init form
		$this -> setTitle('Add New Photos') -> setDescription('Choose photos on your computer to add to this album.') -> setAttrib('id', 'form-upload') -> setAttrib('name', 'albums_create') -> setAttrib('enctype', 'multipart/form-data');
		
		$this -> addElement('Dummy', 'html5_upload', array('decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_Html5Upload.tpl',
						'class' => 'form element',
					)
				)), ));
		$this -> addElement('Hidden', 'html5uploadfileids', array('value' => '', 'order' => 1));

		// Init submit
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Photos',
			'type' => 'submit',
			'onclick' => 'disable()',
		));
	}



}
