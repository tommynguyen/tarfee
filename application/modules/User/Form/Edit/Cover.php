<?php
class User_Form_Edit_Cover extends Engine_Form {
    public function init() {
        $this
          ->setTitle('Upload Cover Photo')
          ->setAttrib('class', 'global_form_popup')
          ;
        
        
        $this->addElement('File', 'photo', array(
            'label' => 'Cover Photo',
            'required' => true,
            'allowEmpty' => false,
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
            
        // Buttons
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Photo',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array('ViewHelper')
        ));
    
        $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'href' => '',
          'onclick' => 'parent.Smoothbox.close();',
          'decorators' => array(
            'ViewHelper'
          )
        ));
        
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons');
  }
}

