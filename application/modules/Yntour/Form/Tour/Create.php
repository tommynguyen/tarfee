<?php

class Yntour_Form_Tour_Create extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Edit Tour Guide')
        ->setAttrib('class','global_form_popup')        
        -> setDescription('Please compose your tour guide.')
        -> setAttrib('id', 'tour_create');

        // Add title
        $this -> addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'allowEmpty' => false,
        ));
        
        // Add title        
        
        
        
        
        $this -> addElement('Select', 'autoplay', array(
            'label' => 'Auto play',
            'value'=>0,
            'multiOptions'=>array(
                '0'=>'No, do not autoplay',
                '1'=>'Yes, Auto play'
            )
        ));

        $this -> addElement('Select', 'view_rule', array(
            'label' => 'Enable for',
            'value'=>'all',
            'multiOptions'=>array(
                'all'=>'All',
                'members'=>'Members',
                'guests'=>'Guests'
            ),
        ));
        
        $this->addElement('Select','enabled',array(
            'label'=>'Enabled',
            'value'=>1,
            'style' => 'width: 90px',
            'multiOptions' =>array(
                '0'=>'Disable',
                '1'=>'Enable',
            ),
        ));
       
       $this -> addElement('Radio', 'option', array(
            'label' => 'Option',
            'value'=>0,
            'multiOptions'=>array(
                '0'=>'Use this url',
                '1'=>'Use Page id'
            )
        ));
       
        $this -> addElement('Hidden', 'path', array('order'=>1));
        $this -> addElement('Hidden', 'bodyid', array('order'=>2)); 
        
        
        // Buttons
        $this -> addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this -> addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'ignore' => true,
            'link' => true,
            'href' => 'javascript:parent.Smoothbox.close()',
            'prependText' => Zend_Registry::get('Zend_Translate') -> _(' or '),
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }

}
