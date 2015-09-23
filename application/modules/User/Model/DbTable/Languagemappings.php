<?php
class User_Model_DbTable_Languagemappings extends Engine_Db_Table
{
  	protected $_name = 'user_languagemappings';
	
	public function save($language_id, $item) 
	{
		// check exist
		$select = $this -> select() 
						-> where('language_id = ?', $language_id)
						-> where('item_id = ?', $item -> getIdentity()) 
						-> where('item_type = ?', $item -> getType());
		$row = $this -> fetchRow($select);
		if(!$row)
		{
			$row = $this -> createRow();
			$row -> language_id = $language_id;
			$row -> item_id = $item -> getIdentity();
			$row -> item_type = $item -> getType();
			$row -> save();
		}
	}
	
	public function getLanguageIds($item) {
		$select = $this -> select() 
						-> where('item_id = ?', $item -> getIdentity())
						-> where('item_type = ?', $item -> getType());
		$arrIds = array();
		foreach($this -> fetchAll($select) as $mappingRow) {
			$arrIds[] = $mappingRow -> language_id;
		}
		return $arrIds;
	}
	
}