<?php
class User_Form_Admin_Player_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    if( !$this->isPublic() ) {

      // Element: max
      $this->addElement('Text', 'max_player_card', array(
        'label' => 'Maximum Allowed Player Cards',
        'description' => 'Enter the maximum number of allowed player cards. The field must contain an integer.',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
      ));
	  
	// Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Player Card Privacy',
        'description' => 'Your members can choose from any of the options checked below when they decide who can see their player card. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'owner_network'       => 'Followers and Networks',
          'owner_member'        => 'My Followers',
          'owner'               => 'Only Me',
        ),
        'value' => array('everyone', 'owner_network', 'owner_member', 'owner'),
      ));
    }
    
  }
}