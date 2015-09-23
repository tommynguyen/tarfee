<?php
class Tfcampaign_Form_UnHide extends Engine_Form
{
  public function init()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
	$view = Zend_Registry::get("Zend_View");
    $this -> setTitle('UnHide Player');
	$this -> setAttrib('class', 'global_form_popup');
	$this -> setDescription("Are you sure to unhide this player?");
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'UnHide',
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
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}