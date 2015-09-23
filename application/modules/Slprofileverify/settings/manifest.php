<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'slprofileverify',
    'version' => '1.1.1',
    'path' => 'application/modules/Slprofileverify',
    'title' => 'SocialLOFT\'s profile verify',
    'description' => 'SocialLOFT\'s profile verify',
    'author' => 'SocialLOFT',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
      array(
         'type' => 'module',
         'name' => 'socialloft',
         'minVersion' => '1.0.0',
      ),
    ),
    'callback' => array(
      'path' => 'application/modules/Slprofileverify/settings/install.php',
      'class' => 'Slprofileverify_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Slprofileverify',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/slprofileverify.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
    'items' => array(
        'slprofileverify',
        'slprofileverify_slprofileverify',
        'slprofileverify_reason',
        'slprofileverify_user',
    ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserEnable',
      'resource' => 'Slprofileverify_Plugin_Core',
    ),
    array(
      'event' => 'onFieldsValuesSave',
      'resource' => 'Slprofileverify_Plugin_Core',
    )
  ),
); ?>