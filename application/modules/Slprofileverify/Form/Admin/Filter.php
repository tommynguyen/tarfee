<?php

class Slprofileverify_Form_Admin_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET');

    $displayname = new Zend_Form_Element_Text('displayname');
    $displayname
      ->setLabel('Display Name')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $username = new Zend_Form_Element_Text('username');
    $username
      ->setLabel('Username')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $email = new Zend_Form_Element_Text('email');
    $email
      ->setLabel('Email')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->getLevelsAssoc();
    $levelMultiOptions = array(0 => ' ');
    foreach ($levels as $key => $value) {
      $levelMultiOptions[$key] = $value;
    }
    
    $level_id = new Zend_Form_Element_Select('level_id');
    $level_id
      ->setLabel('Level')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions($levelMultiOptions);

    $enabled = new Zend_Form_Element_Select('enabled');
    $enabled
      ->setLabel('Status')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'))
      ->setMultiOptions(array(
        'pending' => 'Pending',
        'verified' => 'Verified',
      ))
      ->setValue('pending');

    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
      ->setLabel('Search')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
      ->addDecorator('HtmlTag2', array('tag' => 'div'));
    
    $this->addElements(array(
      $displayname,
      $username,
      $email,
      $level_id,
      $enabled,
      $submit,
    ));

    // Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}