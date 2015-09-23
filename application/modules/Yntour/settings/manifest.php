<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'yntour',
    'version' => '4.02p3',
    'path' => 'application/modules/Yntour',
    'title' => 'YN - Tour Guide',
    'description' => 'Tour guide plugin',
    'author' => 'YouNet Company',
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
      0 => 'application/modules/Yntour',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/yntour.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p1',
      ),
    ),
  ),
) ; ?>