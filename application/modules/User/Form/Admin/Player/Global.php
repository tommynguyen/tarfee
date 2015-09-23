<?php
class User_Form_Admin_Player_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'user_relation_require', array(
      'label' => 'Player Card - Relation',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.relation_require', 1),
      'multiOptions' => array(
        '1' => 'Mandatory.',
        '0' => 'Optional.',
      ),
    ));
	
	$this->addElement('Text', 'user_min_year', array(
      'label' => 'Minimum year displayed',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.min_year', 1985),
    ));
	
	$this->addElement('Text', 'user_max_year', array(
      'label' => 'Maximum year displayed',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('user.max_year', 2003),
    ));
    
    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}