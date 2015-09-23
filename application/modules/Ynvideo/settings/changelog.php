<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
return array(
    '4.01' => array(
        'Form/Edit.php' => 'Fix the bug when there are no options set for the member\'s  current level',
    ),
    '4.01p1' => array(        
        'settings/manifest.php' => 'Add route for vieweing embedding code',
        'settings/install.php' => 'Recalculate user\'s video count when the module is enabled, and do not disable SE video when this module is enable',
        'Form/Edit.php' => 'Fix storing the authentication video view and comment element',
        'Form/Playlist/Edit.php' => 'Fix storing the suthentication playlist view and comment element',
        'Form/Admin/Settings/Level.php' => 'Add privacy relating group module',
        'controllers/AdminSettingsController.php' => 'Fix getting the permission data privacy when viewing the member level settings, the maximum uploaded video for a user level',
        'controllers/AdminManageController.php' => 'Fetch the large thumbnail image for video when a video is set as a featured video',
        'controllers/VideoController.php' => 'Fix viewing the embeded code',
        'controllers/IndexController.php' => 'Fix the issue viewing a not existed video, add activity when a new video is created',
        'controllers/PlaylistController.php' => 'Check video playlists\' authorizations when doing actions',
        'Model/Video.php' => 'Extend the storeThumbnail method',
        'Plugin/Core.php' => 'Remove user\'s playlist and signature when the user is deleted',
        'Plugin/Adapter/Dailymotion.php' => 'Fetch large thumbnail image for video',
        'Plugin/Adapter/Vimeo.php' => 'Fetch large thumbnail image for video',
        'Plugin/Adapter/Youtube.php' => 'Fetch large thumbnail image for video',
        'Plugin/Adapter/Uploaded.php' => 'Fetch large thumbnail image for video',
        'Api/Core.php' => 'Add function to fetch a large thumbnail for videos',
        'widgets/list-featured-videos/Controller.php' => 'Fetch the large thumbnail image for the video if it doesn\'t exist',          
        'views/scripts/_video_featured.tpl' => 'Strip tags the title of the description',
        'views/scripts/_video.tpl' => 'Check video authorization to show the link Remove',
        'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'settings/my-upgrade-4.01-4.01p1.sql' => 'Added',
        'settings/my.sql' => 'Incremented version',
    ),
    '4.01p2' => array(
    	'Api/Core.php' => 'Fix the bug when deleting the Dailymotion video',        
    	'controllers/IndexController.php' => 'Add the feature create video from a FLV URL',
    	'controllers/IndexController.php' => 'Fix the bug about the public access to the video home page',
        'controllers/IndexController.php' => 'Shorten the log when creating the uploading video not successful',
        'external/scripts/composer_video.js' => 'Fix the bug the link not showed when choosing the Dailymotion video and add the type VideoURL',
		'external/scripts/video.js' => 'Add the feature create video from a FLV URL',
    	'Plugin/Adapter/VideoURL.php' => 'Add the feature create video from a FLV URL',
        'Plugin/Adapter/Dailymotion.php' => 'Add mode transparent for the embeded code',
        'Plugin/Job/Encode.php' => 'Fix the reading encoding configuration',    	
        'settings/install.php' => 'Disable SE module when Ynvideo module is enable and the contrary',
        'settings/changelog.php' => 'Incremented version',
        'settings/manifest.php' => 'Incremented version',
        'views/scripts/index/view.tpl' => 'Add the feature create video from a FLV URL',
    	'views/scripts/_add_to.tpl' => 'Fix the issue about appending the add to element to the document',
        'widgets/list-videos/Controller.php' => 'Fix the bug about the paging when viewing videos belonging to a category',        
        'widgets/list-recent-videos/index.tpl' => 'Fix the bug not showing the widget title when using ajax',
    	'widgets/list-recent-videos/Controller.php' => 'Fix the bug not showing the widget title when using ajax',
    ),
    '4.01p3' => array(
    	'externals/styles/main.css' => 'Decrease the description height of show less mode',
    	'externals/scripts/composer_video.js' => 'Fix the issue when choosing back the Video Source option, the URL textbox does not disappear',
    	'views/scripts/index/create.tpl' => 'Fix the issue when choosing a video source, input the link and choose another video source',
    	'settings/changelog.php' => 'Incremented version',
    	'settings/manifest.php' => 'Incremented version',
    	'settings/my-upgrade-4.01p2-4.01p3.sql' => 'Added',
    	'settings/my.sql' => 'Incremented version',    			
    )
)
?>