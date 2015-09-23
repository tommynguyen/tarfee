<?php
class Advgroup_Form_Style extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Club Styles')
      ->setMethod('post')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setAttrib('class', 'global_form_popup')
      ;

    $this->removeDecorator('FormWrapper');

    $this->addElement('Textarea', 'style', array(
      'label' => 'Custom Club Styles',
      'description' => 'You can change the colors, fonts, and styles of your club by adding CSS code below. The contents of the text area below will be output between <style> tags on your club.'
    ));
    $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $this->addElement('Hidden', 'id');
  }
}