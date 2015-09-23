<?php



class Questionanswer_Widget_NewFriendQuestionController extends Engine_Content_Widget_Abstract

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

     $newFriendQuestions = $objQuestion->getNewFriendQuestions($limit);


     $newFriendQuestion_array = array();

     if(is_array($newFriendQuestions) && count($newFriendQuestions) > 0)

     {  

     	foreach($newFriendQuestions as $newFriendQuestion)

     	{

     		$qsuser = null;

          	$table = Engine_Api::_()->getDbtable('users', 'user');

		    $select = $table->select()

		        ->where('user_id = ?', $newFriendQuestion['user_id']);

		    $qsuser = $table->fetchRow($select);

              	  

            $newFriendQuestion['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('style' => 'float:left')), array('class' => 'topquestions_thumb'));

            $newFriendQuestion['user_link'] = $this->view->htmlLink($qsuser->getHref(), $qsuser->getTitle());

            $newFriendQuestion_array[] = $newFriendQuestion;        

     	}

     }else{

     	return $this->setNoRender();

     }   

     

     $this->view->newFriendQuestions = $newFriendQuestion_array;     

  }

}

?>