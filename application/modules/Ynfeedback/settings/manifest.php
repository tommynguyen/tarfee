<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynfeedback',
    'version' => '4.01',
    'path' => 'application/modules/Ynfeedback',
    'title' => 'YN - Feedback',
    'description' => '',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => 
    array (
        'path' => 'application/modules/Ynfeedback/settings/install.php',    
        'class' => 'Ynfeedback_Installer',
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
      0 => 'application/modules/Ynfeedback',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynfeedback.csv',
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
  
  // Items ---------------------------------------------------------------------
    'items' => array(
        'ynfeedback_idea',
        'ynfeedback_status',
        'ynfeedback_file',
        'ynfeedback_screenshot',
        'ynfeedback_category',
        'ynfeedback_poll',
  		'ynfeedback_vote',
    ),
    
  // Routes ---------------------------------------------------------------------
  'routes' => array(
    
		'ynfeedback_extended' => array(
			'route' => 'feedback/:controller/:action/*',
			'defaults' => array(
				'module' => 'ynfeedback',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		
	  	 'ynfeedback_general' => array(
			'route' => 'feedback/:action/*',
			'defaults' => array(
				'module' => 'ynfeedback',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
	            'action' => '(index|simple-helpful|create|author-suggest|vote|show-result|manage-follow|helpful|detail-popup|create-popup|manage|listing|vote-feedback|unvote-feedback|suggest-feedback)',
	        )
		),
	  	
		'ynfeedback_specific' => array(
				'route' => 'feedback/:action/:idea_id/*',
				'defaults' => array(
						'module' => 'ynfeedback',
						'controller' => 'idea',
						'action' => 'index',
				),
				'reqs' => array(				
						'action' => '(remove-files|add-file|manage-files|remove-screenshots|add-screenshot|manage-screenshots|edit|delete|view|profile-follow|un-follow|delete)',
						'idea_id' => '\d+',
				)
		),
		
	),
	// Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onRenderLayoutDefault',
            'resource' => 'Ynfeedback_Plugin_Core',
        ),
        array(
            'event' => 'onUserCreateAfter',
            'resource' => 'Ynfeedback_Plugin_Core',
        ),
        array(
            'event' => 'onUserLoginAfter',
            'resource' => 'Ynfeedback_Plugin_Core',
        ),
        array(
	        'event' => 'onStatistics',
	        'resource' => 'Ynfeedback_Plugin_Core',
	    ),
    ),
); ?>