<?php

class Questionanswer_Widget_TopAnswerController extends Engine_Content_Widget_Abstract
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
     $topAnswers = $objAnswer->getTopAnswers($limit);
     	
     $topAnswers_array = array();
     if(is_array($topAnswers) && count($topAnswers) > 0)
     {  
     	foreach($topAnswers as $topAnswer)
     	{
     		$auser = null;
          	$table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()
		        ->where('user_id = ?', $topAnswer['user_id']);
		    $auser = $table->fetchRow($select);
              	  
            $topAnswer['user_photo'] = $this->view->htmlLink($auser, $this->view->itemPhoto($auser, 'thumb.icon', $auser->getTitle(), array('style' => 'float:left')), array('class' => 'topanswers_thumb'));
            $topAnswer['user_link'] = $this->view->htmlLink($auser->getHref(), $auser->getTitle());
            $topAnswers_array[] = $topAnswer;        
     	}
     }     
     
     $this->view->topAnswers = $topAnswers_array;     
  }
}
?>