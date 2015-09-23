<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
return array(
    array(
        'title' => 'List Categories',
        'description' => 'Displays a list categories.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-categories',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
    array(
        'title' => 'Top Members',
        'description' => 'Displays a list top members having most videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-top-members',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfMembers',
                    array(
                        'label' => 'Number of members',
                        'value' => '5',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Top Members',
        ),
    ),
    array(
        'title' => 'Most Liked Videos',
        'description' => 'Displays a list of most liked videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-liked-videos',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfVideos',
                    array(
                        'label' => 'Number of videos',
                        'value' => '5',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'marginLeft',
                    array(
                        'label' => 'Margin left of each video',
                        'value' => '36',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
        'defaultParams' => array(
            'title' => 'Most Liked Videos',
        )
    ),
    array(
        'title' => 'Featured Videos',
        'description' => 'Displays a list by slideshow of featured videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-featured-videos',
        'defaultParams' => array(
            'title' => 'Featured Videos',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'slidingDuration',
                    array(
                        'label' => 'Time between slide animation',
                        'value' => '5000',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(1000)),
                        )
                    )
                ),
                array(
                    'Text',
                    'slideWidth',
                    array(
                        'label' => 'Width(px) of featured videos frame',
                        'value' => '530',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Text',
                    'slideHeight',
                    array(
                        'label' => 'Height(px) of featured videos frame',
                        'value' => '340',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Most Favorite Videos',
        'description' => 'Displays a list of most favorite videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-favorite-videos',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfVideos',
                    array(
                        'label' => 'Number of videos',
                        'value' => '5',
                        'validators' => array(
                            array('Int', true),                            
                            array('GreaterThan', true, array(0)),                            
                        )
                    )
                ),
                array(
                    'Text',
                    'marginLeft',
                    array(
                        'label' => 'Margin left of each video',
                        'value' => '36',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'View Type',
                        'multiOptions' => array(
                            'small' => 'Small Widget',
                            'big' => 'Big Widget',
                        ),
                        'value' => 'view',
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Profile Videos',
        'description' => 'Displays a member\'s videos on their profile.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.profile-videos',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Videos',
        ),
    ),
    array(
        'title' => 'Profile Favorite Videos',
        'description' => 'Displays a member\'s favorite videos on their profile.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.profile-favorite-videos',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Favorite Videos',
        ),
    ),
    array(
        'title' => 'Profile Video Playlists',
        'description' => 'Displays a member\'s video playlists on their profile.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.profile-video-playlists',
        'isPaginated' => true,
        'requirements' => array(
            'subject' => 'user',
        ),
        'defaultParams' => array(
            'title' => 'Video Playlists',
        ),
    ),
    array(
        'title' => 'Recent Videos',
        'description' => 'Displays a list of recently uploaded videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-recent-videos',
        'defaultParams' => array(
            'title' => 'Recent Videos',
        ),
        'requirements' => array(
            'no-subject',
        ),
        'isPaginated' => true,
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
                        ),
                        'value' => 'creation',
                    )
                ),
                array(
                    'Text',
                    'marginLeft',
                    array(
                        'label' => 'Margin left of each video',
                        'value' => '36',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Popular Videos',
        'description' => 'Displays a list of most viewed videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-popular-videos',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Popular Videos',
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
                            'rating' => 'Rating',
                            'view' => 'Views',
                            'comment' => 'Comments',
                        ),
                        'value' => 'view',
                    )
                ),
                array(
                    'Radio',
                    'viewType',
                    array(
                        'label' => 'View Type',
                        'multiOptions' => array(
                            'small' => 'Small Widget',
                            'big' => 'Big Widget',
                        ),
                        'value' => 'view',
                    )
                ),
                array(
                    'Text',
                    'marginLeft',
                    array(
                        'label' => 'Margin left of each video',
                        'value' => '36',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'People Also Liked',
        'description' => 'Displays a list of other videos that the people who liked this video also liked.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.show-also-liked',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'People Also Liked',
        ),
        'requirements' => array(
            'subject' => 'video',
        ),
    ),
    array(
        'title' => 'Suggested Videos',
        'description' => 'Displays a list of other videos that the member/player that uploaded this video uploaded.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.show-same-poster',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Suggested Videos',
        ),
        'requirements' => array(
            'subject' => array('video','user')
        ),
    ),
    array(
        'title' => 'Similar Videos (tags)',
        'description' => 'Displays a list of other videos that are similar to the current video, based on tags.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.show-same-tags',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Similar Videos (tags)',
        ),
        'requirements' => array(
            'subject' => 'video',
        ),
    ),
    array(
        'title' => 'Related Videos',
        'description' => 'Displays a list of other videos that has the same category to the current video',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.show-same-categories',
        'isPaginated' => true,
        'defaultParams' => array(
            'title' => 'Related Videos',
        ),
        'requirements' => array(
            'subject' => 'video',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'numberOfVideos',
                    array(
                        'label' => 'Number of videos',
                        'value' => '5',
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        )
                    )
                ),
            )
        )
    ),
    array(
        'title' => 'Video Browse Search',
        'description' => 'Displays a search form in the video browse page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.browse-search',
        'requirements' => array(
            'no-subject',
        ),
        'defaultParams' => array(
            'title' => 'Search Videos',
        ),
    ),
    array(
        'title' => 'Video Browse Menu',
        'description' => 'Displays a menu in the video browse page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.browse-menu',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'Video Browse Quick Menu',
        'description' => 'Displays a small menu in the video browse page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.browse-menu-quick',
        'requirements' => array(
            'no-subject',
        ),
    ),
    array(
        'title' => 'List User Favorite Videos',
        'description' => 'Displays a list of current user\'s favorite videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-my-favorite-videos',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'List My Videos',
        'description' => 'Displays a list current user\'s videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-manage-videos',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'List My Video Playlists',
        'description' => 'Displays a list current user\'s video playlists.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-my-playlists',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'List My Watch Later Videos',
        'description' => 'Displays a list current user\'s watch later videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-my-watch-later-videos',
        'requirements' => array(
            'viewer',
        ),
    ),
    array(
        'title' => 'List All Videos',
        'description' => 'Displays a list of all videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.list-videos',
    ),
    
	array(
        'title' => 'Main Page Videos',
        'description' => 'Displays a list of videos on main page.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.main-page-videos',
    ),
    
	array(
        'title' => 'Players of the Week/Day',
        'description' => 'Displays a list of top talent videos.',
        'category' => 'Advanced Videos',
        'type' => 'widget',
        'name' => 'ynvideo.players-of-week',
        'defaultParams' => array(
            'title' => 'Players of the Week/Day',
            'numberOfVideos' => 5,
            'weekDay' => 'sunday',
            'dayHour' => 0,
            'share_internal' => 2,
            'like' => 3,
            'comment' => 2,
            'view' => 1,
            'dislike' => -1,
            'unsure' => 0
        ),
        'adminForm' => 'Ynvideo_Form_Admin_Widget_PlayersOfWeek'
    ),
)
?>