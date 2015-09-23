<?php

class Questionanswer_Widget_FullQuestionAnswerController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    //get user id
     $viewer = Engine_Api::_()->user()->getViewer();
     $this->view->user_id = $viewer->getIdentity();  
  	 $question_id = "";
     if(Engine_Api::_()->core()->hasSubject() )
     {
        $subject = Engine_Api::_()->core()->getSubject();
        $question_id = $subject->getIdentity();
     }
     if(empty($question_id) || $question_id <= 0)
     {
	 	if(isset($_GET['qid']))	$question_id = $_GET['qid']; else $question_id = "";
     }   	
  	 $this->view->qid = $question_id;	 
  }
}
?>