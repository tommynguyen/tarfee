<?php
return array(
	array(
	    'title' => 'Feedback Main Menu Widget',
	    'description' => 'Displays feedback main menu.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.main-menu',
	    'defaultParams' => array(
	    ),
	),
	array(
	    'title' => 'Feedback Poll Widget',
	    'description' => 'Displays a poll view.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.view-poll',
	    'defaultParams' => array(
	    ),
	),
	array(
	    'title' => 'Highlight Feedback Widget',
	    'description' => 'Highlight Feedback Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.highlight-feedback',
		'isPaginated' => true,
	    'defaultParams' => array(
			'title' => 'Highlight Feedback',
	      	'itemCountPerPage' => 8
	    ),
	),
	array(
	    'title' => 'Feedback Search Widget',
	    'description' => 'Feedback Search Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.feedback-search',
	    'defaultParams' => array(
	    ),
	),
	array(
	    'title' => 'Feedback Listing Widget',
	    'description' => 'Feedback Listing Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.feedback-listing',
	    'defaultParams' => array(
			'title' => 'Browse Feedback'
	    ),
	),
	array(
	    'title' => 'Most Followed Feedback Widget',
	    'description' => 'Displays Most Followed Feedback Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.most-followed-feedback',
	    'defaultParams' => array(
			'title' => 'Most Followed Feedback',
	    ),
	),
	array(
	    'title' => 'Most Liked Feedback Widget',
	    'description' => 'Displays Most Liked Feedback Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.most-liked-feedback',
	    'defaultParams' => array(
			'title' => 'Most Liked Feedback',
	    ),
	),
	array(
	    'title' => 'Most Discussed Feedback Widget',
	    'description' => 'Displays Most Discussed Feedback Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.most-discussed-feedback',
	    'defaultParams' => array(
			'title' => 'Most Discussed Feedback',
	    ),
	),
	array(
	    'title' => 'Most Voted Feedback Widget',
	    'description' => 'Displays Most Voted Feedback Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.most-voted-feedback',
	    'defaultParams' => array(
			'title' => 'Most Voted Feedback',
	    ),
	),
	array(
	    'title' => 'Profile Comment Widget',
	    'description' => 'Displays Feedback Profile Comment Widget.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.profile-comment',
	    'defaultParams' => array(
	    ),
	),
	array(
	    'title' => 'Feedback Browse Quick Menu',
	    'description' => 'Displays a small menu in the feedback browse page.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.browse-menu-quick',
	    'requirements' => array(
			'no-subject',
		),
	),
	array(
	    'title' => 'Middle Categories Widget',
	    'description' => 'Displays Feedback Categories in Middle Column.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'name' => 'ynfeedback.middle-categories',
	    'isPaginated' => true,
	    'defaultParams' => array(
			'title' => 'Browse by Category',
			'itemCountPerPage' => 9
	    ),
	),
	array(
	    'title' => 'Right Categories Widget',
	    'description' => 'Displays Feedback Categories in Right Column.',
	    'category' => 'Feedback',
	    'type' => 'widget',
	    'isPaginated' => true,
	    'name' => 'ynfeedback.right-categories',
	    'defaultParams' => array(
			'title' => 'Browse by Category',
			'itemCountPerPage' => 10
	    ),
	),
	
    array(
        'title' => 'Feedback Manage Menu',
        'description' => 'Displays manage menu in my feedback page',
        'category' => 'Feedback',
        'type' => 'widget',
        'name' => 'ynfeedback.feedback-manage-menu',
        'defaultParams' => array(
        ),
    ),
);
