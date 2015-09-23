<?php
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'contactimporter',
    'title' => 'YN - Contact Importer',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'version' => '4.05p3',
    'revision' => '$Revision: 7381 $',
    'path' => 'application/modules/Contactimporter',
    'repository' => 'socialengine.younetco.com',
    'meta' => array(
      'title' => 'Contact Importer',
      'description' => 'Contact Importer',
      'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
      'changeLog' => array(
        
      ),
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.0.4',
      ),
      array(
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p3',
      ),
      array(
        'type' => 'module',
        'name' => 'socialbridge',
        'minVersion' => '4.04p3',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Contactimporter/settings/install.php',
      'class' => 'Contactimporter_Installer',
    ),
    'directories' => array(
      'application/modules/Contactimporter',
    ),
    'files' => array(
      'application/languages/en/contactimporter.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
		array(
	      'event' => 'onUserSignupAfter',
	      'resource' => 'Contactimporter_Plugin_Core',
	    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'contactimporter_provider',
	'contactimporter_apisettings',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
  		'contactimporter_general' => array(
  				'route' => 'contactimporter/:action/*',
  				'defaults' => array(
  						'module' => 'contactimporter',
  						'controller' => 'index',
  						'action' => 'index',
  				),
  				'reqs' => array(
  						'action' => '(index|import|invite|invitedelete|inviteresend|add|queue-message|queue-email|pending-invitation|queuedelete|invitationdelete|invitationsend|fb-save-invitations|fb-invite-successfull)',
  				)
  		),
  		'contactimporter_ref' => array(
  				'route' => 'contactimporter/ref/:user_id',
  				'defaults' => array(
  						'module' => 'contactimporter',
  						'controller' => 'index',
  						'action' => 'ref',
  						'user_id' => 0
  				)
  		),
		'contactimporter_pending' => array(      
          'route' => 'contactimporter/pending/page/:page',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'pending',
            'page' => 1,
          ),
        ),
      'contactimporter' => array(
          'type' => 'Zend_Controller_Router_Route_Static',
          'route' => 'contactimporter/import/',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'import'
          ),
        ),
      'contactimporter_invite' => array(    
        'type' => 'Zend_Controller_Router_Route_Static',      
          'route' => 'contactimporter/invite',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'invite'
          )
        ),
       'contactimporter_fbcanvas' => array(    
        'type' => 'Zend_Controller_Router_Route_Static',      
          'route' => 'contactimporter/fbcanvas',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'fb-canvas'
          )
        ),
        'contactimporter_add' => array(    
        'type' => 'Zend_Controller_Router_Route_Static',      
          'route' => 'contactimporter/add',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'add'
          )
        ), 
        'contactimporter_upload' => array(    
        'type' => 'Zend_Controller_Router_Route_Static',      
          'route' => 'contactimporter/upload',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'upload'
          )
        ), 
		

      'contactimporter_admin_manage_level' => array(
          'route' => 'admin/contactimporter/level/:level_id',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'admin-level',
            'action' => 'index',
            'level_id' => 1
          )
        ),   
      'contactimporter_admin_main_providers' => array(
          'route' => 'admin/contactimporter/manage',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'admin-manage',
            'action' => 'index',
            
          )
        ),
        'contactimporter_popup' => array(      
          'route' => 'contactimporter/popup/*',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'popup'
          )
        ),     
        'login_openid' => array(      
          'route' => 'contactimporter/login/*',
          'defaults' => array(
            'module' => 'contactimporter',
            'controller' => 'index',
            'action' => 'login-openid'
          )
        ),     
  )
) ?>