<?php

class Yntour_Form_Admin_Touritem_Delete extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Delete Tour Guide Step')->setAttrib('class','global_form_popup')
      ->setDescription('Are you sure you want to delete this tour guide step?');

    // Buttons
        $this -> addElement('Button', 'submit', array(
            'label' => 'Delete',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this -> addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'ignore' => true,
            'link' => true,
            'href' =>'javascript: parent.Smoothbox.close()',
            'prependText' => Zend_Registry::get('Zend_Translate') -> _(' or '),
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
       
    }
  
}