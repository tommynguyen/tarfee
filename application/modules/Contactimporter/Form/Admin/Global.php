<?php
class Contactimporter_Form_Admin_Global extends Engine_Form
{
	public function init()
	{
		$this -> setTitle('Global Settings') -> setDescription('These settings affect all members in your community.');

		// Contacts per page
		$this -> addElement('Text', 'contactsPerPage', array(
			'label' => 'Contacts Per Page',
			'description' => 'How many Contacts will be shown per page? (Enter a number between 1 and 999)',
			'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('contactimporter.contactsPerPage', 30),
			'validators' => array(new Zend_Validate_Between(1, 999))
		));
		//ADD MESSAGE CHANGE
		$this -> addElement('Textarea', 'message', array(
			'label' => 'Default invite message',
			'description' => '',
			'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('invite.message', ''),

			'filter' => array('StripTags', ),
		));
		// Add submit button
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true
		));
	}

	public function saveValues()
	{
		$values = $this -> getValues();
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$settings -> invite_message = $values['message'];
		$settings -> contactimporter_contactsPerPage = $values['contactsPerPage'];

	}

}
