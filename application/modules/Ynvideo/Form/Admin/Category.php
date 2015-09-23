<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Admin_Category extends Engine_Form {
    protected $_isEditing;
    protected $_category;

    public function __construct($options = null) {
        if (is_array($options) && array_key_exists('isEditing', $options)) {
            $this->_isEditing = $options['isEditing'];
            unset($options['isEditing']);
        }
        if (is_array($options) && array_key_exists('category', $options)) {
            $this->_category = $options['category'];
            unset($options['category']);
        }

        parent::__construct($options);
    }
    
    public function init() {
        $this->setMethod('post');
        $this->setAttrib('enctype', 'multipart/form-data');

        // add input text field category name
        $label = new Zend_Form_Element_Text('label');
        $label->setLabel('Category Name')
                ->addValidator('NotEmpty')
                ->setRequired(true)
                ->setAttrib('class', 'text');

        // add input drop down list parent category
        if (!$this->_isEditing) {
            $parent = new Zend_Form_Element_Select('parent_id');
            $parent->setLabel('Parent Category');
        }
        
        // add file input for the category photo
        $icon = new Zend_Form_Element_File('photo');
        $icon->setLabel('Attach an icon (The icon will be resized to 24px * 24px)')
                ->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        
        $id = new Zend_Form_Element_Hidden('id');

        $this->addElements(array(
            $parent,
            $label,
            $icon,
            $id,
        ));
        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Add Category',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');

        // $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
        $this->_initValueForElements();
    }

    protected function _initValueForElements() {
        if ($this->_category) {
            $this->label->setValue($this->_category->category_name);
            $this->id->setValue($this->_category->category_id);
        }
        if ($this->_isEditing) {
            $this->submit->setLabel('Edit Category');
        }
    }
}