<?php
return array(
  array(
    'title' => 'Advanced Activity Feed',
    'description' => 'Displays the advanced activity feed.',
    'category' => 'Advanced Feed',
    'type' => 'widget',
    'name' => 'ynfeed.feed',
    'defaultParams' => array(
      'title' => 'What\'s New',
    ),
  'adminForm' => array(
      'elements' => array(
    ),
    )
  ),
  
  array(
    'title' => 'Group Videos Feed',
    'description' => 'Displays the activity feed of group\'s videos.',
    'category' => 'Advanced Feed',
    'type' => 'widget',
    'name' => 'ynfeed.group-videos-feed',
    'defaultParams' => array(
      'title' => '',
    )
  ),
  
  array(
    'title' => 'Welcome Tab',
    'description' => 'Displays the welcome tab on home page.',
    'category' => 'Advanced Feed',
    'type' => 'widget',
    'name' => 'ynfeed.welcome-tab',
    'defaultParams' => array(
      'title' => 'Welcome',
    ),
  'adminForm' => array(
      'elements' => array(
    ),
    )
  )
) ?>