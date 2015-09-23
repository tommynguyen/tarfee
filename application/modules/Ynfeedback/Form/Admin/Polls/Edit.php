<?php
class Ynfeedback_Form_Admin_Polls_Edit extends Ynfeedback_Form_Admin_Polls_Create
{
  public function init()
  {
    parent::init();

    $this->setTitle('Edit Poll');
    $this->submit->setLabel('Save Changes');
  }
}