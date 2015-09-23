<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'yncomment',
    'version' => '4.01p1',
    'path' => 'application/modules/Yncomment',
    'title' => 'YN - Advanced Comment',
    'description' => 'Advanced Comment Plugin - Nested Comments, Replies, Voting & Attachments',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => array(
           'path' => 'application/modules/Yncomment/settings/install.php',
           'class' => 'Yncomment_Installer',
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
      0 => 'application/modules/Yncomment',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/yncomment.csv',
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
  // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Yncomment_Plugin_Core',
        ),
        array(
            'event' => 'onRenderLayoutMobileDefault',
            'resource' => 'Yncomment_Plugin_Core',
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'yncomment_dislike',
        'yncomment_modules'
    ),
); ?>