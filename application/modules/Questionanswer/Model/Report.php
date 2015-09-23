<?php
class Questionanswer_Model_Report extends Core_Model_Item_Abstract
{ 
  
  public function getAllReports()
  {
  	$table  = Engine_Api::_()->getDbTable('reports', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->join('engine4_users as u', 'engine4_questionanswer_reports.user_id = u.user_id', array('u.displayname', 'u.username'))
    		  		->order('is_read DESC');
    		     		 		
    return $table->fetchAll($select)->toArray();    
  }
  
  public function getReportById($id)
  {
  	$table  = Engine_Api::_()->getDbTable('reports', 'Questionanswer');
    $select = $table->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
    				->setIntegrityCheck(false);
    		 $select->where('id = ?', $id);
    		     		 		
    return $table->fetchRow($select);    	    
  }
  
  public function addReports($user_id, $report_type, $content, $report_url, $posted_date)
  {
  	$table = Engine_Api::_()->getDbTable('reports', 'Questionanswer'); 
  	$data = array(
  	 	'user_id' => $user_id,
  	 	'report_type'  => $report_type,
  		'content'      =>  $content,
  		'report_url'   => $report_url,
  		'posted_date'  => 	$posted_date 	
  	 );
  	 
  	 $table->insert($data);
  }  
  
  public function deleteReport($id)
  {  	
  	//delete question votes
  	 $table = Engine_Api::_()->getDbTable('reports', 'Questionanswer');
	 $where = $table->getAdapter()->quoteInto('id = ?',$id);
	 $table->delete($where);
  	
  }
}