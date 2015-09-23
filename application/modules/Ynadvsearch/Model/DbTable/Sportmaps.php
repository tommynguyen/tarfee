<?php

class Ynadvsearch_Model_DbTable_Sportmaps extends Engine_Db_Table {
	public function removeItem($itemType, $itemId) {
		$where = array(
			$this->getAdapter()->quoteInto('item_type = ?', $itemType),
			$this->getAdapter()->quoteInto('item_id = ?', $itemId)
		);
		$this->delete($where);
	}
	
	public function updateItem($item) {
		$this->removeItem($item->getType(), $item->getIdentity());
		$sport = $item->getSportId();
		if (!empty($sport)) {
			if (is_numeric($sport)) {
				$sportmap = $this->createRow();
				$sportmap->item_type = $item->getType();
				$sportmap->item_id = $item->getIdentity();
				$sportmap->sport_id = $sport;
				$sportmap->save();
			}
			else if(is_array($sport)) {
				foreach ($sport as $ele) {
					if (is_numeric($ele)) {
						$sportmap = $this->createRow();
						$sportmap->item_type = $item->getType();
						$sportmap->item_id = $item->getIdentity();
						$sportmap->sport_id = $ele;
						$sportmap->save();
					}
				}
			}
		}
	}	
}
