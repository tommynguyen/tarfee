<?php
class Questionanswer_Model_Questionvotes extends Core_Model_Item_Abstract
{ 
  
  public function getQuestionVotesByUserIdAndQuestionId($user_id, $question_id)
  {
  	$table  = Engine_Api::_()->getDbTable('questionvotes', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->where('user_id = ?', $user_id)
    		 		->where('question_id = ?', $question_id);
    if(count($table->fetchRow($select))>0)
    	return 1;
    else
    	return 0;    
  }
  
  public function addQuestionVotes($user_id, $question_id)
  {
  	$table = Engine_Api::_()->getDbTable('questionvotes', 'Questionanswer'); 
  	$data = array(
  	 	'user_id' => $user_id,
  	 	'question_id'  => $question_id  	 	
  	 );
  	 
  	 $table->insert($data);
  }  
  
  public function deleteQuestionVotes($user_id, $question_id)
  {  	
  	//delete question votes
  	 $table = Engine_Api::_()->getDbTable('questionvotes', 'Questionanswer');
	 $where = $table->getAdapter()->quoteInto('user_id = ? AND question_id = ?',$user_id, $question_id);
	 $table->delete($where);
  	
  }
}