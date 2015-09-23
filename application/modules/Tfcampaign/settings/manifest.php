<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'tfcampaign',
    'version' => '4.01',
    'path' => 'application/modules/Tfcampaign',
    'title' => 'Tarfee Campaign',
    'description' => '',
    'author' => '',
    'callback' => 
    array (
        'path' => 'application/modules/Tfcampaign/settings/install.php',    
        'class' => 'Tfcampaign_Installer',
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
      0 => 'application/modules/Tfcampaign',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/tfcampaign.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
    'items' => array(
  		'tfcampaign_campaign',
  		'tfcampaign_submission',
    ),
 	// Routes ---------------------------------------------------------------------
	'routes' => array(
		'tfcampaign_extended' => array(
			'route' => 'campaigns/:controller/:action/*',
			'defaults' => array(
				'module' => 'tfcampaign',
				'controller' => 'index',
				'action' => 'index',
			),
			'reqs' => array(
				'controller' => '\D+',
				'action' => '\D+',
			)
		),
		'tfcampaign_general' => array(
			'route' => 'campaigns/:action/*',
			'defaults' => array(
				'module' => 'tfcampaign',
				'controller' => 'index',
				'action' => 'browse',
			),
			'reqs' => array(
	            'action' => '(save|remove-save|create|browse)',
	        )
		),
		'tfcampaign_specific' => array(
			'route' => 'campaigns/:action/:campaign_id/*',
	        'defaults' => array(
	            'module' => 'tfcampaign',
	            'controller' => 'campaign',
	            'action' => 'index',
	        ),
	        'reqs' => array(
	            'action' => '(list-withdraw|edit|delete|submit|hide|unhide|withdraw|save-suggest|list-edit|edit-submission)',
	            'campaign_id' => '\d+',
	        )
	    ),
	    'tfcampaign_profile' => array(
			'route' => 'campaigns/:id/:slug/*',
			'defaults' => array(
					'module' => 'tfcampaign',
					'controller' => 'profile',
					'action' => 'index',
					'slug' => '',
			),
			'reqs' => array(
					'id' => '\d+',
			)
	    ),
    ),
); ?>