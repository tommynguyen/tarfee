<?php
class Ynblog_Form_Edit extends Ynblog_Form_Create
{
/*----- Init Form Function -----*/
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Talk')
      ->setDescription('Edit your entry below, then click "Post Entry" to publish the entry on your talk.');
    $this->submit->setLabel('Save Changes');
    $captcha = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.captcha',0);
    if($captcha){
      $this->removeElement('captcha');
    }
  }
}