<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9868 2013-02-12 21:50:45Z shaun $
 * @author     John
 */
return array(
  
  array(
    'title' => 'Preferred Clubs',
    'description' => 'Displays a list of preferred clubs.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-preferred-clubs-search',
    'requirements' => array(
      'viewer',
    ),
  ),
  array(
    'title' => 'Preferred Sports',
    'description' => 'Displays a list of preferred sports.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-preferred-sports-search',
    'requirements' => array(
      'viewer',
    ),
    'defaultParams' => array(
      'title' => 'User Sports',
    ),
  ),
  array(
    'title' => 'Quick Links',
    'description' => 'Displays a list of quick links.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.home-links',
    'requirements' => array(
      'viewer',
    ),
  ),
  array(
    'title' => 'User Photo',
    'description' => 'Displays the logged-in member\'s photo.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.home-photo',
    'requirements' => array(
      'viewer',
    ),
  ),
  array(
    'title' => 'Online Users',
    'description' => 'Displays a list of online members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.list-online',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => '%d Members Online',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Popular Members',
    'description' => 'Displays the list of most popular members.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.list-popular',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Members',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Recent Signups',
    'description' => 'Displays the list of most recent signups.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.list-signups',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Signups',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Login or Signup',
    'description' => 'Displays a login form and a signup link for members that are not logged in.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.login-or-signup',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Profile My Library',
    'description' => 'Displays a member\'s library on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-library',
    'defaultParams' => array(
      'title' => 'My Library',
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Fields',
    'description' => 'Displays a member\'s profile field data on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-fields',
    'defaultParams' => array(
      'title' => 'Info',
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Friends',
    'description' => 'Displays a member\'s friends on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-friends',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Friends',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Followers',
    'description' => 'Displays a member\'s followers on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-friends-followers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Followers',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Following',
    'description' => 'Displays the members a member is following on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-friends-following',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Following',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Mutual Friends',
    'description' => 'Displays the mutual friends between the viewer and the subject.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-friends-common',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Mutual Friends'
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Info',
    'description' => 'Displays a member\'s info (signup date, friend count, etc) on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-info',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Options',
    'description' => 'Displays a list of actions that can be performed on a member on their profile (report, add as friend, etc).',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-options',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Photo',
    'description' => 'Displays a member\'s photo on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-photo',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Cover',
    'description' => 'Displays a member\'s cover on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-cover',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Status',
    'description' => 'Displays a member\'s name and most recent status on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-status',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Profile Tags',
    'description' => 'Displays photos, blogs, etc that a member has been tagged in.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-tags',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Tags',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'User Settings Menu',
    'description' => 'Displays a menu in the user settings pages.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.settings-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Member Browse Menu',
    'description' => 'Displays a menu in the member browse page.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Member Browse Search',
    'description' => 'Displays a search form in the member browse page.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  
   array(
    'title' => 'Profile Players',
    'description' => 'Displays my players on their profile.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.profile-players',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Players',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  
  	array(
    	'title' => 'Club Profile Players',
    	'description' => 'Displays club players on club profile.',
    	'category' => 'User',
    	'type' => 'widget',
    	'name' => 'user.club-profile-players',
    	'isPaginated' => true,
    	'defaultParams' => array(
      		'title' => 'Players',
    	),
    	'requirements' => array(
      		'subject' => 'group',
    	),
  	),
  
	//HOANGND widget for render profile sections
  	array(
        'title' => 'Profile Sections',
        'description' => 'Displays Sections on User Profile page',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'user.profile-sections',
        'requirements' => array(
          'subject' => 'user',
        ),
    ),
    
	array(
        'title' => 'Profile Recommendation',
        'description' => 'Displays Recommendation on User Profile page',
        'category' => 'User',
        'type' => 'widget',
        'name' => 'user.profile-recommendation',
        'requirements' => array(
          'subject' => 'user',
        ),
    ),
) ?>