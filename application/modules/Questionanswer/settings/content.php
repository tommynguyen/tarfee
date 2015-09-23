<?php

return array(
  array(
  	'title' => 'Q&amp;A',
    'description' => 'Display all question and answer.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.full-question-answer',
    'defaultParams' => array(
  		'title' => 'Q&A',
  	),
	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Q&A',
          )
        ),     
      )
    ),
  ),  
  array(
  	'title' => 'Top Users',
    'description' => 'Display user have most question.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.top-user',
    'defaultParams' => array(
  		'title' => 'Top Users',  		
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Top Users',
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ), 
  array(
  	'title' => 'Top Answers',
    'description' => 'Display user have most answer.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.top-answer',
    'defaultParams' => array(
  		'title' => 'Top Answers',  		
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Top Answers',
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),  
  array(
  	'title' => 'Top Questions',
    'description' => 'Display question have most like.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.top-question',
    'defaultParams' => array(
  		'title' => 'Top Questions',	
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => 'Top Questions',  	
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),
  array(

      'title' => 'New Questions',

    'description' => 'Display newest question.',

      'category' => 'Q&A',

      'type' => 'widget',

    'name' => 'questionanswer.new-question',

    'defaultParams' => array(

          'title' => 'New Questions',    

      ),

      'adminForm' => array(

      'elements' => array(

        array(

          'Text',

          'title',

          array(

            'label' => 'Title', 'value' => 'New Questions',      

          )

        ),

        array(

          'Text',

          'max',

         

           array(

            'label' => 'Max Item Count',

            'description' => 'Number of shown data item of each widget.',

            'value' => 5,

            

          )

          

        ),

      )

    ),

  ),

  array(
  	'title' => "Top Friends' Answers",
    'description' => 'Display top answers of friends and owner.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.top-friend-answer',
    'defaultParams' => array(
  		'title' => "Top Friends' Answers",  		
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => "Top Friends' Answers",
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),  
  array(
  	'title' => "Top Friends' Questions",
    'description' => 'Display top questions of friends and owner.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.top-friend-question',
    'defaultParams' => array(
  		'title' => "Top Friends' Questions",	
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => "Top Friends' Questions",  	
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),
  
  array(
  	'title' => "New Friends' Answers",
    'description' => 'Display new answers of friends and owner.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.new-friend-answer',
    'defaultParams' => array(
  		'title' => "New Friends' Answers",  		
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => "New Friends' Answers",
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),  
  array(
  	'title' => "New Friends' Questions",
    'description' => 'Display new questions of friend and owner.',
  	'category' => 'Q&A',
  	'type' => 'widget',
    'name' => 'questionanswer.new-friend-question',
    'defaultParams' => array(
  		'title' => "New Friends' Questions",	
  	),
  	'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title', 'value' => "New Friends' Questions",  	
          )
        ),
        array(
          'Text',
          'max',
         
           array(
            'label' => 'Max Item Count',
            'description' => 'Number of shown data item of each widget.',
            'value' => 5,
            
          )
          
        ),
      )
    ),
  ),
) ?>