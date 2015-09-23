<?php
class Yncomment_Model_DbTable_Hide extends Engine_Db_Table {
    public function checkHideItem($comment)
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $select = $this -> select() 
            -> where('user_id  = ?', $viewer -> getIdentity()) 
            -> where("hide_resource_type = ?", $comment -> getType())
            -> where("hide_resource_id = ?", $comment -> getIdentity())
            -> limit(1);
        return $this -> fetchRow($select);
    }
}
