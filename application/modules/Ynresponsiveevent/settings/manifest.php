<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynresponsiveevent',
    'version' => '4.01p4',
    'path' => 'application/modules/Ynresponsiveevent',
    'title' => 'YN - Responsive Event Template',
    'description' => 'YN - Responsive Event Template',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => 
    array (
       'path' => 'application/modules/Ynresponsiveevent/settings/install.php',
       'class' => 'Ynresponsiveevent_Installer',
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
      0 => 'application/modules/Ynresponsiveevent',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynresponsiveevent.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p7',
      ),
    ),
  ),
  'items' => 
  array (
    0 => 'ynresponsiveevent_event',
    1 => 'ynresponsiveevent_sponsor',
   ),
  'routes' => array(
    'ynresponsive_event' => array(
      'route' => 'responsive-event/:action/*',
      'defaults' => array(
        'module' => 'ynresponsivevent',
        'controller' => 'index',
        'action' => 'event'
      ),
    ),
    'ynresponsive_event_listtng' => array(
      'route' => 'responsive-event/listing/*',
      'defaults' => array(
        'module' => 'ynresponsiveevent',
        'controller' => 'index',
        'action' => 'event'
      ),
    ),
  ),
); ?>