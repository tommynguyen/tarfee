<?php

class Ynevent_Model_Category extends Core_Model_Item_Abstract {

	protected $_searchTriggers = false;

	public function getUsedCount() {
		$eventTable = Engine_Api::_() -> getItemTable('event');
		return $eventTable -> select() -> from($eventTable, new Zend_Db_Expr('COUNT(event_id)')) -> where('category_id = ?', $this -> category_id) -> query() -> fetchColumn();
	}

	public function isOwner(Core_Model_Item_Abstract $owner) {
		return false;
	}

	public function getOwner($recurseType = NULL) {
		return $this;
	}

	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}

	public function delete() {
		return $this -> _table -> deleteNode($this);
	}

	/**
	 * get chilren node
	 *
	 */
	public function getChilren() {
		$table = $this -> _table;
		$select = $table -> select() -> where('parent_id = ?', $this -> getIdentity()) -> order('title asc');
		return $table -> fetchAll($select);
	}

	/**
	 * get chilren node
	 *
	 */
	public function countChildren() {
		$table = $this -> _table;
		$select = $table -> select() -> where('parent_id = ?', $this -> getIdentity());
		return count($table -> fetchAll($select));
	}

	public function getParentNode() {
		if (!$this -> parent_id) {
			return null;
		}
		return $this -> _table -> find($this -> parent_id) -> current();
	}

	public function getBreadCrumNode() {
		$result = array($this);
		$parent = $this -> getParentNode();
		while ($parent) {
			$result[] = $parent;
			$parent = $parent -> getParentNode();
		}
		return array_reverse($result);
	}

	public function getLevel() {
		return count($this -> getBreadCrumNode());	
	}

	public function getAllChildrens() {
		$table = $this -> _table;
		$select =  $table->select()->where('category_id in(?)', implode(',', $this->getAllChildrenIds()));
		return $table->fetchAll($select);
	}

	public function getAllChildrenIds($ids = NULL, $max = 8) {
		if ($ids == NULL) {
			$ids = array($this -> getIdentity());
		}
		
		$table = $this -> _table;
		$len = count($ids);

		for ($i = 0; $i < $max; ++$i) {
			$select = $table -> select() ->from('engine4_event_categories','category_id')-> where('parent_id IN (?)', $ids) -> orWhere('category_id IN(?)', $ids) ;
			
			$ids = $table->getAdapter()->fetchCol($select);
			
			if (count($ids) == $len) {
				break;
			}
			$len =  count($ids);
		}

		return $ids;
	}
}
