<?php

class Ynevent_Model_DbTable_Follow extends Engine_Db_Table {

    protected $_type = 'event';
    protected $_name = 'event_follow';

    public function getFollowEvent($event_id, $uid) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where('resource_id=?', $event_id);

        return $this->fetchRow($select);
    }

    public function getFollowEvents($uid) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where("follow=?", 1);
        return $this->fetchAll($select);
    }

    public function getUserFollow($event_id) {
        $select = $this->select();
        $select->where('resource_id=?', $event_id)
                ->where("follow=?", 1);

        return $this->fetchAll($select);
    }

    public function setOptionFollowEvent($event_id, $uid, $option) {
        $select = $this->select();
        $select->where("user_id=?", $uid)
                ->where('resource_id=?', $event_id);

        $row = $this->fetchRow($select);
        if ($row) {
            $row->follow = $option;
            $row->save();
        }
    }

}