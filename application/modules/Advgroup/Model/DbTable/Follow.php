<?php

class Advgroup_Model_DbTable_Follow extends Engine_Db_Table {

    protected $_type = 'group';
    protected $_name = 'group_follow';

    public function getFollowGroup($group_id, $uid) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where('resource_id=?', $group_id);

        return $this->fetchRow($select);
    }

    public function getFollowGroups($uid) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where("follow=?", 1);
        return $this->fetchAll($select);
    }

    public function getUserFollow($group_id) {
        $select = $this->select();
        $select->where('resource_id=?', $group_id)
                ->where("follow=?", 1);

        return $this->fetchAll($select);
    }

    public function setOptionFollowGroup($group_id, $uid, $option) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where('resource_id=?', $group_id);

        $row = $this->fetchRow($select);
        if ($row) {
            $row->follow = $option;
            $row->save();
        }
    }

}