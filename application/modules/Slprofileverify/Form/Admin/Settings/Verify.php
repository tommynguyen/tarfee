<?php

// Dev Tan SocialLoft
class Slprofileverify_Form_Admin_Settings_Verify extends Engine_Form
{
  public function init()
  {

    $this->setTitle($this->getView()->translate("Indentity Verification Settings"));
    
    $this->addElement('Checkbox', 'firstname', array(
      'label' => $this->getView()->translate("First name"),
      'description' => $this->getView()->translate("Verified info"),
    ));
    
    $this->addElement('Checkbox', 'lastname', array(
        'label' => $this->getView()->translate("Last name")
     ));    
     
    
    $this->addElement('Checkbox', 'gender', array(
        'label' => $this->getView()->translate("Gender")
     ));
    
    $this->addElement('Checkbox', 'birthdate', array(
        'label' => $this->getView()->translate("Date of birth")
     ));
    
    $this->addElement('Checkbox', 'profile_picture', array(
        'label' => $this->getView()->translate("Profile picture")
     ));
    
    $this->addElement('File', 'file_id', array(
      'label' => $this->getView()->translate("Scan ID sample")
    ));
    $this->file_id->setMultiFile(4);
    $this->file_id->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    
    $this->addElement('Button', 'submit', array(
        'label' => $this->getView()->translate("Save Changes"),
        'type' => 'submit',
        'ignore' => true
    ));
  }
}