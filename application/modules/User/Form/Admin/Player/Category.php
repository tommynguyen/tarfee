<?php
class User_Form_Admin_Player_Category extends Engine_Form
{
  protected $_field;

  public function init()
  {
    $this->setMethod('post');
  
   $this->addElement('Hidden','id');
     //Location Name - Required
   $this->addElement('Text','label',array(
      'label'     => 'Sport Category Name',
      'required'  => true,
      'allowEmpty'=> false,
    ));
	
	$this -> addElement('File', 'photo', array('label' => 'Icon'));
	$this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
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
      'onClick'=> 'javascript:parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

  public function setField($category)
  {
    $this->_field = $category;
    // Set up elements
    $this->label->setValue($category->title);
    $this->id->setValue($category->sportcategory_id);
    $this->submit->setLabel('Edit Category');

  }
}