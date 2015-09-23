<?php
class Ynresponsiveevent_Form_Event_Edit extends Ynresponsiveevent_Form_Event_Create
{
 	public function init()
	{
		parent::init();
		$this ->setTitle('Edit Event');
		$this -> removeElement('to');
		$this -> removeElement('toValues');
	}
}