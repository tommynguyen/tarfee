<?php

class Questionanswer_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
  	 $objQuestion = new Questionanswer_Model_Question(array());
  	 $objAnswer = new Questionanswer_Model_Answer(array());
  	  
  	  $page = $this->_getParam('page');
  	  if(!is_numeric($page))
  	  	$page = 1;
		
	  $question_id = $this->_getParam('qid');
	  if(!is_numeric($question_id))
		$question_id = 0;
		
	  $questions_list = array(); 
      $question_array = $objQuestion->getQuestions($question_id,0,1,'',0,0);
      if(count($question_array) > 0)
      {                   
          $i =0;
          foreach($question_array as $question_info)
          {    
          	  $qsuser = null;
          	  $table = Engine_Api::_()->getDbtable('users', 'user');
		      $select = $table->select()
		        ->where('user_id = ?', $question_info['user_id']);
		      $qsuser = $table->fetchRow($select);
              	  
          	  $question_info['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('class' => 'qa_photo', 'style' => 'float:left')), array('class' => 'f1'));
          	  $question_info['user_link'] = $this->view->htmlLink($qsuser, $qsuser->getTitle());
              //get answer of question
              $answers_array = $objAnswer->getAnswers($question_info['question_id'], 0, 0);
              
			  $answers_list = array();
              foreach ($answers_array as $answer_info)
	          {
	              $auser = null;
	              $table = Engine_Api::_()->getDbtable('users', 'user');
				  $select = $table->select()
				      ->where('user_id = ?', $answer_info['user_id']);
				  $auser = $table->fetchRow($select);
		              	  
		       	  $answer_info['user_photo'] = $this->view->htmlLink($auser, $this->view->itemPhoto($auser, 'thumb.icon', $auser->getTitle(), array('class' => 'qa_photo', 'style' => 'float:left')), array('class' => 'f1'));
				  $answer_info['user_link'] = $this->view->htmlLink($auser, $auser->getTitle());
		       	  $answers_list[] = $answer_info;	
	          }
              
              $questions_list[$i]['answers_list'] = $answers_list;
              $questions_list[$i]['question'] = $question_info;
              $i++;
          }
      } 
      
       // Make paginator
       $this->view->paginator = $paginator = Zend_Paginator::factory($questions_list);
       $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    
  } 
  
  public function viewreportAction()
  {
  	$objReport = new Questionanswer_Model_Report(array());
  	
  	$page = $this->_getParam('page');
  	  if(!is_numeric($page))
  	  	$page = 1;
      $report_array = $objReport->getAllReports();
       
       // Make paginator
       $this->view->paginator = $paginator = Zend_Paginator::factory($report_array);
       $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

  public function deletequestionAction()
  {
  	$objQuestion = new Questionanswer_Model_Question(array());  	  
  	  
  	$id = $this->_getParam('id', null);    
    $this->view->form = $form = new Questionanswer_Form_Admin_Manage_DeleteQuestion();
    
   if ($this->getRequest()->isPost()) 
    { 
      try
      {
      	$objQuestion->deleteQuestion($id);
      }

      catch( Exception $e )
      {       
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Question deleted.')
      ));
    }	
  }
  
  public function deleteanswerAction()
  {
  	$objAnswer = new Questionanswer_Model_Answer(array());
  	  
  	$id = $this->_getParam('id', null);    
    $this->view->form = $form = new Questionanswer_Form_Admin_Manage_DeleteAnswer();
    
    if ($this->getRequest()->isPost()) 
    { 
      try
      {
      	$objAnswer->deleteAnswer($id);
      }

      catch( Exception $e )
      {       
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Answer deleted.')
      ));
    }	
  }
  
  public function deletereportAction()
  {
  	$objReport = new Questionanswer_Model_Report(array());
  	$id = $this->_getParam('id', null);    
    $this->view->form = $form = new Questionanswer_Form_Admin_Manage_DeleteReport();
    
   if ($this->getRequest()->isPost()) 
    { 
      try
      {
      	$objReport->deleteReport($id);
      }

      catch( Exception $e )
      {       
        throw $e;
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Report deleted.')
      ));
    }	
  }
  
  public function multimodifyAction()
  {
  	$objReport = new Questionanswer_Model_Report(array());
  	$id_arr = $this->_getParam('id', null);    
        
   if ($this->getRequest()->isPost()) 
    { 
      try
      {
      	foreach($id_arr as $id)
      	{
      		$objReport->deleteReport($id);
      	}
      }

      catch( Exception $e )
      {       
        throw $e;
      } 
      return $this->_helper->redirector->gotoRoute(array('action' => 'viewreport'));     
    }	
  }
  
  public function editquestionAction()
  {  
  	$objQuestion = new Questionanswer_Model_Question(array()); 
  	
    $id = $this->_getParam('id', null);    
    $this->view->form = $form = new Questionanswer_Form_Admin_Manage_EditQuestion();
    $question = $objQuestion->getQuestionById($id);
    
    // Posting form
    if( $this->getRequest()->isPost() )
    {
      if( $form->isValid($this->getRequest()->getPost()) )
      {
        $data_array = $form->getValues();
        $date_updated = date('Y-m-d H:i:s');
        $objQuestion->updateQuestion($id, $data_array['content'], $date_updated);
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Question Edited.')
      ));
    }

    // Initialize data
    else
    {
     foreach( $form->getElements() as $name => $element )
      {      	        
        if( isset($question[$name]) )
        {
          $element->setValue($question[$name]);
        }
      }
    }
  }
  
  public function editanswerAction()
  {
  	$objAnswer = new Questionanswer_Model_Answer(array());
    $id = $this->_getParam('id', null);    
    $this->view->form = $form = new Questionanswer_Form_Admin_Manage_EditAnswer();
    $answer = $objAnswer->getAnswerById($id);
    
    // Posting form
    if( $this->getRequest()->isPost() )
    {
      if( $form->isValid($this->getRequest()->getPost()) )
      {
        $data_array = $form->getValues();
        $date_updated = date('Y-m-d H:i:s');
        $objAnswer->updateAnswer($id, $data_array['content'], $date_updated);
      }
      
      $this->_forward('success', 'utility', 'core', array(
      'smoothboxClose' => true,
      'parentRefresh' => true,
      'format'=> 'smoothbox',
      'messages' => array('Answer Edited.')
      ));
    }

    // Initialize data
    else
    {
     foreach( $form->getElements() as $name => $element )
      {      	        
        if( isset($answer[$name]) )
        {
          $element->setValue($answer[$name]);
        }
      }
    }
  }
}
?>