<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'minify',
    'version' => '4.03p3',
    'path' => 'application/modules/Minify',
    'title' => 'Minify',
    'description' => 'It combines multiple CSS or Javascript files, removes unnecessary whitespace and comments, and serves them with gzip encoding and optimal client-side cache headers. Make your application run faster.',
    'author' => 'Younet Company',
	
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.01',
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
      0 => 'application/modules/Minify',
      1 => 'externals/minify',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/minify.csv',
	  1 => 'application/modules/Core/View/Helper/HeadScript.php',
	  2 => 'application/modules/Core/View/Helper/HeadLink.php',
	  3 => 'application/settings/minify.php'
    ),
  ),
    'items' => array(
        'minify',
        'minify_minifies',
       
    ),
); ?>