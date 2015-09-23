<?php

class Questionanswer_Widget_TopFriendQuestionController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$objQuestion = new Questionanswer_Model_Question(array());
  	
    //get user id
     $viewer = Engine_Api::_()->user()->getViewer();
     $this->view->user_id = $viewer->getIdentity();  
	 
     $limit = $this->_getParam('max'); 
     if(!is_numeric($limit) || $limit <= 0)
     	$limit = 5;
     $topFriendQuestions = $objQuestion->getTopFriendQuestions($limit);
     
     $topFriendQuestion_array = array();
     if(is_array($topFriendQuestions) && count($topFriendQuestions) > 0)
     {  
     	foreach($topFriendQuestions as $topFriendQuestion)
     	{
     		$qsuser = null;
          	$table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()
		        ->where('user_id = ?', $topFriendQuestion['user_id']);
		    $qsuser = $table->fetchRow($select);
              	  
            $topFriendQuestion['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('style' => 'float:left')), array('class' => 'topquestions_thumb'));
            $topFriendQuestion['user_link'] = $this->view->htmlLink($qsuser->getHref(), $qsuser->getTitle());
            $topFriendQuestion_array[] = $topFriendQuestion;        
     	}
     }else{
     	return $this->setNoRender();
     }
     
     $this->view->topFriendQuestions = $topFriendQuestion_array;     
  }
}
?>