<?php
class User_Form_Library_Edit extends User_Form_Library_Create
{
	
  public function init()
  {
	parent::init();
	
    $this->setTitle('Edit Library');
	$this->submit_button->setLabel('Save Changes');
	
  }
}
