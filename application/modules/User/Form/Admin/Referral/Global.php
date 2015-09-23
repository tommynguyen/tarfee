<?php
class User_Form_Admin_Referral_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'user_referral_enable', array(
      'label' => 'Referral Program',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user_referral_enable', 1),
      'multiOptions' => array(
        '1' => 'Activate.',
        '0' => 'Deactivte.',
      ),
    ));
	
	$this->addElement('Text', 'user_referral_trial',array(
	      'label'=>'Trial period of referral codes (day)',
	      'description' => '0 is unlimited',
	      'filters' => array(
	        new Engine_Filter_Censor(),
	      ),
	      'validators' => array(
	          array('Int', true),
	          new Engine_Validate_AtLeast(0),
	        ),
	     'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('user_referral_trial', 0),
    ));	
	
    $this->getElement('user_referral_trial')->getDecorator("Description")->setOption("placement", "append");
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}