<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ProfileCompleteness_Model_DbTable_Weights extends Engine_Db_Table
{
	public function getGlobalWeight($field_id)
	{
		$select = $this -> select();
        $select -> where('type_id = 0')
				-> where("field_id = ?", $field_id)
				-> limit(1);
        $row = $this->fetchRow($select);
		if($row)
			return $row -> weight;
		else {
			return 0;
		} 
	}
	public function setGlobalWeight($field_id, $weight)
	{
		$select = $this -> select();
        $select -> where('type_id = 0')
				-> where("field_id = ?", $field_id)
				-> limit(1);
        $row = $this->fetchRow($select);
		if($row)
		{
			$row -> weight = $weight;
			$row -> save();
		}
	}
	public function isSportLikeOrFollow()
	{
		return FALSE;
	}
	public function isClubFollow()
	{
		$table = Engine_Api::_()->getDbTable('follow', 'advgroup');
		$viewer = Engine_Api::_()->user()->getViewer();
		$select = $table -> select() -> where("user_id = ?", $viewer -> getIdentity()) ->where("follow=?", 1) -> limit(1);
		return $table -> fetchRow($select);
	}
	public function isVideoUpload()
	{
		$table = Engine_Api::_() -> getItemTable("video");
		$viewer = Engine_Api::_()->user()->getViewer();
		$select = $table -> select() -> where("owner_id = ?", $viewer -> getIdentity()) -> limit(1);
		return $table -> fetchRow($select);
	}
}
?>
