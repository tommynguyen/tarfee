<?php

class QuestionAnswer_Form_Index_Question extends Engine_Form
{
  public function init()
  {    
    $auth = Engine_Api::_()->authorization()->context;
    $user = Engine_Api::_()->user()->getViewer();

    $this->addElement('Textarea', 'mess', array(
    	'required' => true,
    	'rows' 	   => "20",
    	'cols'	   => "50",
    	'class'    => 'text_qa',
    	'filters' => array(
    		'StripTags',
    		new Engine_Filter_Censor(),
    		new Engine_Filter_StringLength(array('max' => '400'))
    	),
    ));
        
    $this->addElement('Button', 'btnSubmit', array(
    	'class' => 'qa_post_btn',
    	'value' => 'Post Question',
    	'type'  => 'button',
     	'onClick' => 'postQuestion()',     	
    ));
    
  
    $this->addElement('hidden', 'thuan', array(	
    	'value' => 'post question',
    ));
    
    $this->addElement('hidden', 'test', array(
    	'value' => 'test',
    	'max'   => '300',
    ));
    $this->addElement('hidden', 'thanh', array(
    	'value' => '1',
    ));
    
    $this->addElement('hidden', 'category', array(
    	'value' => '1',
    ));
    
     
  }
 
}