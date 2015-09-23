<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynbanmem',
    'version' => '4.01p3',
    'path' => 'application/modules/Ynbanmem',
    'title' => 'Member Management',
    'description' => 'Member Management',
    'author' => 'YouNet Company',
	'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
	  array(
        'type' => 'module',
        'name' => 'user',
        'minVersion' => '4.1.7',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
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
      0 => 'application/modules/Ynbanmem',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynbanmem.csv',
    ),
  ),
  'hooks' => 
  	array( 
  		array(
				'event' => 'onUserLoginAfter',
				'resource' => 'Ynbanmem_Plugin_Core',
		), 
		array(
		      	'event' => 'onRenderLayoutDefault',
		      	'resource' => 'Ynbanmem_Plugin_Core',
	   ),
		
	),
     // Routes --------------------------------------------------------------------
  'routes' => array(
    // Ynbanmem - General
    'ynbanmem_general' => array(
      'route' => 'banmem/:controller/:action/*',
      'defaults' => array(
        'module' => 'ynbanmem',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
	 
      ),
    
    
); ?>