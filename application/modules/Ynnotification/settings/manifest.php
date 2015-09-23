<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynnotification',
    'version' => '4.01p2',
    'path' => 'application/modules/Ynnotification',
    'title' => 'Advanced Notification',
    'description' => 'Advanced Notification',
    'author' => 'YouNet Company',
    'callback' => 
    array (
      'path' => 'application/modules/Ynnotification/settings/install.php',
      'class' => 'Ynnotification_Installer',
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
      0 => 'application/modules/Ynnotification',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynnotification.csv',
    ),
  		
	'routes' =>
		array (
				'ynnotification_general' =>
				array (
						'route' => 'ynnotification/:action/*',
						'defaults' =>
						array (
								'module' => 'ynnotification',
								'controller' => 'index',
								'action' => 'index',
						),
						'reqs' =>
						array (
								'action' => '\D+',
						),
				),		
		),
  ),
); ?>