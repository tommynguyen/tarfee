<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'ynresponsive-event',
    'version' => '4.01p4',
    'path' => 'application/themes/ynresponsive-event',
    'repository' => 'younetco.com',
    'title' => 'YN - Responsive Event Template',
    'thumb' => 'theme.jpg',
    'author' => 'YouNet Company',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'ynresponsive1',
         'minVersion' => '4.04',
      ),
      array(
         'type' => 'module',
         'name' => 'ynresponsiveevent',
         'minVersion' => '4.01p4',
      ),
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/themes/ynresponsive-event',
      1 => 'application/themes/configure/default',
      2 => 'application/themes/configure/ynresponsive-event',
    ),
    'description' => 'YouNet Responsive Event Template',
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>