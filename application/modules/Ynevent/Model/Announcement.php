<?php
class Ynevent_Model_Announcement extends Core_Model_Item_Abstract {
	protected $_parent_type = 'user';

	protected $_owner_type = 'user';

	public function getHref($params = array()) {
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'default', true);
	}

	protected function _update() {
		parent::_update();
	}

	public function setProfile() {
		$table = Engine_Api::_() -> getDbtable('announcements', 'ynevent');
		$where = $table -> getAdapter() -> quoteInto('event_id = ?', $this -> event_id);
		$table -> update(array('highlight' => 0, ), $where);
		$this -> highlight = !$this -> highlight;
		$this -> save();
	}
	
	public function setUnHighlight()
	{
		if($this->highlight)
		{
			$table = Engine_Api::_() -> getDbtable('announcements', 'ynevent');
			$select = $this -> select() -> where('event_id = ?', $this->event_id) -> where("announcement_id != ? ", $this->announcement_id) ->where("highlight = 1 ")->limit(1);
			$row = $table->fetchRow($select);
			if($row)
			{
				$row -> highlight = 0;
				$row -> save();
			}		
		}		
	}

}
