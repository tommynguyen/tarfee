<?php
class User_Form_Edit_CropPhoto extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('enctype', 'multipart/form-data')
      ->setAttrib('name', 'CropPhoto');

    $this->addElement('Image', 'current', array(
      'label' => 'Main Photo',
      'ignore' => true,
      'decorators' => array(array('ViewScript', array(
        'viewScript' => '_formCropImage.tpl',
        'class'      => 'form element',
        'testing' => 'testing'
      )))
    ));
    Engine_Form::addDefaultDecorators($this->current);
    

    $this->addElement('Hidden', 'coordinates', array(
      'filters' => array(
        'HtmlEntities',
      )
    ));

    $this->addElement('Button', 'done', array(
      'label' => 'Save Photo',
      'type' => 'submit',
      'onsubmit' => 'lassoEnd()',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
	$this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => 'javascript:void(0)',
      'onclick' => 'parent.Smoothbox.close()',
      'class' => 'smoothbox',
      'decorators' => array(
        'ViewHelper'
      ),
    ));

    $this->addDisplayGroup(array('done', 'cancel'), 'buttons');
  }
}