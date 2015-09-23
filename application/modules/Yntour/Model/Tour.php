<?php

class Yntour_Model_Tour extends Core_Model_Item_Abstract
{

    protected function _postDelete()
    {
        parent::_postDelete();
        $model = new Yntour_Model_DbTable_Touritems;
        $select = $model -> select() -> where('tour_id=?', $this -> getIdentity());
        foreach ($model->fetchAll($select) as $row)
        {
            $row -> delete();
        }
    }

    public function setPath($path)
    {
        $this -> path = $path;
        $this -> path_hash = sha1($path, false);
    }

}
