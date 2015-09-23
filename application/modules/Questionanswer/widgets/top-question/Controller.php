<?php



class Questionanswer_Widget_TopQuestionController extends Engine_Content_Widget_Abstract

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

     $topQuestions = $objQuestion->getTopQuestions($limit);

     

     $topQuestion_array = array();

     if(is_array($topQuestions) && count($topQuestions) > 0)

     {  

     	foreach($topQuestions as $topQuestion)

     	{

     		$qsuser = null;

          	$table = Engine_Api::_()->getDbtable('users', 'user');

		    $select = $table->select()

		        ->where('user_id = ?', $topQuestion['user_id']);

		    $qsuser = $table->fetchRow($select);

              	  

            $topQuestion['user_photo'] = $this->view->htmlLink($qsuser, $this->view->itemPhoto($qsuser, 'thumb.icon', $qsuser->getTitle(), array('style' => 'float:left')), array('class' => 'topquestions_thumb'));

            $topQuestion['user_link'] = $this->view->htmlLink($qsuser->getHref(), $qsuser->getTitle());

            $topQuestion_array[] = $topQuestion;        

     	}

     }     

     

     $this->view->topQuestions = $topQuestion_array;     

  }

}

?>