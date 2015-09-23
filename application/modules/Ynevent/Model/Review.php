<?php
/**
 * YouNet Company
*
* @category   Application_Extensions
* @package    Ynevent
* @author     YouNet Company
*/

class Ynevent_Model_Review extends Core_Model_Item_Abstract {
	
	public function getLastReport()
	{
		$table = Engine_Api::_()->getItemTable('ynevent_reviewreport');
		$select = $table->select() -> where('review_id = ?', $this->review_id) ->order('report_id DESC')->limit(1);
		$row = $table->fetchRow($select);
		if($row)
			return $row->creation_date;
		
	}
	protected function _delete() 
	{
		$table = Engine_Api::_()->getItemTable('ynevent_reviewreport');
		$select = $table->select() -> where('review_id = ?', $this->review_id);
		$rows = $table->fetchAll($select);
		foreach($rows as $row)
		{
			$row->delete();
		}
	}
	public function isUserReported($user)
	{
		if (!$user)
			return false;
		$reportTbl = Engine_Api::_()->getItemTable('ynevent_reviewreport');
		$selec = $reportTbl->select()
			->where("user_id = ?", $user->getIdentity())
			->where("review_id = ?", $this->getIdentity());
		if (count($reportTbl->fetchAll($selec)))
			return true;
		else
			return false;
	}
	
}