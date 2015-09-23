<?php

return array(

  // Profile Talks Widget
  array(
    'title' => 'Profile Talks',
    'description' => 'Displays a member\'s talk entries on their profile.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.profile-blogs',
    'isPaginated' => true,
    'requirements' => array(
						'subject' => 'user',
				),
    'defaultParams' => array(
        'title' => 'Talks',
    ),
  ),

  // Club Profile Talks Widget
  array(
    'title' => 'Club Profile Talks',
    'description' => 'Displays a club\'s talk entries on its profile.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.club-profile-blogs',
    'isPaginated' => true,
    'requirements' => array(
						'subject' => 'group',
				),
    'defaultParams' => array(
        'title' => 'Talks',
    ),
  ),

  //Talks Menu Widget
  array(
    'title' => 'Talk Menu',
    'description' => 'Displays menu talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-menu',
  ),
  
  // Top Talks (Most Liked Talks) Widget
  array(
    'title' => 'Trending Talks',
    'description' => 'Displays most liked talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.top-blogs',
    'defaultParams' => array(
      'title' => 'Trending Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max', array('label' => 'Number of Talks show on page.',
                                     'value' => 5)),
        )
    ),
  ),
  
  array(
    'title' => 'Favored Talks',
    'description' => 'Displays favored talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.favorite-blogs',
    'defaultParams' => array(
      'title' => 'Favored Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max', array('label' => 'Number of Talks show on page.',
                                     'value' => 8)),
        )
    ),
  ),
  
  array(
    'title' => 'My Talks',
    'description' => 'Displays my talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.my-blogs',
    'defaultParams' => array(
      'title' => 'My Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max', array('label' => 'Number of Talks show on page.',
                                     'value' => 8)),
        )
    ),
  ),

  // New Talks Widget
   array(
    'title' => 'New Talks',
    'description' => 'Displays new talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.new-blogs',
    'defaultParams' => array(
      'title' => 'New Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array('label' => 'Title')),
          array('Text', 'max', array( 'label' => 'Number of Talks show on page.',
                                      'value' => 8)),
        )
     ),
    ),
    // New Talks Widget
   array(
    'title' => 'Recent Talks',
    'description' => 'Displays recent talks on main page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.recent-blogs',
    'defaultParams' => array(
      'title' => 'Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array('label' => 'Title')),
          array('Text', 'max', array( 'label' => 'Number of Talks show on page.',
                                      'value' => 8)),
        )
     ),
    ),

   //Most Viewed Talks Widget
   array(
    'title' => 'Most Viewed Talks',
    'description' => 'Displays most viewed talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.most-viewed-blogs',
    'defaultParams' => array(
      'title' => 'Most Viewed Talks',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array( 'label' => 'Title')),
          array('Text', 'max',array( 'label' => 'Number of Talks show on page.',
                                     'value' => 4)),
        )
     ),
   ),

   //Most Commented Talks Widget
   array(
    'title' => 'Most Commented Talks',
    'description' => 'Displays most commented talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.most-commented-blogs',
    'defaultParams' => array(
      'title' => 'Most Commented Talks',
    ),
    'adminForm' => array(
        'elements' => array(
            array('Text', 'title', array('label' => 'Title')),
            array('Text', 'max', array('label' => 'Number of Talks show on page.',
                                       'value' => 4)),
        )
     ),
  ),

  //Featured Talks Widget
  array(
    'title' => 'Featured Talks',
    'description' => 'Displays featured talks on Talk Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.featured-blogs',
      'defaultParams' => array(
      'title' => 'Featured Talks',
    ),
  ),

  //Blog Categories Widget
  array(
    'title' => 'Talk Categories',
    'description' => 'Displays blog categories on browse talks page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blog-categories',
  ),

  //Blog Search Widget
  array(
    'title' => 'Talks Search',
    'description' => 'Displays blog search box on browse talks page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-search',
  ),
  
  //Blog Listing Widget
  array(
    'title' => 'Talks Listing',
    'description' => 'Displays list of talks on Listing talks Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-listing',
  ),

  //Blog Statistics
   array(
    'title' => 'Blog Statistics',
    'description' => 'Displays blog statistics on Talks Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-statistic',
  ),
  
  //Top Bloggers Widget
  array(
    'title' => 'Top Bloggers',
    'description' => 'Displays top bloggers on Talks Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.top-bloggers',
    'defaultParams' => array(
      'title' => 'Top Bloggers',
    ),
    'adminForm' => array(
        'elements' => array(
          array('Text', 'title', array('label' => 'Title')),
          array('Text', 'max', array( 'label' => 'Number of Bloggers show on page.',
                                      'value' => 12)),
        )
     ),
   ),

  //View By Date Talks Widget
  array(
    'title' => 'View By Date',
    'description' => 'Displays view by date on Talks Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.view-by-date-blogs',
  ),

  //Talk Tags Widget
  array(
    'title' => 'Tags',
    'description' => 'Displays tags on Talks Browse Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-tags',
  ),

  // Blog Owner Photo Widget
   array(
    'title' => 'Talk Owner Photo',
    'description' => 'Displays talk owner photo on User Talk List Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.owner-photo',
  ),

    // Blog Gluter Menu Widget
   array(
    'title' => 'Talk Side Menu',
    'description' => 'Displays talk side menu on User Talk List Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.blogs-side-menu',
   ),

     // Blog User Archieves Widget
   array(
    'title' => 'Talk User Archive',
    'description' => 'Displays user\'s talk archives Blog List Page.',
    'category' => 'Talks',
    'type' => 'widget',
    'name' => 'ynblog.user-blog-archives',
   ),
)?>