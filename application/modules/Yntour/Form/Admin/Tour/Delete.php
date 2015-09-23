<?php

class Yntour_Form_Admin_Tour_Delete extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Delete Selected Tour Guide')
        ->setAttrib('class','global_form_popup')
         -> setDescription('Are you sure you want to delete this guide?');

        
        $this -> addElement('Button', 'submit', array(
            'label' => 'Delete',
            'type' => 'submit',
            'decorators' => array('ViewHelper')
        ));
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
