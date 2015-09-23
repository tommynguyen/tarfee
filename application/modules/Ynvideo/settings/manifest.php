<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
return array(
    'package' => array(
        'type' => 'module',
        'name' => 'ynvideo',
        'version' => '4.03p4',
        'path' => 'application/modules/Ynvideo',
        'title' => 'YN - Advanced Video',
        'description' => 'YouNet Video Plugin',
        'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
        'actions' => array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Ynvideo/settings/install.php',
            'class' => 'Ynvideo_Installer',
        ),
        'directories' => array(
            0 => 'application/modules/Ynvideo',
        ),
        'files' => array(
            0 => 'application/languages/en/ynvideo.csv',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'video',
                'minVersion' => '4.1.3',
            ),
            array(
                'type' => 'module',
                'name' => 'younet-core',
                'minVersion' => '4.02p3',
            ),
        ),
    ),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'video',
        'ynvideo',
        'ynvideo_video',
        'ynvideo_signature',
        'ynvideo_favorite',
        'ynvideo_playlist',
        'ynvideo_playlistassoc',
        'video_category',
	    'ynvideo_ratingtype',
	    'ynvideo_review_rating',
    ),
    // Compose
    'composer' => array(
        'video' => array(
            'script' => array('_composeVideo.tpl', 'ynvideo'),
            'plugin' => 'Ynvideo_Plugin_Composer',
            'auth' => array('video', 'create'),
        ),
    ),
    'directories' => array(
        'application/modules/Ynvideo',
    ),
    'files' => array(
        'application/languages/en/ynvideo.csv',
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onStatistics',
            'resource' => 'Ynvideo_Plugin_Core'
        ),
        array(
            'event' => 'onUserDeleteBefore',
            'resource' => 'Ynvideo_Plugin_Core',
        ),
        array(
            'event' => 'onItemCreateAfter',
            'resource' => 'Ynvideo_Plugin_Core',
        ),
    	array(
            'event' => 'onItemDeleteBefore',
            'resource' => 'Ynvideo_Plugin_Core',
        ),    
	
    ),
    'routes' => array(
        'video_other_route' => array(
            'route' => 'video/:controller/:action/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'video'
            )
        ),
        'video_general' => array(
            'route' => 'videos/:action/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'index',
                'action' => 'index',
            ),
            'reqs' => array(
                'action' => '(index|browse|create|list|manage|validation|add-to|edit|delete|rate|compose-upload|add-to-group|rating)',
            )
        ),
        'video_admin_general' => array(
            'route' => 'admin/video/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'admin-manage',
                'action' => 'index'
            ),
            'reqs' => array(
                'controller' => '(admin-manage|admin-settings)',
            )
        ),
        'video_view' => array(
            'route' => 'videos/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'index',
                'action' => 'view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'video_mobile_view' => array(
            'route' => 'videos/mobile/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'index',
                'action' => 'mobile-view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'video_popup_view' => array(
            'route' => 'videos/popup/:user_id/:video_id/:slug/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'index',
                'action' => 'popup-view',
                'slug' => '',
            ),
            'reqs' => array(
                'user_id' => '\d+'
            )
        ),
        'video_favorite' => array(
            'route' => 'videos/favorite/:action/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'favorite',
                'action' => 'index',
            )
        ),
        'video_playlist' => array(
            'route' => 'videos/playlist/:action/:slug/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'playlist',
                'action' => 'index',
                'slug' => '-'
            )
        ),
        'video_watch_later' => array(
            'route' => 'videos/watch-later/:action/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'watch-later',
                'action' => 'index'
            )
        ),
        'ynvideo_admin_settings' => array(
            'route' => 'video/admin-settings/:action/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'admin-settings',
            )
        ),
        'ynvideo_compose' => array(
            'route' => 'video/index/compose-upload/*',
            'defaults' => array(
                'module' => 'ynvideo',
                'controller' => 'index',
                'action' => 'compose-upload'
            )
        )
    )
);
?>