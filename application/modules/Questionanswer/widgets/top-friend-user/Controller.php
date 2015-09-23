<?php

class Questionanswer_Widget_TopFriendUserController extends Engine_Content_Widget_Abstract
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
     $topUsers = $objQuestion->getTopFriendUsers($limit, 1);
     
     $topUser_array = array();
     if(is_array($topUsers) && count($topUsers) > 0)
     {  
     	foreach($topUsers as $topUser)
     	{
     		$qsuser = null;
          	$table = Engine_Api::_()->getDbtable('users', 'user');
		    $select = $table->select()
		        ->where('user_id = ?', $topUser['user_id']);
		    $qsuser = $table->fetchRow($select);
              	  
            $topUser['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('style' => 'float:left')), array('class' => 'topusers_thumb'));
            $topUser['user_link'] = $this->view->htmlLink($qsuser->getHref(), $qsuser->getTitle());
            $topUser_array[] = $topUser;        
     	}
     }     
     
     $this->view->topUsers = $topUser_array;     
  }
}
?>