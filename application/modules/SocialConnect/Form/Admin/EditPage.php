<?php
class SocialConnect_Form_Admin_EditPage extends SocialConnect_Form_Admin_CreatePage
{
  public function init()
  {
     parent::init();
    $this->setTitle('Edit Page');
    $this->submit->setLabel('Edit Page');
  }
}