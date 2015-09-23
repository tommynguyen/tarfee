<?php return array (
  'package' =>
  array (
    'type' => 'module',
    'name' => 'profile-completeness',
    'version' => '4.01p6',
    'path' => 'application/modules/ProfileCompleteness',
    'title' => 'Profile Completeness',
    'description' => 'Display the percentage of your profile completeness.',
    'changeLog' => 'settings/changelog.php',
    'author' => 'YouNet Company',
    'callback' =>
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.0',
      ),
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' =>
    array (
      0 => 'application/modules/ProfileCompleteness',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/profile-completeness.csv',
    ),
    // Items ---------------------------------------------------------------------
  'items' => array(

  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(

  ),
  ),
); ?>