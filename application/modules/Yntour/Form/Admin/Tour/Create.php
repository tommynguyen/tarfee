<?php

class Yntour_Form_Admin_Tour_Create extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Edit Tour Guide')
        ->setAttrib('class','global_form')
        -> setDescription('Please compose your new tour guide below.') -> setAttrib('id', 'tour_create');

        // Add title
        $this -> addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Select', 'autoplay', array(
            'label' => 'Auto play',
            'value'=>0,
            'multiOptions'=>array(
                '0'=>'No, do not autoplay',
                '1'=>'Yes, Auto play'
            )
        ));
        
        
        // Add title
        $this -> addElement('Text', 'path', array(
            'label' => 'Link URL',
            'required' => true,
            'allowEmpty' => false,
        ));
        
        $this -> addElement('Text', 'bodyid', array(
            'label' => 'Page ID',
            'required'=>false,
            'allowEmpty' => true,
        ));
        
        $this -> addElement('Radio', 'option', array(
            'label' => 'Select Link URL or Page ID to setting Tour Guide',
            'value'=>0,
            'multiOptions'=>array(
                '0'=>'Use this url',
                '1'=>'Use Page id'
            )
        ));
        
        $this->addElement('Select','enabled',array(
            'label'=>'Enabled',
            'value'=>1,
            'multiOptions'=>array(
                '0'=>'Disable',
                '1'=>'Enable',
            ),
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
    
        /*
        $this -> addElement('TinyMce', 'body', array(
            'label' => 'Body',
            'required' => true,
            'allowEmpty' => false,
        ));
        */
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
            'href' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
                'module' => 'yntour',
                'controller' => 'manage',
                'action' => 'index'
            ), 'admin_default', true),
            'prependText' => Zend_Registry::get('Zend_Translate') -> _(' or '),
            'decorators' => array('ViewHelper', ),
        ));

        $this -> addDisplayGroup(array(
            'submit',
            'cancel'
        ), 'buttons');
    }

}
