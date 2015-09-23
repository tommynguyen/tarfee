<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'socialloft',
    'version' => '1.1.0',
    'path' => 'application/modules/Socialloft',
    'title' => 'SocialLOFT\'s Core',
    'description' => 'SocialLOFT\'s Core',
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
    'directories' => 
    array (
      0 => 'application/modules/Socialloft',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/socialloft.csv',
    ),
  ),
); ?>