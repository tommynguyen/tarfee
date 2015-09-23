<?php

class Ynevent_Model_DbTable_Agents extends Engine_Db_Table
{
    protected $_name = 'event_agents';
    protected $_rowClass = "Ynevent_Model_Agent";

    public function getUserAgentSelect($user)
    {
        $select = $this -> select();
        $select -> where("user_id = ?", $user -> getIdentity());
        return $select;
    }
    
	public function getUserAgents($user)
    {
        $select = $this -> select();
        $select -> where("user_id = ?", $user -> getIdentity());
        return $this->fetchAll($select);
    }

}
?>
