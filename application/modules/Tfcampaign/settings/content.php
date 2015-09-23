<?php
return array(
	
	array(
        'title' => 'Campaign - Filter Campaigns',
        'description' => 'Displays filter campaigns in home page.',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.filter-campaigns'
	),
	
	array(
        'title' => 'Campaign - My saved Campaigns',
        'description' => 'Displays saved campaigns of user.',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.my-saved-campaigns'
	),
	
	array(
        'title' => 'Campaign - My Campaigns',
        'description' => 'Displays campaigns which are belong to user.',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.my-campaigns'
	),
	
	array(
        'title' => 'Campaign - My submissions',
        'description' => 'Displays submissions which are belong to user.',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.my-submissions'
	),
	
	array(
        'title' => 'Campaign - User Profile Campaigns',
        'description' => 'Displays campaign on user profile page.',
        'category' => 'Campaign',
        'type' => 'widget',
        'isPaginated' => true,
        'name' => 'tfcampaign.user-profile-campaign'
	),
	
	array(
        'title' => 'Campaign - Club Profile Campaigns',
        'description' => 'Displays campaign on club profile page.',
        'category' => 'Campaign',
        'type' => 'widget',
        'isPaginated' => true,
        'name' => 'tfcampaign.club-profile-campaign'
	),
	
	array(
        'title' => 'Campaign - Profile Player Fulfill Info',
        'description' => 'Displays Suggest Info for users to fulfill the campaign info on Campaign Detail page',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.profile-fulfill-info',
        'requirements' => array(
	      'subject' => 'tfcampaign_campaign',
	    ),
    ),
    
	array(
        'title' => 'Campaign - Recent Campaigns',
        'description' => 'Displays recent campaigns on main page.',
        'category' => 'Campaign',
        'type' => 'widget',
        'isPaginated' => true,
        'name' => 'tfcampaign.recent-campaign'
	),
	
	array(
        'title' => 'Campaign - Profile Player Submissions',
        'description' => 'Displays Player Submissions on campaign Detail page',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.profile-submission',
        'requirements' => array(
	      'subject' => 'tfcampaign_campaign',
	    ),
    ),
    
	array(
        'title' => 'Campaign - Profile Hidden Submissions',
        'description' => 'Displays Player Hidden Submissions on campaign Detail page',
        'category' => 'Campaign',
        'type' => 'widget',
        'name' => 'tfcampaign.profile-hidden-submission',
        'requirements' => array(
	      'subject' => 'tfcampaign_campaign',
	    ),
    ),
);