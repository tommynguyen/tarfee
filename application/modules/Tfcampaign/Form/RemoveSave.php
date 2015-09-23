<?php
class Tfcampaign_Form_RemoveSave extends Engine_Form
{
  
  public function init()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
	$view = Zend_Registry::get("Zend_View");
	$viewer = Engine_Api::_() -> user() -> getViewer();
    $this -> setTitle('Remove Saved Campaign');
	$this -> setAttrib('class', 'global_form_popup');
	$this -> setDescription("Are you sure to remove this campaign?");
	
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Remove',
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