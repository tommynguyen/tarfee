<?php
class Ynresponsiveevent_Form_Sponsor_Edit extends Ynresponsiveevent_Form_Sponsor_Create
{
 	public function init()
	{
		parent::init();
		$this ->setTitle('Edit Sponsor Logo');
		$this -> removeElement('to');
		$this -> removeElement('toValues');
	}
}