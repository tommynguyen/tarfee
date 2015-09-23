<?php

class Ynadvsearch_Model_DbTable_Keywords extends Engine_Db_Table {
    protected $_rowClass = 'Ynadvsearch_Model_Keyword';
	
	public function getKeywordsAssoc($params = array()) {
		$select = $this->select();
		if (!empty($params['ids'])) {
			$select->where('keyword_id IN (?)', $params['ids']);
		}
		$result = array();
		$rows = $this->fetchAll($select);
		foreach ($rows as $row) {
			$result[] = array('id'=>$row->getIdentity(), 'name'=>$row->title);
		}
		return $result;
	}
	
	public function addKeyword($query = '', $count = true) {
		if (empty($query)) return 0;
		$select = $this->select()->where('title = ?', $query);
        $keyword = $this->fetchRow($select);
		if ($keyword) {
            $now = new DateTime();
            $keyword->modified_date = $now->format('Y-m-d H:i:s');
        }
        else {
            $keyword = $table->createRow();
            $keyword->title = $query;
        }
		if ($count)
			$keyword->count++;
        $keyword->save();
		return $keyword->getIdentity();
	}
}
