<?php
class User_Form_Playercard_Edit extends User_Form_Playercard_Create
{
  public function init()
  {
    parent::init();
	$this -> setTitle('Edit Player Card');
	$this->submit->setLabel('Save Changes');
  }
}