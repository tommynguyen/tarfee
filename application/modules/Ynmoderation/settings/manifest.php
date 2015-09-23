<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynmoderation',
    'version' => '4.02',
    'path' => 'application/modules/Ynmoderation',
    'title' => 'Moderation',
    'description' => 'Moderation',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynmoderation/settings/install.php',
      'class' => 'Ynmoderation_Package_Installer',
    ),
    'dependencies' =>
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.0',
      ),
      1 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.01',
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
      0 => 'application/modules/Ynmoderation',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynmoderation.csv',
    ),
  ),
  'items' => 
  array (
    0 => 'ynmoderation_module',
  ),
);?>