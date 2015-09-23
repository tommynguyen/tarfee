<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'sladvsubscription',
    'version' => '1.1.0',
    'path' => 'application/modules/Sladvsubscription',
    'title' => 'SocialLOFT\'s Advsubscription ',
    'description' => 'SocialLOFT\'s Advsubscription ',
    'author' => 'SocialLOFT',
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
      0 => 'application/modules/Sladvsubscription',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/sladvsubscription.csv',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Sladvsubscription_Plugin_Core',
    ),
    array(
      'event' => 'onAuthorizationLevelCreateAfter',
      'resource' => 'Sladvsubscription_Plugin_Core',
    ),
  ),
  'routes' => array(
  	'choose_package' => array(
      'route' => 'payment/subscription',
      'defaults' => array(
        'module' => 'sladvsubscription',
        'controller' => 'subscription',
        'action' => 'index'
      )
    ),
    'update_package' => array(
     'route' => 'payment/settings',
      'defaults' => array(
        'module' => 'sladvsubscription',
        'controller' => 'settings',
        'action' => 'index'
      )
    )
    ,
    'update_package_id' => array(
     'route' => 'payment/settings/:id',
      'defaults' => array(
        'module' => 'sladvsubscription',
        'controller' => 'settings',
        'action' => 'index'
      ),
      'reqs' => array(
      	'id' => '\d+',
      )
    ) 
  )
); ?>