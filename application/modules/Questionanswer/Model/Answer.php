<?php
class Questionanswer_Model_Answer extends Core_Model_Item_Abstract
{
  
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'questionanswer_special',
      'controller' => 'index',
      'action' => 'index',
      'id'=> $this->question_id,      
    ), $params);
    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }
  
  public function getAnswers($question_id, $RecordPerPage, $offset)
  {
    $table  = Engine_Api::_()->getDbtable('answers', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->join('engine4_users as u', 'engine4_questionanswer_answers.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))
    		 		->join('engine4_questionanswer_questions as q', 'engine4_questionanswer_answers.question_id = q.question_id', array())
    		 		->order('engine4_questionanswer_answers.answer_id DESC');

    if($question_id > 0)
    	$select->where('engine4_questionanswer_answers.question_id = ?', $question_id);
    if($RecordPerPage > 0)
    	$select->limit($RecordPerPage, $offset);
    
    return $table->fetchAll($select)->toArray();
  }  
  
  public function getTopAnswers($limit, $flagFriend = false)
  {
  	$table  = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->from("engine4_questionanswer_answers", array("COUNT(engine4_questionanswer_answers.user_id) as TopUser", 'engine4_questionanswer_answers.user_id'))
    		 		->join('engine4_users as u', 'engine4_questionanswer_answers.user_id = u.user_id', array('u.displayname', 'u.username'))
    		 		->group('engine4_questionanswer_answers.user_id')
    		 		->order('TopUser DESC')
    		 		->order('answer_id DESC')
                    ->limit($limit);
    return $table->fetchAll($select)->toArray();
  }
  
  public function getAnswerById($id)
  {
  	$table  = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->where('answer_id = ?', $id);
    return $table->fetchRow($select)->toArray();
  }
  
  public function addAnswer($user_id, $question_id, $content, $date_created)
  {  
  	$table = Engine_Api::_()->getDbTable('answers', 'Questionanswer'); 
  	$data = array(
  	 	'user_id' => $user_id,
  	 	'question_id'  => $question_id,
  	 	'content' => $content,
  	 	'date_created'  => $date_created
  	 );
  	 
  	 $table->insert($data);  
  	 
  	 //update number answer of question
  	 $objQuestion = new Questionanswer_Model_Question(array());
  	 $objQuestion->updateNumberAnswer($question_id);
	 
	 return $this;
  }
  
  public function updateAnswer($id, $content, $date_updated)
  {
  	$table = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
  	$data = array(
  		'content' => $content,
  		'date_created' => $date_updated
  	);
  	
  	$where = $table->getAdapter()->quoteInto('answer_id = ?', $id);
  	$table->update($data, $where);  
  }
  
  public function deleteAnswer($answer_id)
  {
  	//delete answers  	
  	$table = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
  	$where = $table->getAdapter()->quoteInto('answer_id = ?', $answer_id);
  	return $table->delete($where);
  }
  
  public function deleteAnswerByQuestion($question_id)
  {
  	//delete answers
  	$table = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
  	$where = $table->getAdapter()->quoteInto('question_id = ?', $question_id);
  	$table->delete($where);
  }
  
  
  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }  
  
  /**
   * Gets a proxy object for the subscribe handler
   *
   * @return Engine_ProxyObject
   **/
  public function subscribes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('subscribes', 'core'));
  }
  
  
  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
  
	public function getTopFriendAnswers($limit)
  	{
  		try{
			$viewer = Engine_Api::_()->user()->getViewer();
	     	if (empty($viewer->user_id))
	       	{
	           return null;
	       	}
	
			// Getting all friend Ids
			$ids = $this->getAllFriendIds($viewer->user_id);
	
			// Adding Id of current user
			$ids[] = $viewer->user_id;
			$strIds = implode(',', $ids);
			
		  	$table  = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
		    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
		    				->setIntegrityCheck(false);
		    		 $select->from("engine4_questionanswer_answers", array("COUNT(engine4_questionanswer_answers.user_id) as TopUser", 'engine4_questionanswer_answers.user_id'))
		    		 		->join('engine4_users as u', 'engine4_questionanswer_answers.user_id = u.user_id', array('u.displayname', 'u.username'))
		    		 		->group('engine4_questionanswer_answers.user_id')
		    		 		->order('TopUser DESC')
		    		 		->order('answer_id DESC')
		    		 		->having("engine4_questionanswer_answers.user_id IN ($strIds)")
		                    ->limit($limit);
		    return $table->fetchAll($select)->toArray();
  		}catch(Exception $ex){
			// Ignore this case
			return null;
		}
	}
  
	public function getNewFriendAnswers($limit)
  	{
  		try{
			$viewer = Engine_Api::_()->user()->getViewer();
	     	if (empty($viewer->user_id))
	       	{
	           return null;
	       	}
	
			// Getting all friend Ids
			$ids = $this->getAllFriendIds($viewer->user_id);
	
			// Adding Id of current user
			$ids[] = $viewer->user_id;
			$strIds = implode(',', $ids);
			
		  	$table  = Engine_Api::_()->getDbTable('answers', 'Questionanswer');
		    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
		    				->setIntegrityCheck(false);
		    		 $select->from("engine4_questionanswer_answers", array("COUNT(engine4_questionanswer_answers.user_id) as TopUser", 'engine4_questionanswer_answers.user_id', 'engine4_questionanswer_answers.date_created'))
		    		 		->join('engine4_users as u', 'engine4_questionanswer_answers.user_id = u.user_id', array('u.displayname', 'u.username'))
		    		 		->group('engine4_questionanswer_answers.user_id')
		    		 		->order('engine4_questionanswer_answers.date_created DESC')
		    		 		->having("engine4_questionanswer_answers.user_id IN ($strIds)")
		                    ->limit($limit);
		    return $table->fetchAll($select)->toArray();
  		}catch(Exception $ex){
			// Ignore this case
			return null;
		}
	}
	
	/**
	 * Get all friend ids based on id of the user
	 * 
	 * @param int $userId: Id of the user
	 */
	protected function getAllFriendIds($userId)
	{
		try{
	       	$ids = null;
	       	$membershipTable = Engine_Api::_()->getDbtable('membership', 'user');
	       	
	       	$friends = $membershipTable->fetchAll($membershipTable->select()->where('resource_id = ?', $userId)->where('active = ?', '1'));
	       	       	
	       	foreach($friends as $friend){
	       		$ids[] = $friend->user_id;
	       	}
	       	
	       	return $ids;
		}catch(Exception $ex){
			// Ignore this case
		}
		
		return null;
	}
}