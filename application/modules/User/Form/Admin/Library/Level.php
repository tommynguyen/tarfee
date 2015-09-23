<?php


class User_Form_Admin_Library_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Libraries?',
      'description' => 'Do you want to let members view libraries? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all libraries, even private ones.',
        1 => 'Yes, allow viewing of libraries.',
        0 => 'No, do not allow libraries to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {


      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Libraries Privacy',
        'description' => 'Your members can choose from any of the options checked below when they decide who can see their library. If you do not check any options, settings will default to the last saved configuration. If you select only one option, members of this level will not have a choice.',
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