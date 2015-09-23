<?php

class Questionanswer_Widget_NewFriendAnswerController extends Engine_Content_Widget_Abstract
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
     $newFriendAnswers = $objAnswer->getNewFriendAnswers($limit);
     	
     $newFriendAnswers_array = array();
     if(is_array($newFriendAnswers) && count($newFriendAnswers) > 0)
     {  
     	foreach($newFriendAnswers as $newFriendAnswer)
     	{
     		$auser = null;
          	$table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()
		        ->where('user_id = ?', $newFriendAnswer['user_id']);
		    $auser = $table->fetchRow($select);
              	  
            $newFriendAnswer['user_photo'] = $this->view->htmlLink($auser, $this->view->itemPhoto($auser, 'thumb.icon', $auser->getTitle(), array('style' => 'float:left')), array('class' => 'topanswers_thumb'));
            $newFriendAnswer['user_link'] = $this->view->htmlLink($auser->getHref(), $auser->getTitle());
            $newFriendAnswers_array[] = $newFriendAnswer;        
     	}
     }else{
     	return $this->setNoRender();
     }
     
     $this->view->newFriendAnswers = $newFriendAnswers_array;     
  }
}
?>