<?php

class Slprofileverify_Form_Admin_Reason extends Engine_Form
{
  public function init()
  {
    $this->setMethod('post')
         ->setTitle($this->getView()->translate("Add Reason"))   
         ->setAttrib('class', 'global_form_box');
    
    $this->addElement('Textarea', 'description', array(
        'label' => $this->getView()->translate("Description reason"),
        'class' => 'text'
    ));
    
    $this->addElement('Hidden', 'id', array());
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => $this->getView()->translate("Add"),
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => $this->getView()->translate("Cancel"),
      'link' => false,
      'prependText' => $this->getView()->translate("or") . " ",
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array('ViewHelper')
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->getDisplayGroup('buttons');
  }

  public function setField($reason)
  {
    $this->setTitle($this->getView()->translate("Edit Reason"));
    $this->description->setValue($reason->description);
    $this->id->setValue($reason->reason_id);
    $this->submit->setLabel('Edit');
  }
}