<?php

return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'questionanswer',
    'version' => '4.02',
    'path' => 'application/modules/Questionanswer',
    'meta' => array(
      'title' => 'Question & Answer',
      'description' => 'Question & Answer',
      'author' => 'YouNet Developments',
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 4000,
    ),
    'directories' => array(
      'application/modules/Questionanswer',
    ),
    'files' => array(
      'application/languages/en/questionanswer.csv',
    ),
  ),
  // Content -------------------------------------------------------------------
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
	array(
      'event' => 'onStatistics',
      'resource' => 'Questionanswer_Plugin_Core'
    )
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'questionanswer',
  	'qa',
    'questionanswer_question',
  	'questionanswer_questions',
  	'questionanswer_questionvotes',
  	'questionanswer_answer',
  	'questionanswer_report'
  	
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array( 	
    'questionanswer_extend' => array(
      'route' => 'qa',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'index'
     ),
     'reps' => array(
    	'action' => '(index)',
     )
    ),
    'questionanswer_special' => array(
      'route' => 'qa/:id',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'index'
     )    
    ),
    'questionanswer_list' => array(
      'route' => 'questionanswer/list',
      'defaults' => array(
        'module' => 'questionanswer',
        'controller' => 'index',
        'action' => 'list'
      ),
      'reqs' => array(
        'action' => '(list)',
      )
    ),
    'questionanswer_postquestion' => array(
      'route' => 'questionanswer/postquestion',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'postquestion'
     ),
     'reps' => array(
    	'action' => '(postquestion)',
     )
    ),
     'questionanswer_postanswer' => array(
      'route' => 'questionanswer/postanswer',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'postanswer'
     ),
     'reps' => array(
    	'action' => '(postanswer)',
     )
    ),
    'qa_home' => array(
      'route' => 'qa/home',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'home'
     ),
     'reps' => array(
    	'action' => '(home)',
     )
    ),
    'questionanswer_votequestion' => array(
      'route' => 'questionanswer/votequestion',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'votequestion'
     ),
     'reps' => array(
    	'action' => '(votequestion)',
     )
    ),
    'questionanswer_report' => array(
      'route' => 'qa/addreport',
      'defaults' => array(
    	'module' => 'questionanswer',
    	'controller' => 'index',
    	'action' => 'addreport'
     ),
     'reps' => array(
    	'action' => '(addreport)',
     )
    ),
  )
);