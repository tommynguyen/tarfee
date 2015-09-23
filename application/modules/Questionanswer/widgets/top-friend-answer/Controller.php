<?php

class Questionanswer_Widget_TopFriendAnswerController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  	
  	$objAnswer = new Questionanswer_Model_Answer(array());
    //get user id
     $viewer = Engine_Api::_()->user()->getViewer();
     $this->view->user_id = $viewer->getIdentity();  
	 
     $limit = $this->_getParam('max'); 
     if(!is_numeric($limit) || $limit <= 0)
     	$limit = 5;
     $topFriendAnswers = $objAnswer->getTopFriendAnswers($limit);
     	
     $topFriendAnswers_array = array();
     if(is_array($topFriendAnswers) && count($topFriendAnswers) > 0)
     {  
     	foreach($topFriendAnswers as $topFriendAnswer)
     	{
     		$auser = null;
          	$table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()
		        ->where('user_id = ?', $topFriendAnswer['user_id']);
		    $auser = $table->fetchRow($select);
              	  
            $topFriendAnswer['user_photo'] = $this->view->htmlLink($auser, $this->view->itemPhoto($auser, 'thumb.icon', $auser->getTitle(), array('style' => 'float:left')), array('class' => 'topanswers_thumb'));
            $topFriendAnswer['user_link'] = $this->view->htmlLink($auser->getHref(), $auser->getTitle());
            $topFriendAnswers_array[] = $topFriendAnswer;        
     	}
     }else{
     	return $this->setNoRender();
     }
     
     $this->view->topFriendAnswers = $topFriendAnswers_array;     
  }
}
?>