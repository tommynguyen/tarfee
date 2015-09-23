<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
    0 => 
    array (
      'id' => 17,
      'name' => 'ynevent_main',
      'type' => 'standard',
      'title' => 'Advanced Event Main Navigation Menu',
      'order' => 999,
    ),
    1 => 
    array (
      'id' => 18,
      'name' => 'ynevent_profile',
      'type' => 'standard',
      'title' => 'Advanced Event Profile Options Menu',
      'order' => 999,
    ),
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 170,
      'name' => 'mobi_browse_ynevent',
      'module' => 'ynevent',
      'label' => 'Events',
      'plugin' => '',
      'params' => '{"route":"event_general"}',
      'menu' => 'mobi_browse',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 7,
    ),
    1 => 
    array (
      'id' => 171,
      'name' => 'ynevent_quick_create',
      'module' => 'ynevent',
      'label' => 'Create New Event',
      'plugin' => 'Ynevent_Plugin_Menus::canCreateEvents',
      'params' => '{"route":"event_general","action":"create","class":"buttonlink icon_event_new"}',
      'menu' => 'ynevent_quick',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    2 => 
    array (
      'id' => 172,
      'name' => 'ynevent_profile_style',
      'module' => 'ynevent',
      'label' => 'Edit Styles',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    3 => 
    array (
      'id' => 173,
      'name' => 'ynevent_profile_share',
      'module' => 'ynevent',
      'label' => 'Share',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 5,
    ),
    4 => 
    array (
      'id' => 174,
      'name' => 'ynevent_profile_report',
      'module' => 'ynevent',
      'label' => 'Report Event',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    5 => 
    array (
      'id' => 175,
      'name' => 'ynevent_profile_message',
      'module' => 'ynevent',
      'label' => 'Message Members',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 7,
    ),
    6 => 
    array (
      'id' => 176,
      'name' => 'ynevent_profile_member',
      'module' => 'ynevent',
      'label' => 'Member',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    7 => 
    array (
      'id' => 177,
      'name' => 'ynevent_profile_invite',
      'module' => 'ynevent',
      'label' => 'Invite',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 6,
    ),
    8 => 
    array (
      'id' => 178,
      'name' => 'ynevent_profile_edit',
      'module' => 'ynevent',
      'label' => 'Edit Profile',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    9 => 
    array (
      'id' => 179,
      'name' => 'ynevent_profile_delete',
      'module' => 'ynevent',
      'label' => 'Delete Event',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 8,
    ),
    10 => 
    array (
      'id' => 180,
      'name' => 'ynevent_main_upcoming',
      'module' => 'ynevent',
      'label' => 'Upcoming Events',
      'plugin' => '',
      'params' => '{"route":"event_upcoming"}',
      'menu' => 'ynevent_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    11 => 
    array (
      'id' => 181,
      'name' => 'ynevent_main_past',
      'module' => 'ynevent',
      'label' => 'Past Events',
      'plugin' => '',
      'params' => '{"route":"event_past"}',
      'menu' => 'ynevent_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    12 => 
    array (
      'id' => 182,
      'name' => 'ynevent_main_manage',
      'module' => 'ynevent',
      'label' => 'My Events',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '{"route":"event_general","action":"manage"}',
      'menu' => 'ynevent_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    13 => 
    array (
      'id' => 183,
      'name' => 'ynevent_main_create',
      'module' => 'ynevent',
      'label' => 'Create New Event',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '{"route":"event_general","action":"create"}',
      'menu' => 'ynevent_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
    14 => 
    array (
      'id' => 184,
      'name' => 'ynevent_admin_main_manage',
      'module' => 'ynevent',
      'label' => 'Manage Events',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynevent","controller":"manage"}',
      'menu' => 'ynevent_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 1,
    ),
    15 => 
    array (
      'id' => 185,
      'name' => 'ynevent_admin_main_level',
      'module' => 'ynevent',
      'label' => 'Member Level Settings',
      'plugin' => '',
      'params' => '{"route":"ynevent_admin_default","action":"level"}',
      'menu' => 'ynevent_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
    16 => 
    array (
      'id' => 186,
      'name' => 'ynevent_admin_main_categories',
      'module' => 'ynevent',
      'label' => 'Categories',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynevent","controller":"settings","action":"categories"}',
      'menu' => 'ynevent_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 3,
    ),
    17 => 
    array (
      'id' => 187,
      'name' => 'core_sitemap_ynevent',
      'module' => 'ynevent',
      'label' => 'Events',
      'plugin' => '',
      'params' => '{"route":"event_general"}',
      'menu' => 'core_sitemap',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 6,
    ),
    18 => 
    array (
      'id' => 188,
      'name' => 'core_main_ynevent',
      'module' => 'ynevent',
      'label' => 'Events',
      'plugin' => '',
      'params' => '{"route":"event_upcoming"}',
      'menu' => 'core_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 6,
    ),
    19 => 
    array (
      'id' => 189,
      'name' => 'core_admin_main_plugins_ynevent',
      'module' => 'ynevent',
      'label' => 'Advanced Events',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynevent","controller":"manage"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    20 => 
    array (
      'id' => 190,
      'name' => 'authorization_admin_level_ynevent',
      'module' => 'ynevent',
      'label' => 'Events',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynevent","controller":"level","action":"index"}',
      'menu' => 'authorization_admin_level',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    22 => 
    array (
      'id' => 325,
      'name' => 'ynevent_profile_promote',
      'module' => 'ynevent',
      'label' => 'Promote Event',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 9,
    ),
    23 => 
    array (
      'id' => 345,
      'name' => 'ynevent_profile_invite_group',
      'module' => 'ynevent',
      'label' => 'Invite Groups',
      'plugin' => 'Ynevent_Plugin_Menus',
      'params' => '',
      'menu' => 'ynevent_profile',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 9,
    ),
    24 => 
    array (
      'id' => 346,
      'name' => 'ynevent_admin_main_global',
      'module' => 'ynevent',
      'label' => 'Global Settings',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"ynevent","controller":"global"}',
      'menu' => 'ynevent_admin_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 4,
    ),
  ),
  'mails' => 
  array (
    0 => 
    array (
      'mailtemplate_id' => 43,
      'type' => 'notify_ynevent_accepted',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]',
    ),
    1 => 
    array (
      'mailtemplate_id' => 44,
      'type' => 'notify_ynevent_approve',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]',
    ),
    2 => 
    array (
      'mailtemplate_id' => 45,
      'type' => 'notify_ynevent_discussion_response',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]',
    ),
    3 => 
    array (
      'mailtemplate_id' => 46,
      'type' => 'notify_ynevent_discussion_reply',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]',
    ),
    4 => 
    array (
      'mailtemplate_id' => 47,
      'type' => 'notify_ynevent_invite',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description]',
    ),
    5 => 
    array (
      'mailtemplate_id' => 48,
      'type' => 'notify_ynevent_invite_message',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]',
    ),
    6 => 
    array (
      'mailtemplate_id' => 49,
      'type' => 'notify_ynevent_change_details',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]',
    ),
    7 => 
    array (
      'mailtemplate_id' => 50,
      'type' => 'notify_ynevent_remind',
      'module' => 'ynevent',
      'vars' => '[host],[email],[recipient_title],[recipient_link],[recipient_photo],[sender_title],[sender_link],[sender_photo],[object_title],[object_link],[object_photo],[object_description],[message]',
    ),
  ),
  'jobtypes' => 
  array (
    0 => 
    array (
      'jobtype_id' => 11,
      'title' => 'Rebuild Advanced Event Privacy',
      'type' => 'ynevent_maintenance_rebuild_privacy',
      'module' => 'ynevent',
      'plugin' => 'Ynevent_Plugin_Job_Maintenance_RebuildPrivacy',
      'form' => NULL,
      'enabled' => 1,
      'priority' => 50,
      'multi' => 1,
    ),
  ),
  'notificationtypes' => 
  array (
    0 => 
    array (
      'type' => 'ynevent_accepted',
      'module' => 'ynevent',
      'body' => 'Your request to join the event {item:$subject} has been approved.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    1 => 
    array (
      'type' => 'ynevent_approve',
      'module' => 'ynevent',
      'body' => '{item:$subject} has requested to join the event {item:$object}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    2 => 
    array (
      'type' => 'ynevent_change_details',
      'module' => 'ynevent',
      'body' => 'Event {item:$object} is changed by {item:$subject}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    3 => 
    array (
      'type' => 'ynevent_discussion_reply',
      'module' => 'ynevent',
      'body' => '{item:$subject} has {item:$object:posted} on a {itemParent:$object::event topic} you posted on.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    4 => 
    array (
      'type' => 'ynevent_discussion_response',
      'module' => 'ynevent',
      'body' => '{item:$subject} has {item:$object:posted} on a {itemParent:$object::event topic} you created.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    5 => 
    array (
      'type' => 'ynevent_invite',
      'module' => 'ynevent',
      'body' => '{item:$subject} has invited you to the event {item:$object}.',
      'is_request' => 1,
      'handler' => 'ynevent.widget.request-event',
      'default' => 1,
    ),
    6 => 
    array (
      'type' => 'ynevent_invite_message',
      'module' => 'ynevent',
      'body' => '{item:$subject} has invited you to the event {item:$object}.',
      'is_request' => 1,
      'handler' => '',
      'default' => 1,
    ),
    7 => 
    array (
      'type' => 'ynevent_remind',
      'module' => 'ynevent',
      'body' => 'Reminder: the event {item:$object} will start at {item:$object:$label}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    
    8 => 
    array (
      'type' => 'ynevent_delete',
      'module' => 'ynevent',
      'body' => 'Event {var:$ynevent_title} that you have been joined is deleted by {item:$subject}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    
    9 => 
    array (
      'type' => 'ynevent_edit_delete',
      'module' => 'ynevent',
      'body' => 'Event {var:$ynevent_title} that you have been joined is moved to {item:$object} by {item:$subject}.',
      'is_request' => 0,
      'handler' => '',
      'default' => 1,
    ),
    
  ),
  'actiontypes' => 
  array (
    0 => 
    array (
      'type' => 'ynevent_create',
      'module' => 'ynevent',
      'body' => '{item:$subject} created a new event:',
      'enabled' => 1,
      'displayable' => 5,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
    1 => 
    array (
      'type' => 'ynevent_join',
      'module' => 'ynevent',
      'body' => '{item:$subject} joined the event {item:$object}',
      'enabled' => 1,
      'displayable' => 3,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
    2 => 
    array (
      'type' => 'ynevent_photo_upload',
      'module' => 'ynevent',
      'body' => '{item:$subject} added {var:$count} photo(s).',
      'enabled' => 1,
      'displayable' => 3,
      'attachable' => 2,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
    3 => 
    array (
      'type' => 'ynevent_topic_create',
      'module' => 'ynevent',
      'body' => '{item:$subject} posted a {item:$object:topic} in the event {itemParent:$object:event}: {body:$body}',
      'enabled' => 1,
      'displayable' => 3,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
    4 => 
    array (
      'type' => 'ynevent_topic_reply',
      'module' => 'ynevent',
      'body' => '{item:$subject} replied to a {item:$object:topic} in the event {itemParent:$object:event}: {body:$body}',
      'enabled' => 1,
      'displayable' => 3,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
	5 => 
    array (
      'type' => 'ynevent_video_create',
      'module' => 'ynevent',
      'body' => '{item:$subject} posted a new video:',
      'enabled' => 1,
      'displayable' => 3,
      'attachable' => 1,
      'commentable' => 1,
      'shareable' => 1,
      'is_generated' => 1,
    ),
  ),
  'permissions' => 
  array (
    0 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'auth_comment',
      3 => 5,
      4 => '["owner_network","owner_member_member","owner_member","parent_member","member","owner"]',
    ),
    1 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'auth_photo',
      3 => 5,
      4 => '["member","owner"]',
    ),
    2 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'auth_video',
      3 => 5,
      4 => '["registered","member","owner"]',
    ),
    3 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","parent_member","member"]',
    ),
    4 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    5 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'commentHtml',
      3 => 3,
      4 => 'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    6 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    7 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'delete',
      3 => 2,
      4 => NULL,
    ),
    8 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'edit',
      3 => 2,
      4 => NULL,
    ),
    9 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'invite',
      3 => 1,
      4 => NULL,
    ),
    10 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    11 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'style',
      3 => 1,
      4 => NULL,
    ),
    12 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'video',
      3 => 1,
      4 => NULL,
    ),
    13 => 
    array (
      0 => 'admin',
      1 => 'event',
      2 => 'view',
      3 => 2,
      4 => NULL,
    ),
    14 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'auth_comment',
      3 => 5,
      4 => '["owner_network","owner_member_member","owner_member","parent_member","member","owner"]',
    ),
    15 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'auth_photo',
      3 => 5,
      4 => '["member","owner"]',
    ),
    16 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'auth_video',
      3 => 5,
      4 => '["registered","member","owner"]',
    ),
    17 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","parent_member","member"]',
    ),
    18 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'comment',
      3 => 2,
      4 => NULL,
    ),
    19 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'commentHtml',
      3 => 3,
      4 => 'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    20 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    21 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'delete',
      3 => 2,
      4 => NULL,
    ),
    22 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'edit',
      3 => 2,
      4 => NULL,
    ),
    23 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'invite',
      3 => 1,
      4 => NULL,
    ),
    24 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    25 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'style',
      3 => 1,
      4 => NULL,
    ),
    26 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'video',
      3 => 1,
      4 => NULL,
    ),
    27 => 
    array (
      0 => 'moderator',
      1 => 'event',
      2 => 'view',
      3 => 2,
      4 => NULL,
    ),
    28 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'auth_comment',
      3 => 5,
      4 => '["owner_network","owner_member_member","owner_member","parent_member","member","owner"]',
    ),
    29 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'auth_photo',
      3 => 5,
      4 => '["member","owner"]',
    ),
    30 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'auth_video',
      3 => 5,
      4 => '["registered","member"]',
    ),
    31 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'auth_view',
      3 => 5,
      4 => '["everyone","owner_network","owner_member_member","owner_member","parent_member","member"]',
    ),
    32 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'comment',
      3 => 1,
      4 => NULL,
    ),
    33 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'commentHtml',
      3 => 3,
      4 => 'blockquote, strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr',
    ),
    34 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'create',
      3 => 1,
      4 => NULL,
    ),
    35 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'delete',
      3 => 1,
      4 => NULL,
    ),
    36 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'edit',
      3 => 1,
      4 => NULL,
    ),
    37 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'invite',
      3 => 1,
      4 => NULL,
    ),
    38 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'photo',
      3 => 1,
      4 => NULL,
    ),
    39 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'style',
      3 => 1,
      4 => NULL,
    ),
    40 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'video',
      3 => 1,
      4 => NULL,
    ),
    41 => 
    array (
      0 => 'user',
      1 => 'event',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
    42 => 
    array (
      0 => 'public',
      1 => 'event',
      2 => 'view',
      3 => 1,
      4 => NULL,
    ),
  ),
  'pages' => 
  array (
    'ynevent_index_browse' => 
    array (
      'page_id' => 24,
      'name' => 'ynevent_index_browse',
      'displayname' => 'Advanced Event Browse Page',
      'url' => NULL,
      'title' => 'Advanced Event Browse',
      'description' => 'This page lists advanced events.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => NULL,
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 659,
          'page_id' => 24,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 660,
              'page_id' => 24,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 659,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 661,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.browse-menu',
                  'parent_content_id' => 660,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 662,
          'page_id' => 24,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 663,
              'page_id' => 24,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 662,
              'order' => 4,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 664,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.list-popular-events',
                  'parent_content_id' => 663,
                  'order' => 6,
                  'params' => '{"title":"Popular Events","itemCountPerPage":"","popularType":"view","nomobile":"0","name":"ynevent.list-popular-events"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 665,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.list-most-attending-events',
                  'parent_content_id' => 663,
                  'order' => 7,
                  'params' => '{"title":"Most Attending","popularType":"view","nomobile":"0","itemCountPerPage":"","name":"ynevent.list-most-attending-events"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 666,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.list-most-liked-events',
                  'parent_content_id' => 663,
                  'order' => 8,
                  'params' => '{"title":"Most Liked"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 667,
              'page_id' => 24,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 662,
              'order' => 5,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 668,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.browse-search',
                  'parent_content_id' => 667,
                  'order' => 13,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 669,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.browse-menu-quick',
                  'parent_content_id' => 667,
                  'order' => 14,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 670,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.events-calendar',
                  'parent_content_id' => 667,
                  'order' => 15,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                3 => 
                array (
                  'content_id' => 671,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.list-most-rated-events',
                  'parent_content_id' => 667,
                  'order' => 16,
                  'params' => '{"title":"Most Rated"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            2 => 
            array (
              'content_id' => 672,
              'page_id' => 24,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 662,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 1139,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'ynevent.feature-events',
                  'parent_content_id' => 672,
                  'order' => 10,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 673,
                  'page_id' => 24,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 672,
                  'order' => 11,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
    'ynevent_profile_index' => 
    array (
      'page_id' => 25,
      'name' => 'ynevent_profile_index',
      'displayname' => 'Advanced Event Profile Page',
      'url' => NULL,
      'title' => 'Advanced Event Profile',
      'description' => 'This is the profile for an advanced event.',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => '[1,2,3,4,5]',
      'provides' => 'subject=event',
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 674,
          'page_id' => 25,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '["[]"]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 675,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 674,
              'order' => 6,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 676,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.browse-menu',
                  'parent_content_id' => 675,
                  'order' => 3,
                  'params' => '["[]"]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 677,
          'page_id' => 25,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[""]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 678,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'left',
              'parent_content_id' => 677,
              'order' => 4,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 679,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-photo',
                  'parent_content_id' => 678,
                  'order' => 6,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 680,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-options',
                  'parent_content_id' => 678,
                  'order' => 7,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'content_id' => 681,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-info',
                  'parent_content_id' => 678,
                  'order' => 8,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                3 => 
                array (
                  'content_id' => 682,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-rsvp',
                  'parent_content_id' => 678,
                  'order' => 9,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                4 => 
                array (
                  'content_id' => 683,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-add-rates',
                  'parent_content_id' => 678,
                  'order' => 10,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                5 => 
                array (
                  'content_id' => 684,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-addthis',
                  'parent_content_id' => 678,
                  'order' => 11,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                6 => 
                array (
                  'content_id' => 685,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-follow',
                  'parent_content_id' => 678,
                  'order' => 12,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            1 => 
            array (
              'content_id' => 1045,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'right',
              'parent_content_id' => 677,
              'order' => 5,
              'params' => '["[]"]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 1138,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-related',
                  'parent_content_id' => 1045,
                  'order' => 25,
                  'params' => '{"title":"Related Events"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'content_id' => 1046,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-near-location',
                  'parent_content_id' => 1045,
                  'order' => 26,
                  'params' => '{"title":"Nearest Events","radius":"500","max":"5","nomobile":"0","itemCountPerPage":"","name":"ynevent.profile-near-location"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
            2 => 
            array (
              'content_id' => 685,
              'page_id' => 25,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 677,
              'order' => 6,
              'params' => '[""]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 686,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'ynevent.profile-status',
                  'parent_content_id' => 685,
                  'order' => 13,
                  'params' => '[""]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
                1 => 
                    array (
                      'content_id' => 1150,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-slideshow-photos',
                      'parent_content_id' => 685,
                      'order' => 14,
                      'params' => '{"title":"Slideshow Photos"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                2 => 
                array (
                  'content_id' => 687,
                  'page_id' => 25,
                  'type' => 'widget',
                  'name' => 'core.container-tabs',
                  'parent_content_id' => 685,
                  'order' => 14,
                  'params' => '{"max":"3","title":"","nomobile":"0","name":"core.container-tabs"}',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                    0 => 
                    array (
                      'content_id' => 945,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-map',
                      'parent_content_id' => 687,
                      'order' => 16,
                      'params' => '{"title":"Location","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    1 => 
                    array (
                      'content_id' => 688,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'activity.feed',
                      'parent_content_id' => 687,
                      'order' => 15,
                      'params' => '{"title":"Updates"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    2 => 
                    array (
                      'content_id' => 1137,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-calendar',
                      'parent_content_id' => 687,
                      'order' => 17,
                      'params' => '{"title":"Calendar"}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    3 => 
                    array (
                      'content_id' => 689,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-members',
                      'parent_content_id' => 687,
                      'order' => 18,
                      'params' => '{"title":"Guests","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    4 => 
                    array (
                      'content_id' => 690,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-photos',
                      'parent_content_id' => 687,
                      'order' => 19,
                      'params' => '{"title":"Photos","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    5 => 
                    array (
                      'content_id' => 1044,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-videos',
                      'parent_content_id' => 687,
                      'order' => 20,
                      'params' => '{"title":"Videos","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    6 => 
                    array (
                      'content_id' => 692,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'core.profile-links',
                      'parent_content_id' => 687,
                      'order' => 21,
                      'params' => '{"title":"Links","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    7 => 
                    array (
                      'content_id' => 946,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-sponsors',
                      'parent_content_id' => 687,
                      'order' => 22,
                      'params' => '{"title":"Sponsors","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                    8 => 
                    array (
                      'content_id' => 691,
                      'page_id' => 25,
                      'type' => 'widget',
                      'name' => 'ynevent.profile-discussions',
                      'parent_content_id' => 687,
                      'order' => 23,
                      'params' => '{"title":"Discussions","titleCount":true}',
                      'attribs' => NULL,
                      'ynchildren' => 
                      array (
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);?>