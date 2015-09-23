<?php

class Questionanswer_Model_Question extends Core_Model_Item_Abstract

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

  

  public function getQuestions($question_id='0', $user_id, $cat_id, $search, $record_per_page, $offset)

  { 

    $table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions as q", array("q.*", '(SELECT COUNT(*) FROM engine4_questionanswer_questionvotes AS v WHERE v.question_id = q.question_id) AS likes'))

    		 		->join('engine4_users as u', 'q.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))

    		 		->join('engine4_questionanswer_cats as c', 'q.cat_id = c.id', 'c.cat_name')

    		 		->order('q.question_id DESC')

                    ->limit($record_per_page, $offset);

    if(!empty($search))

      $select->where('q.content LIKE ?', $search);

    if (!empty($user_id))

      $select->where('q.user_id = ?', $user_id);

    if (!empty($cat_id))

      $select->where('q.cat_id = ?', $cat_id);

	if(!empty($question_id) && $question_id > 0)

      $select->where('q.question_id = ?', $question_id);

    return $table->fetchAll($select)->toArray();

  } 

  

  public function getTopUsers($limit, $cat_id)

  {

  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions", array("COUNT(engine4_questionanswer_questions.user_id) as TopUser", 'engine4_questionanswer_questions.user_id'))

    		 		->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username'))

    		 		->join('engine4_questionanswer_cats as c', 'engine4_questionanswer_questions.cat_id = c.id', '')

    		 		->group('engine4_questionanswer_questions.user_id')

    		 		->order('TopUser DESC')

    		 		->order('question_id DESC')

                    ->limit($limit);

   if (!empty($cat_id))

      $select->where('engine4_questionanswer_questions.cat_id = ?', $cat_id);

    return $table->fetchAll($select)->toArray();

  }

public function getTopFriendUsers($limit, $cat_id)

  {

	
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
  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions", array("COUNT(engine4_questionanswer_questions.user_id) as TopUser", 'engine4_questionanswer_questions.user_id'))

    		 		->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username'))

    		 		->join('engine4_questionanswer_cats as c', 'engine4_questionanswer_questions.cat_id = c.id', '')
					
					->having("engine4_questionanswer_questions.user_id IN ($strIds)")
    		 		
					->group('engine4_questionanswer_questions.user_id')

    		 		->order('TopUser DESC')

    		 		->order('question_id DESC')

                    ->limit($limit);

   if (!empty($cat_id))

      $select->where('engine4_questionanswer_questions.cat_id = ?', $cat_id);

    return $table->fetchAll($select)->toArray();

  }


  public function getTopQuestions($limit)

  {

  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions", array(" DISTINCT(engine4_questionanswer_questions.user_id), engine4_questionanswer_questions.question_id", "engine4_questionanswer_questions.content", "engine4_questionanswer_questions.date_created", "engine4_questionanswer_questions.user_id","(SELECT COUNT(*) FROM engine4_questionanswer_questionvotes AS v WHERE v.question_id = engine4_questionanswer_questions.question_id) AS vote"))

    		 		->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))

    		 		->joinRight('engine4_questionanswer_questionvotes as v', 'engine4_questionanswer_questions.question_id = v.question_id', NULL)

    		 		->order('vote DESC')

    		 		->order('question_id DESC')

                    ->limit($limit);

    return $table->fetchAll($select)->toArray();

  }

  public function getNewQuestions($limit)

  {

  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions", array(" DISTINCT(engine4_questionanswer_questions.user_id), engine4_questionanswer_questions.question_id", "engine4_questionanswer_questions.content", "engine4_questionanswer_questions.date_created", "engine4_questionanswer_questions.user_id"))

    		 		->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))

    		 		->order('question_id DESC')

                    ->limit($limit);

    return $table->fetchAll($select)->toArray();

  }

  

  public function getQuestionById($id)

  {

  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->where('question_id = ?', $id);

    return $table->fetchRow($select)->toArray();

  }

  

  public function addQuestion($user_id, $cat_id, $content, $date_created)

  {

  	$table = Engine_Api::_()->getDbTable('questions', 'Questionanswer'); 

  	$data = array(

  	 	'user_id' => $user_id,

  	 	'cat_id'  => $cat_id,

  	 	'content' => $content,

  	 	'date_created'  => $date_created

  	 );

  	 

  	 $table->insert($data);	 

	 return $this;

  }

  

  public function updateQuestion($id, $content, $date_updated)

  {

  	$table = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

  	$data = array(

  		'content' => $content,

  		'date_created' => $date_updated

  	);

  	

  	$where = $table->getAdapter()->quoteInto('question_id = ?', $id);

  	$table->update($data, $where);  

  }

  

  public function updateNumberAnswer($id)

  {

  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

    				->setIntegrityCheck(false);

    		 $select->from("engine4_questionanswer_questions", array('engine4_questionanswer_questions.answers'))

    		 		->where('engine4_questionanswer_questions.question_id = ?', $id);

    $question = $table->fetchRow($select)->toArray();

  			 

  	$table = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

  	$data = array(

  		'answers' => $question['answers'] + 1

  	);

  	

  	$where = $table->getAdapter()->quoteInto('question_id = ?', $id);

  	$table->update($data, $where);  	

  }

  

  public function countQuestion($question_id='0', $user_id, $cat_id, $search)

  {

    $table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)

                    ->setIntegrityCheck(false);

             $select->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))

                     ->join('engine4_questionanswer_cats as c', 'engine4_questionanswer_questions.cat_id = c.id')

                     ->order('engine4_questionanswer_questions.question_id DESC');

     if(!empty($search))

      $select->where('engine4_questionanswer_questions.content LIKE ?', $search);                   

    if (!empty($user_id) && $user_id>0)

      $select->where('engine4_questionanswer_questions.user_id = ?', $user_id);

    if (!empty($cat_id) && $cat_id>0)

      $select->where('engine4_questionanswer_questions.cat_id = ?', $cat_id);    

	if(!empty($question_id) && $question_id > 0)

      $select->where('engine4_questionanswer_questions.question_id = ?', $question_id);

    return count($table->fetchAll($select)->toArray()); 

  }

  

  public function deleteQuestion($question_id)

  {

  	//delete answers

  	Questionanswer_Model_Answer::deleteAnswerByQuestion($question_id);

  	//delete question

  	 $table = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

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

  

	public function getTopFriendQuestions($limit)

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

			

		  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

		    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

		    				->setIntegrityCheck(false);

		    		 $select->from("engine4_questionanswer_questions", array(" DISTINCT(engine4_questionanswer_questions.user_id)", "engine4_questionanswer_questions.date_created"))

		    		 		->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))
							->joinRight('engine4_questionanswer_questionvotes as v', 'engine4_questionanswer_questions.question_id = v.question_id', NULL)
		    		 		->having("engine4_questionanswer_questions.user_id IN ($strIds)")
							->order('engine4_questionanswer_questions.date_created DESC');
		                    
			$friends = $table->fetchAll($select)->toArray();
		    $tmpFriends = array(); 
			$checkarr = array();
			foreach($friends as $friend)
			{			
						$flag = true;
						foreach($checkarr as $check)
						{
							if($check['user_id'] == $friend['user_id'])
								$flag = false;
						}
						$checkarr[] = $friend;
						if($flag == true && count($tmpFriends) < $limit)
						{
							$select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
									->from("engine4_questionanswer_questions",  array("engine4_questionanswer_questions.question_id", "engine4_questionanswer_questions.content", "engine4_questionanswer_questions.date_created", "(SELECT COUNT(*) FROM engine4_questionanswer_questionvotes AS v WHERE v.question_id = engine4_questionanswer_questions.question_id) AS vote"))
									->where('engine4_questionanswer_questions.user_id = ?',$friend['user_id'])
									->order('vote DESC')
	    		 					->order('question_id DESC')
									->limit(1);
							$questions = $table->fetchAll($select)->toArray();
							$friend['question_id'] = $questions[0]['question_id'];
							$friend['content'] = $questions[0]['content'];
							$friend['date_created'] = $questions[0]['date_created'];
							$friend['vote'] = $questions[0]['vote'];
							$tmpFriends[] = $friend;
						}
			}

		    return $tmpFriends;

  		}catch(Exception $ex){

			// Ignore this case

			return null;

		}

  	}

  	

	public function getNewFriendQuestions($limit)

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


		  	$table  = Engine_Api::_()->getDbTable('questions', 'Questionanswer');

		    $select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)

		    				->setIntegrityCheck(false);

		    		 $select->from("engine4_questionanswer_questions", array(" DISTINCT(engine4_questionanswer_questions.user_id)", "engine4_questionanswer_questions.date_created"))

		    		 		->order('engine4_questionanswer_questions.date_created DESC')
							
							->join('engine4_users as u', 'engine4_questionanswer_questions.user_id = u.user_id', array('u.displayname', 'u.username', 'u.photo_id'))

		    		 		->having("engine4_questionanswer_questions.user_id IN ($strIds)");

		                   
			$friends = $table->fetchAll($select)->toArray();

			$tmpFriends = array(); 
			$checkarr = array();
			foreach($friends as $friend)
			{			
						$flag = true;
						foreach($checkarr as $check)
						{
							if($check['user_id'] == $friend['user_id'])
								$flag = false;
						}
						$checkarr[] = $friend;
						if($flag == true && count($tmpFriends) < $limit)
						{
							$select = $table->select(Zend_Db_Table::SELECT_WITHOUT_FROM_PART)
									->from("engine4_questionanswer_questions",  array("engine4_questionanswer_questions.question_id", "engine4_questionanswer_questions.content", "engine4_questionanswer_questions.date_created", "(SELECT COUNT(*) FROM engine4_questionanswer_questionvotes AS v WHERE v.question_id = engine4_questionanswer_questions.question_id) AS vote"))
									->where('engine4_questionanswer_questions.user_id = ?',$friend['user_id'])
									->order('engine4_questionanswer_questions.date_created DESC')
									->limit(1);
							$questions = $table->fetchAll($select)->toArray();
							$friend['question_id'] = $questions[0]['question_id'];
							$friend['content'] = $questions[0]['content'];
							$friend['date_created'] = $questions[0]['date_created'];
							$friend['vote'] = $questions[0]['vote'];
							$tmpFriends[] = $friend;
						}
			}
 
		    return $tmpFriends;

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