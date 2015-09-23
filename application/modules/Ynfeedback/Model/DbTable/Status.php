<?php
class Ynfeedback_Model_DbTable_Status extends Engine_Db_Table {
    protected $_rowClass = 'Ynfeedback_Model_Status';
    protected $_name = 'ynfeedback_status';
    
    public function getStatusList() {
        $list = array(
        	'0' => Zend_Registry::get("Zend_Translate")->_('All')
        );
        $datas = $this->fetchAll();
        foreach ($datas as $data) {
            $list[$data->getIdentity()] = Zend_Registry::get("Zend_Translate")->_($data->title);
        }
        return $list;
    }
    
   	public function getStatusAssoc()
   	{
   		return $this->getStatusList();
   	}
	
	public function getStatusLabel($statusID)
	{
		$select = $this -> select() -> where('status_id = ?', $statusID) -> limit(1);
		$row = $this -> fetchRow($select);
		if($row)
			return $row -> title;
		else
			return null;
	}
	
	public function getStatusColor($statusID)
	{
		$select = $this -> select() -> where('status_id = ?', $statusID) -> limit(1);
		$row = $this -> fetchRow($select);
		if($row)
			return $row -> color;
		else
			return null;
	}
}