<?php
// YouNet Responsive Event
return array(
  array(
    'title' => 'Mini Menu',
    'description' => 'Shows the site-wide mini menu. You can edit its contents in your menu editor.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-mini-menu',
    'adminForm' => 'Core_Form_Admin_Widget_Logo',
    'requirements' => array( 'header-footer'
    ),
  ),
  array(
    'title' => 'Search Events',
    'description' => 'Shows search events.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-search-events',
    'requirements' => array(),
  ),
  array(
    'title' => 'Slide Events',
    'description' => 'Shows slide events. You can edit events in YouNet Responsive Plugin.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-slide-events',
    'isPaginated' => true,
    'requirements' => array(),
    
  ),
   array(
    'title' => 'Hot Events',
    'description' => 'Shows hot events.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-hot-events',
    'requirements' => array(),
    'defaultParams' => array(
      'title' => 'Hot Events',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Number of events',
            'default' => 4,
            'multiOptions' => array(
              4 => 4,
              8 => 8,
            )
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Popular Events',
    'description' => 'Shows popular events.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-popular-events',
    'requirements' => array(),
    'defaultParams' => array(
      'title' => 'Popular Events',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Number of events',
            'default' => 6,
            'multiOptions' => array(
              6 => 6,
              12 => 12,
            )
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Top Sponsors',
    'description' => 'Shows top sponsors events. You can edit event sponsors in YouNet Responsive Plugin.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-top-sponsors',
    'requirements' => array(),
    'isPaginated' => true,
  ),
  array(
    'title' => 'Footer About',
    'description' => 'Shows footer about.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-footer-about',
    'requirements' => array(),
    'autoEdit' => true,
    'adminForm' => 'Core_Form_Admin_Younetadvancedhtmlblock',
  ),
  array(
    'title' => 'Footer Menu',
    'description' => 'Shows footer menu.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-footer-menu',
    'requirements' => array(),
  ),
  array(
    'title' => 'Categories',
    'description' => 'Shows event catetoies.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-categories',
    'requirements' => array(),
    'defaultParams' => array(
    ),
  ),
  array(
    'title' => 'Personalize',
    'description' => 'Shows me event options.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-personalize',
    'requirements' => array(),
    'defaultParams' => array(
    ),
  ),
  array(
    'title' => 'Search Listing',
    'description' => 'Shows search result events.',
    'category' => 'YouNet Responsive Event',
    'type' => 'widget',
    'name' => 'ynresponsiveevent.event-search-listing',
    'requirements' => array(),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
                'Radio',
                'view_mode',
                array(
                        'label' => 'Which view mode is default?',
                        'multiOptions' => array(
                            'list' => 'List view.',
                            'grid' => 'Grid view.',
                        ),
                        'value' => 'list',
                )
        ),
      )
    ),
  ),
);
