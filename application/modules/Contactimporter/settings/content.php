<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Contact Importer
 * @copyright  YouNet 2010
 * @version    1.0
 * @author     longldh
 */
return array(
	array(
	    'title' => 'Inviter Menu',
	    'description' => 'Displays a Contact inviter menu',
	    'category' => 'Contactimporter',
	    'type' => 'widget',
	    'name' => 'contactimporter.menu',
	    'requirements' => array(
	      'no-subject',
	    ),
	),
	array(
	    'title' => 'Top Inviters',
	    'description' => 'Top Inviters that invited the most of their friends',
	    'category' => 'Contactimporter',
	    'type' => 'widget',
	    'name' => 'contactimporter.top-inviters',
	    'defaultParams' => array(
		      'title' => 'Top Inviters',
		),
	),
	
	array(
	    'title' => 'Inviter Statistics',
	    'description' => 'Displays inviter statisctics on Contact Importer plugin',
	    'category' => 'Contactimporter',
	    'type' => 'widget',
	    'name' => 'contactimporter.statistics',
	    'defaultParams' => array(
		      'title' => 'Inviter Statistics',
		),
	    'requirements' => array(
	      'subject' => 'user',
	    ),
	),
  
  	array(
    'title' => 'Home Page Inviter',
    'description' => 'After users sign up, the invite form would be on user home page until they want to close it.',
    'category' => 'Contactimporter',
    'type' => 'widget',
    'name' => 'contactimporter.homepage-inviter',
    'defaultParams' => array(
      'title' => 'Import Your Contacts',
    ), 
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'max',
           array(
            'label' => 'Number of providers show on widget.',
            'value' => 10,
            
          )
        ),
      )
    ),
  ),
) ?>
