<?php
class User_Form_Admin_Referral_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription('These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.');

    if( !$this->isPublic() ) 
    {

      // Element: max
      $this->addElement('Text', 'max_referral', array(
        'label' => 'Number of referral codes',
        'description' => 'Enter the maximum number of allowed referral codes. The field must contain an integer, use zero for unlimited.',
        'validators' => array(
          array('Int', true),
          new Engine_Validate_AtLeast(0),
        ),
        'value' => 5,
      ));
    }
    // Element: view
    $this->addElement('Radio', 'allow_referral', array(
      'label' => 'Allow Referral Code Generation?',
      'description' => 'Do you want to have access to Referral Code Generation?',
      'multiOptions' => array(
        1 => 'Yes, allow to have access.',
        0 => 'No, do not allow to have access.',
      ),
      'value' => 1,
    ));
  }
}