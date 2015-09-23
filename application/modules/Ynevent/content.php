<?php

return array(
    array(
        'title' => 'Upcoming Advanced Events',
        'description' => 'Displays the logged-in member\'s upcoming advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.home-upcoming',
        'isPaginated' => true,
        'requirements' => array(
            'viewer',
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title'
                    )
                ),
                array(
                    'Radio',
                    'type',
                    array(
                        'label' => 'Show',
                        'multiOptions' => array(
                            '1' => 'Any upcoming advanced events.',
                            '2' => 'Current member\'s upcoming advanced events.',
                            '0' => 'Any upcoming events when member is logged out, that member\'s advanced events when logged in.',
                        ),
                        'value' => '0',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Profile Advanced Events',
        'description' => 'Displays a member\'s advanced events on their profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'event.profile-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Advanced Events',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'user',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Discussions',
        'description' => 'Displays a advanced event\'s discussions on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-discussions',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Discussions',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Info',
        'description' => 'Displays a advanced event\'s info (creation date, member count, etc) on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-info',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Members',
        'description' => 'Displays a advanced event\'s members on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-members',
        'isPaginated' => true,
        'defaultParams' => array(
           // 'title' => 'Guests',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Options',
        'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a advanced event on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-options',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Photo',
        'description' => 'Displays a advanced event\'s photo on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-photo',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Photos',
        'description' => 'Displays a advanced event\'s photos on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-photos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Photos',
            'titleCount' => true,
        ),
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile RSVP',
        'description' => 'Displays options for RSVP\'ing to an advanced event on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-rsvp',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Status',
        'description' => 'Displays a advanced event\'s title on it\'s profile.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-status',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
    array(
        'title' => 'Popular Events',
        'description' => 'Displays a list of most viewed advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.list-popular-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Advanced Events',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'view' => 'Views',
                            'member' => 'Members',
                        ),
                        'value' => 'view',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Most Attending',
        'description' => 'Displays a list of most attending advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.list-most-attending-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Most Attending',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'popularType',
                    array(
                        'label' => 'Popular Type',
                        'multiOptions' => array(
                            'view' => 'Views',
                            'member' => 'Members',
                        ),
                        'value' => 'view',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Most Rated',
        'description' => 'Displays a list of most rated advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.list-most-rated-events',
        'defaultParams' => array(
            'title' => 'Most Rated',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Most Liked',
        'description' => 'Displays a list of most liked advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.list-most-liked-events',
        'defaultParams' => array(
            'title' => 'Most Liked',
        ),
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Recent Advanced Events',
        'description' => 'Displays a list of recently created advanced events.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.list-recent-events',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Recent Events',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'recentType',
                    array(
                        'label' => 'Recent Type',
                        'multiOptions' => array(
                            'creation' => 'Creation Date',
                            'modified' => 'Modified Date',
                            'start' => 'Started',
                            'end' => 'Ended',
                        ),
                        'value' => 'creation',
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Advanced Event Browse Search',
        'description' => 'Displays a search form in the advanced event browse page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.browse-search',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Event Browse Menu',
        'description' => 'Displays a menu in the advanced event browse page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Event Browse Quick Menu',
        'description' => 'Displays a small menu in the advanced event browse page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.browse-menu-quick',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Add Rates',
        'description' => 'Displays options for rates, like with Facebook, G+, Twitter to an advanced event on it\'s profile',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-add-rates',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Event Profile Follow',
        'description' => 'Displays options for Following\'ing to an advanced event on it\'s profile',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.profile-follow',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Advanced Event Calendar',
        'description' => 'Jay Chou.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'name' => 'ynevent.events-calendar',
        'requirements' => array(
            'subject' => 'ynevent',
        ),
    ),
        )
?>