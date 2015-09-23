<?php
class Tfcampaign_Form_Hide extends Engine_Form
{
  public function init()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
	$view = Zend_Registry::get("Zend_View");
    $this -> setTitle('Hide with reason');
	$this -> setAttrib('class', 'global_form_popup');
	$this -> setDescription("Are you sure to hide this player?");
	
	$reasonTable = Engine_Api::_() -> getDbTable('reasons', 'tfcampaign');
	$reasonArr = $reasonTable -> getReasonArray();
	
	if(!empty($reasonArr)) {
		$this->addElement('Select', 'reason_id', array(
			'multiOptions' => $reasonArr,
			'description' => $view -> translate("Select your reason"),
		));
	}
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Hide',
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