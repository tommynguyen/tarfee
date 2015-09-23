<?php return array (
		// Packages---------------------------------------------------------------------
		'package' =>
		array (
				'type' => 'module',
				'name' => 'advgroup',
				'version' => '4.08p4',
				'path' => 'application/modules/Advgroup',
				'title' => 'YN - Advanced Groups',
				'description' => 'Advanced Groups allow member to create groups, post photos,albums, polls or discussion, etc .. on their groups.',
				'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
				'dependencies' => array(
						array(
								'type' => 'module',
								'name' => 'core',
								'minVersion' => '4.1.2',
						),
						array(
								'type' => 'module',
								'name' => 'group',
								'minVersion' => '4.1.5',
						),
						array(
								'type' => 'module',
								'name' => 'younet-core',
								'minVersion' => '4.02',
						),
				),
				'callback' => array (
						'path' => 'application/modules/Advgroup/settings/install.php',
						'class' => 'Advgroup_Installer',
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
						0 => 'application/modules/Advgroup',
				),
				'files' =>
				array (
						0 => 'application/languages/en/yngroup.csv',
						1 => 'application/modules/Messages/controllers/MessagesController.php'
				),
		),
		// Hooks ---------------------------------------------------------------------
		'hooks' => array(
				array(
						'event' => 'onStatistics',
						'resource' => 'Advgroup_Plugin_Core'
				),
				array(
						'event' => 'onUserDeleteBefore',
						'resource' => 'Advgroup_Plugin_Core',
				),
				array(
						'event' => 'getActivity',
						'resource' => 'Advgroup_Plugin_Core',
				),
				array(
						'event' => 'addActivity',
						'resource' => 'Advgroup_Plugin_Core',
				),
				array(
						'event' => 'onUserCreateAfter',
						'resource' => 'Advgroup_Plugin_Signup',
				),
			    array (
					    'event' => 'onItemCreateAfter',
					    'resource' => 'Advgroup_Plugin_Core',
			    ),
			    array (
					    'event' => 'onItemUpdateAfter',
					    'resource' => 'Advgroup_Plugin_Core',
			    ),	
			    array (
					    'event' => 'onItemDeleteAfter',
					    'resource' => 'Advgroup_Plugin_Core',
			    ),		    
				
				 
		),
		// Items ---------------------------------------------------------------------
		'items' => array(
				'advgroup_announcement',
				'group',
				'advgroup',
				'advgroup_album',
				'advgroup_highlight',
				'advgroup_mp3music',
				'advgroup_music',
				'advgroup_blacklist',
				'advgroup_mapping',
				'advgroup_sponsor',
				'advgroup_category',
				'advgroup_list',
				'advgroup_list_item',
				'advgroup_photo',
				'advgroup_post',
				'advgroup_topic',
				'advgroup_link',
				'advgroup_poll',
				'advgroup_video',
				'advgroup_report',
				'advgroup_event',
				'advgroup_mapping',
				'advgroup_request',
		),
		// Routes --------------------------------------------------------------------
		'routes' => array(
				'group_mp3music_create_album' => array(
				      'route' => 'mp3-music/album/create/*',
				      'defaults' => array(
				        'module' => 'mp3music',
				        'controller' => 'album',
				        'action' => 'create',
				      ),
				),
				
				'group_extended' => array(
						'route' => 'clubs/:controller/:action/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'index',
								'action' => 'index',
						),
						'reqs' => array(
								'controller' => '\D+',
								'action' => '\D+',
						)
				),
				'group_viewsubfolder' => array(
							
						'route' => 'clubs/:controller/:action/slug/:slug/folder_id/:folder_id/parent_type/:parent_type/parent_id/:parent_id/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'file',
								'action' => 'view-folder',				
						),
						
				),
				'group_general' => array(
						'route' => 'clubs/:action/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'index',
								'action' => 'browse',
						),
						'reqs' => array(
								'action' => '(browse|create|listing|manage|post|get-my-location|display-map-view|follow)',
						)
				),
				'group_specific' => array(
						'route' => 'clubs/:action/:group_id/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'group',
								'action' => 'index',
						),
						'reqs' => array(
								'action' => '(request-verify|verify-request|verify|unverify|edit|delete|join|leave|cancel|accept|invite|style|transfer|email-to-followers|crop-photo)',
								'group_id' => '\d+',
						)
				),
				'group_post' => array(
						'route' => 'clubs/post/control/:action/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'post',
								'action' => 'edit',
						),
						'reqs' => array(
								'action' => '(edit|delete|report)',
						)
				),
				'group_profile' => array(
						'route' => 'club/:id/:slug/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'profile',
								'action' => 'index',
								'slug' => '',
						),
						'reqs' => array(
								'id' => '\d+',
						)
				),
				'group_browse' => array(
						'route' => 'group/browse',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'index',
								'action' => 'browse'
						)
				),
				'group_link' =>array(
						'route' => 'clubs/link/:action/*',
						'defaults' =>array(
								'module' => 'advgroup',
								'controller' => 'link',
								'action' => 'create',
						),
						'reqs' => array(
								'action' => '(create|manage|edit|delete)',
						)
				),
				'group_report' => array(
						'route' => 'club/report/:group_id/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'report',
								'action' => 'add')
				),
				'reqs' => array(
						'group_id' => '\d+',
				),
				'group_activity' => array(
						'route' => 'club/:action/*',
						'defaults' => array(
								'module' => 'advgroup',
								'controller' => 'activity',
								'action' => 'activity',
						),
						'reqs' => array(
								'action' => '(activity|viewmore)',
						)
				),
		)
); ?>