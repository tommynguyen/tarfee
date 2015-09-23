<?php

class Questionanswer_IndexController extends Core_Controller_Action_Standard
{
     public function checkVersionSE()
  {
      $c_table  = Engine_Api::_()->getDbTable('modules', 'core');
      $c_name   = $c_table->info('name');
      $select   = $c_table->select()
                        ->where("$c_name.name LIKE ?",'core')->limit(1);
      
      $row = $c_table->fetchRow($select)->toArray();
      $strVersion = $row['version'];
      $intVersion = (int)str_replace('.','',$strVersion);
      return  $intVersion >= 410?true:false;
  }  
  protected $_paginate_params = array();
  public function init()
  {  	
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('question.questionsPerPage', 5);
  }
  public function indexAction()
  {   	 
  	$qid = $this->_getParam('id');
  	if(!empty($qid) && $qid > 0)
  	{
  		$question = Engine_Api::_()->getItem('questionanswer_questions', $qid);
	  	if( $question instanceof Core_Model_Item_Abstract)
	    {
	        if(!Engine_Api::_()->core()->hasSubject() )
	        {
	          Engine_Api::_()->core()->setSubject($question);
	        }
	    }
  	}
    //$apiQA = new QuestionAnswer_Api_Core();
     if($this->checkVersionSE())//version 4.1.x
        {
            $this->_helper->content->setNoRender()->setEnabled();
        }
        else//version 4.0.x
        {
            $this->_helper->content->render();    
        }          
  }
  
  public function homeAction()
  {
  	  	  
  }
  public function listAction()
  {
  	  //get object
  	  $objQuestion = new Questionanswer_Model_Question(array());
  	  $objQuestionVote = new Questionanswer_Model_Questionvotes(array());
  	  $objAnswer = new Questionanswer_Model_Answer(array());
  	  
  	  $_user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
  	  if(!$_user_id)
  	  	$_user_id = 0;
      //get user id
      $user_id =  $_POST['user_id'];
      $_category     = $_POST['category'];
      $_page         = $_POST['page'];
      $_search_query = "";
      $_search		 = $_POST['search'];	  
	  $_question_id = $_POST['qid'];
      // SEARCH QUESTION
	  if( !empty($_search) )
	  { 
	  	//$_search = htmlentities(stripslashes($_search));
	    $_search_arr = explode(" ", $_search);
	    $_searchs = array();
	    foreach ($_search_arr as $item)
	    {
	  	  if($item != "")
	  	  {
	  		$_searchs[] = $item;
	  	  }
	    }
	    $_search_query = implode("%", $_searchs);
	    $_search_query = "%" . $_search_query . "%";	
	  
	    $_search_query = addslashes($_search_query);
	  }
	  
      if($_page <= 0)
        $_page = 1;
              
      //get question      
      $questions_per_page = 5;
      $total_questions = $objQuestion->countQuestion($_question_id, $user_id, $_category, $_search_query);
      $page_vars = make_page($total_questions, $questions_per_page, $_page);            
	  $question_array = $objQuestion->getQuestions($_question_id, $user_id, $_category, $_search_query, $questions_per_page, $page_vars[0]);
      if(count($question_array) > 0)
      {
          $questions_list = array();          
          $i =0;
          foreach($question_array as $question_info)
          {    
          	  //get user picture
          	  $qsuser = null;
          	  $table = Engine_Api::_()->getDbtable('users', 'user');
		      $select = $table->select()
		        ->where('user_id = ?', $question_info['user_id']);
		      $qsuser = $table->fetchRow($select);              	  
          	  $question_info['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('class' => 'qa_photo', 'style' => 'float:left')), array('class' => 'f1'));
          	  
          	  //check user voted
          	  $is_vote = $objQuestionVote->getQuestionVotesByUserIdAndQuestionId($_user_id, $question_info['question_id']);
          	  
          	  if($_user_id && ($_user_id != $question_info['user_id']) && $is_vote == 0)
          	  	$is_vote = "1";
          	  else
          	  	$is_vote = "0";
          	  
          	  $question_info['is_allowed'] = $is_vote;
          	  
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
	           	  $answers_list[] = $answer_info;	
	          }
              
              $questions_list[$i]['answers_list'] = $answers_list;
              $questions_list[$i]['question'] = $question_info;
              $i++;
          }
      }   
	  
      if($total_questions == 0)
      {
      	echo '{"result":"norecord", "message":"No data found!"}';
   		die;
      }
      else
      {
      	$page_info = array("p" => $page_vars[1], "maxpage" => $page_vars[2], "p_start" => $page_vars[0] + 1, "p_end" => $page_vars[0] + $total_questions, "total_records" => $total_questions);
      	echo json_encode(array("result"=>$total_questions, "page_info"=>$page_info, "threads_info"=>$questions_list));
      	die;
      }
      
  }  
  
  
  public function postanswerAction()
  {  	
  	$objAnswer = new Questionanswer_Model_Answer(array());
  	  
  	$_user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
  	$question_id = $_POST['question_id'];
  	$content = $_POST['mess'];  	
  	$date_created = date('Y-m-d H:i:s');
  	//$answer = $objAnswer->addAnswer($_user_id, $question_id, $content, $date_created);
	
	$params['user_id'] = $_user_id;
    $params['question_id'] = $question_id;
    $params['content'] = trim($content);
	$params['date_created'] = $date_created;
    $answer = Engine_Api::_()->getDbtable('answers', 'Questionanswer')->createRow();    
    $answer->setFromArray($params);
    $answer->save();
	
	//get question detail
	$objQuestion = new Questionanswer_Model_Question(array());	
	$question_detail = $objQuestion->getQuestionById($question_id);
	
	$objQuestion->updateNumberAnswer($question_id);
	
	//add notification for owner of question
  	if( $answer instanceof Core_Model_Item_Abstract)
    {
        if( !Engine_Api::_()->core()->hasSubject() )
        {
          Engine_Api::_()->core()->setSubject($answer);
        }
    }
    $subject = Engine_Api::_()->core()->getSubject();
    $subjectOwner = Engine_Api::_()->getItem('user', $question_detail['user_id']);
    $viewer = Engine_Api::_()->user()->getViewer();
    if($subjectOwner->getIdentity() != $viewer->getIdentity())
    {		
		$notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
		$notifyApi->addNotification($subjectOwner, $viewer, $answer, 'answer_posted', array('label'=>"question"));
    }
    
	$linkQuestion = $this->view->htmlLink("qa?qid=".$question_detail['question_id'], substr($question_detail['content'], 0, 150)); 
		
	$activity = Engine_Api::_()->getDbtable('actions', 'activity');
    $action   = $activity->addActivity(
       Engine_Api::_()->user()->getViewer(),
       Engine_Api::_()->user()->getViewer(),
       'answer_new',
	   null,
	   array('body' => substr(trim($content), 0, 200), 'question' => $linkQuestion)
   );
   if (null !== $action){
   	  	$activity->attachActivity($action, $answer);
   }
   
  	echo '{"result":"success", "message":"Post message successfully!"}';
    die;
  }
  
  public function postquestionAction()
  { 
  	$objQuestion = new Questionanswer_Model_Question(array());
  	  
  	$_user_id = $this->_helper->api()->user()->getViewer()->getIdentity(); 	
  	$content = $_POST['mess'];
  	$category_id = $_POST['category'];
  	$date_created = date('Y-m-d H:i:s');
  		
	$params['user_id'] = $_user_id;
    $params['cat_id'] = $category_id;
    $params['content'] = trim($content);
	$params['date_created'] = $date_created;
    $question = Engine_Api::_()->getDbtable('questions', 'Questionanswer')->createRow();    
    $question->setFromArray($params);
    $question->save();
    
    $linkQuestion = $this->view->htmlLink("qa?qid=".$question['question_id'], substr($content, 0, 150)); 
    
  	//create activity
    $activity = Engine_Api::_()->getDbtable('actions', 'activity');
    $action   = $activity->addActivity(
        Engine_Api::_()->user()->getViewer(),
        Engine_Api::_()->user()->getViewer(),
        'question_new',
		null,
		array('body' => $linkQuestion)
     );
     if (null !== $action){
     	$activity->attachActivity($action, $question);
     }
	 
  	echo '{"result":"success", "message":"Post message successful!"}';
    die;
  }
  
  public function votequestionAction()
  {
  	$objQuestionVote = new Questionanswer_Model_Questionvotes(array());
  	
  	$_user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
  	$question_id = $_POST['question_id'];
  	
  	$objQuestionVote->addQuestionVotes($_user_id, $question_id);
  	echo '{"result":"success", "message":"Vote question successfully!"}';
    die;
  }
  
  public function addreportAction()
  {
  	
  	if(!$this->_helper->requireUser()->isValid()) return;
	
	  	$user_id = $this->_helper->api()->user()->getViewer()->getIdentity();
	  	$qid = $this->getRequest()->getParam('qid');
	  	$objReport = new Questionanswer_Model_Report(array());        
	    $this->view->form = $form = new Questionanswer_Form_Index_AddReport();
	    $id = '0';
	    // Posting form
	    if( $this->getRequest()->isPost() )
	    {
	      if( $form->isValid($this->getRequest()->getPost()) )
	      {
	        $data_array = $form->getValues();
	        $url = $data_array['qid'];
	        $date_updated = date('Y-m-d H:i:s');
	        $objReport->addReports($user_id, $data_array['type'], $data_array['content'], $url, $date_updated);
	      }
	      
	      $this->_forward('success', 'utility', 'core', array(
	      'smoothboxClose' => true,
	      'parentRefresh' => true,
	      'format'=> 'smoothbox',
	      'messages' => array('Sent report successful!.')
	      ));
	  	}
	  	else
	  	{
	  		foreach( $form->getElements() as $name => $element )
		    {   
		       if($name == "qid")
		       {
		          $element->setValue($qid);
		       }
		    }		    
	  	}
  	
  }
   
}

function make_page($total_items, $items_per_page, $p)
{
    if( !$items_per_page ) $items_per_page = 1;
       $maxpage = ceil($total_items / $items_per_page);
    if( $maxpage <= 0 ) $maxpage = 1;
       $p = ( ($p > $maxpage) ? $maxpage : ( ($p < 1) ? 1 : $p ) );
       $start = ($p - 1) * $items_per_page;
    return array($start, $p, $maxpage);
}