<?php
class Ynevent_Form_Filter_Manage extends Engine_Form
{
  public function init()
  {
    $this->clearDecorators()
      ->addDecorators(array(
        'FormElements',
        array('HtmlTag', array('tag' => 'dl')),
        'Form',
      ))
      ->setMethod('get');

    $this->addElement('Text', 'text', array(
      'label' => 'Search:',
      'alt' => 'Search events',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'dd')),
        array('Label', array('tag' => 'dt', 'placement' => 'PREPEND'))
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));

    $this->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '' => 'All My Events',
        '2' => 'Only Events I Lead',
      ),
     
      'onchange' => '$(this).getParent("form").submit();',
    ));
  }
}