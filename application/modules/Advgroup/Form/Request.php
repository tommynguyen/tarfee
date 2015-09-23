<?php
class Advgroup_Form_Request extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $this
      ->setTitle('Verification Request')
	  ->setDescription('ADVGROUP_FORM_REQUEST_DESCRIPTION')
	  ->setAttrib('class', 'global_form_popup');

    $this->addElement('Textarea', 'description', array(
      'label' => 'Message',
      'description' => 'Maximum 300 characters',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 300)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this->description->setAttrib('required', true);
	$this->description->getDecorator('Description')->setOption('placement', 'append');
	
     // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Request',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
