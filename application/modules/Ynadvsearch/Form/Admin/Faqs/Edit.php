<?php
class Ynadvsearch_Form_Admin_Faqs_Edit extends Ynadvsearch_Form_Admin_Faqs_Create {
  public function init() {
     parent::init();
    $this->setTitle('Edit FAQ');
    $this->setDescription('YNADVSEARCH_FAQS_EDIT_DESCRIPTION');
    $this->submit->setLabel('Edit FAQ');
  }
}