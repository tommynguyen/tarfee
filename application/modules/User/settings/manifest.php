<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'user',
    'version' => '4.8.7',
    'revision' => '$Revision: 10271 $',
    'path' => 'application/modules/User',
    'repository' => 'socialengine.com',
    'title' => 'Members',
    'description' => 'Members',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.2.0',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/User/settings/install.php',
      'class' => 'User_Installer',
      'priority' => 3000,
    ),
    'directories' => array(
      'application/modules/User',
    ),
    'files' => array(
      'application/languages/en/user.csv',
    ),
  ),
  // Compose -------------------------------------------------------------------
  'compose' => array(
    array('_composeFacebook.tpl', 'user'),
    array('_composeTwitter.tpl', 'user'),
  ),
  'composer' => array(
    'facebook' => array(
      'script' => array('_composeFacebook.tpl', 'user'),
    ),
    'twitter' => array(
      'script' => array('_composeTwitter.tpl', 'user'),
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserEnable',
      'resource' => 'User_Plugin_Core',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'User_Plugin_Core',
    ),
    array(
      'event' => 'onUserCreateAfter',
      'resource' => 'User_Plugin_Core',
    ),
    array(
      'event' => 'getAdminNotifications',
      'resource' => 'User_Plugin_Core',
    ),
    array(
      'event' => 'onItemCreateAfter',
      'resource' => 'User_Plugin_Core',
    ),
    array(
      'event' => 'onItemUpdateAfter',
      'resource' => 'User_Plugin_Core',
    ),
     array(
      'event' => 'onItemDeleteAfter',
      'resource' => 'User_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'user',
    'user_list',
    'user_list_item',
    'user_offerservice',
    'user_service',
    'user_archievement',
    'user_license',
    'user_experience',
    'user_education',
    'user_recommendation',
    'user_location',
    'user_library',
    'user_sportcategory',
    'user_playercard',
    'user_photo',
    'user_inviterequest',
     'user_membershiprequest'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // User - General
    'user_extended' => array(
      'route' => 'members/:controller/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'user_general' => array(
      'route' => 'members/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'index',
        'action' => 'browse'
      ),
      'reqs' => array(
        'action' => '(check-code|confirm-trial|using-trial|home|browse|render-section|get-my-location|upload-photo|sublocations|get-continent|suggest-group|save-preferred-clubs|get-view-preferred-clubs|suggest-user|save-basic|get-countries|suggest-user-block|block-users|in-mail|transfer-item|view-basic|view-eyeons|signon-zendesk)',
      )
    ),
	
	'user_recommendation' => array(
      'route' => 'members/recommendation/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'recommendation',
        'action' => 'received'
      ),
    ),
	'user_library' => array(
      'route' => 'members/library/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'library',
      ),
      'reqs' => array(
        'action' => '(create-sub-library|edit|delete|move-to-sub|move-to-main|move-to-player)',
      )
    ),
	'user_photo' => array(
      'route' => 'members/photo/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'photo',
        'action' => 'upload'
      ),
      'reqs' => array(
        'action' => '(upload|upload-photo)',
      )
    ),
	
    // User - Specific
    'user_profile' => array(
      'route' => 'profile/:id/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'profile',
        'action' => 'index'
      )
    ),
    'user_home' => array(
      'route' => '/home/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'auth',
        'action' => 'home'
      )
    ),
    'user_request' => array(
      'route' => '/request-invite/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'signup',
        'action' => 'request-invite'
      )
    ),
    'user_register' => array(
      'route' => '/register/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'signup',
        'action' => 'register'
      )
    ),
    'user_login' => array(
      'route' => '/login/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'auth',
        'action' => 'login'
      )
    ),
    'user_logout' => array(
      'type' => 'Zend_Controller_Router_Route_Static',
      'route' => '/logout',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'auth',
        'action' => 'logout'
      )
    ),
    'user_signup' => array(
      'route' => '/signup/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'signup',
        'action' => 'index'
      )
    ),
    
    'user_signup1' => array(
      'route' => '/signup/account/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'signup',
        'action' => 'account'
      )
    ),
    
	'user_sport' => array(
      'route' => '/user/sport/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'sport',
        'action' => 'manage'
      )
    ),
    
    'user_playercard' => array(
      'route' => '/player/:action/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'player-card',
        'action' => 'create'
      )
    ),
    'playercard_profile' => array(
      'route' => 'player/:id/*',
      'defaults' => array(
        'module' => 'user',
        'controller' => 'player-card',
        'action' => 'view'
      ),
      'reqs' => array(
					'id' => '\d+',
			)
    ),
  )
); ?>
