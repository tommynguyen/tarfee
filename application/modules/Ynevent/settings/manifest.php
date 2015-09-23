<?php defined("_ENGINE") or die("access denied"); return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynevent',
    'version' => '4.05p5',
    'path' => 'application/modules/Ynevent',
    'title' => 'YN - Advanced Event',
    'description' => 'Advanced Event',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Ynevent/settings/install.php',
      'class' => 'Ynevent_Installer',
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
      0 => 'application/modules/Ynevent',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynevent.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p7',
      ),
      1 => 
      array (
        'type' => 'module',
        'name' => 'event',
        'minVersion' => '4.1.8',
      ),
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onStatistics',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    1 => 
    array (
      'event' => 'onUserDeleteBefore',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    2 => 
    array (
      'event' => 'getActivity',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    3 => 
    array (
      'event' => 'addActivity',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    4 => 
    array (
      'event' => 'onEventUpdateAfter',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    5 => 
    array (
      'event' => 'onActivityActionCreateAfter',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    6 => 
    array (
      'event' => 'onBeforeActivityNotificationsUpdate',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    7 => 
    array (
      'event' => 'onItemCreateAfter',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    8 => 
    array (
      'event' => 'onItemUpdateAfter',
      'resource' => 'Ynevent_Plugin_Core',
    ),
    9 => 
    array (
      'event' => 'onItemDeleteAfter',
      'resource' => 'Ynevent_Plugin_Core',
    ),
  ),
  'items' => 
  array (
    0 => 'event',
    1 => 'ynevent_event',
    2 => 'ynevent_album',
    3 => 'ynevent_category',
    4 => 'ynevent_photo',
    5 => 'ynevent_post',
    6 => 'ynevent_topic',
    7 => 'ynevent_topicwatches',
    8 => 'ynevent_membership',
    9 => 'ynevent_ratings',
    10 => 'ynevent_follow',
    11 => 'ynevent_remind',
    12 => 'event_album',
    13 => 'event_category',
    14 => 'event_photo',
    15 => 'event_post',
    16 => 'event_topic',
    17 => 'event_sponsor',
    18 => 'event_agent',
  	19 => 'ynevent_announcement',
  	20 => 'ynevent_review',
  	21 => 'ynevent_reviewreport',
  	22 => 'ynevent_mapping',
  ),
  'routes' => 
  array (
    'ynevent_default' => 
    array (
      'route' => 'event/:controller/:action/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
      ),
    ),
    'event_extended' => 
    array (
      'route' => 'events/:controller/:action/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'controller' => '\\D+',
        'action' => '\\D+',
      ),
    ),
    'event_general' => 
    array (
      'route' => 'events/:action/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'listing',
      ),
      'reqs' => 
      array (
        'action' => '(listing|index|browse|create|delete|list|manage|edit|calendar|add-location|event-badge|promote-calendar|calendar-badge|view-more|display-map-view|get-my-location|display-map-view-time)',
      ),
    ),
    'event_specific' => 
    array (
      'route' => 'events/:action/:event_id/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'event',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(edit|delete|join|leave|invite|accept|style|reject|promote|transfer|direction)',
        'event_id' => '\\d+',
      ),
    ),
    'event_profile' => 
    array (
      'route' => 'event/:id/:slug/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'profile',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'id' => '\\d+',
      ),
    ),
    'event_member' => 
    array (
      'route' => 'event/member/:action/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'member',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(invite-groups|ajax-groups)',
      ),
    ),
    'event_upcoming' => 
    array (
      'route' => 'events/upcoming/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'browse',
        'filter' => 'future',
      ),
    ),
    'event_new' => 
    array (
      'route' => 'events/new/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'new',
        'filter' => 'recent',
      ),
    ),
    
    'event_past' => 
    array (
      'route' => 'events/past/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'past',
        'filter' => 'past',
      ),
    ),
    'event_following' => 
    array (
      'route' => 'events/following/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'index',
        'action' => 'following',
      ),
    ),
    'ynevent_admin_default' => 
    array (
      'route' => 'admin/event/settings/:action/*',
      'defaults' => 
      array (
        'module' => 'ynevent',
        'controller' => 'admin-settings',
      ),
    ),
    // blog
    'ynevent_blog' => array(
        'route' => 'events/blog/:action/:event_id/*',
        'defaults' => array(
            'module' => 'ynevent',
            'controller' => 'blog',
            'action' => 'index',
        ),
        'reqs' => array(
            'action' => '(import-blogs|delete|remove)',
        ),
    ),
  ),
);?>