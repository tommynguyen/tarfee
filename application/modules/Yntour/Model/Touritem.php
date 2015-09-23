<?php

class Yntour_Model_Touritem extends Core_Model_Item_Abstract
{
    public function setPriorityLast()
    {
        $model = new Yntour_Model_DbTable_Touritems;
        $db = $model -> getAdapter();
        $table = $model -> info('name');
        $sql = 'select count(*) from ' . $table . ' where tour_id=' . (int)$this -> tour_id;
        $this -> priority = (int)$db -> fetchOne($sql);
    }
    public function getLanguages()
    {
        $model_language = new Yntour_Model_DbTable_Itemlanguages; 
        $select = $model_language->select()->where('item_id = ?', $this->touritem_id);
        return $model_language->fetchAll($select);
    }
}