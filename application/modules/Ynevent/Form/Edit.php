<?php

class Ynevent_Form_Edit extends Ynevent_Form_Create
{
	public function init()
	{
		parent::init();
		$this
			->setTitle('Edit Event')
			->setAttrib('id', 'ynevent_create_form');
		$this->save_change->setLabel('Save Changes');
	}
}