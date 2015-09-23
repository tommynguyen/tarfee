<?php return array (
  /*----- Package -----*/
  'package' =>
  array (
    'type' => 'module',
    'name' => 'ynblog',
    'version' => '4.08p4',
    'path' => 'application/modules/Ynblog',
    'title' => 'YN - Advanced Blog',
    'description' => 'The blog plugin helps users share their feeling, emotion and their knowledge to each other.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
      array(
        'type' => 'module',
        'name' => 'blog',
        'minVersion' => '4.1.5',
      ),
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' =>
    array (
      'path' => 'application/modules/Ynblog/settings/install.php',
      'class' => 'Ynblog_Installer',
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
      0 => 'application/modules/Ynblog',
    ),
    'files' =>
    array (
      0 => 'application/languages/en/ynblog.csv',
    ),
  ),
  /*----- Hook -----*/
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Ynblog_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Ynblog_Plugin_Core',
    ),
  ),
  /*----- Item -----*/
  'items' => array(
    'blog',
    'blog_category',
    'blog_feature',
  	'blog_link'
  ),
  /*----- Routes -----*/
  'routes' => array(

    'blog_specific' => array(
      'route' => 'talks/:action/:blog_id/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'blog_id' => '\d+',
        'action' => '(delete|edit)',
      ),
    ),

    'blog_general' => array(
      'route' => 'talks/:action/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'listing',
      ),
      'reqs' => array(
        'action' => '(index|create|manage|listing|style|tag|upload-photo|rss|become|favorite-ajax|un-favorite-ajax|favorite)',
      ),
    ),

    'blog_view' => array(
      'route' => 'talks/:user_id/*',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'list',
      ),
      'reqs' => array(
        'user_id' => '\d+',
      ),
    ),

    'blog_entry_view' => array(
      'route' => 'talks/:user_id/:blog_id/:slug',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'blog_id' => '\d+'
      ),
    ),
    // Public
    'blog_import' => array(
      'route' => 'talks/import',
      'defaults' => array(
        'module' => 'ynblog',
        'controller' => 'import',
        'action' => 'import',
      ),
      'reqs' => array(
        'action' => '(import)',
      ),
    ),

  ),
); ?>