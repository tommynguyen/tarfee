<?php

class Ynbanmem_Model_DbTable_ExtraInfo extends Engine_Db_Table {

    
    public function addExtraInfo($bannedUserId, $info) {
        
        $exists = $this->select()
                        ->from($this, new Zend_Db_Expr('TRUE'))
                        ->where('banned_id = ?', $bannedUserId)
                        ->where('banned_type = ?', $info['type'])
                        ->query()
                        ->fetchAll();
        
        if (count($exists)== 0)  {
            $this->insert(array(
                'banned_id' => $bannedUserId,
                'banned_type' => $info['type'],
                'admin'=>$info['admin'],
                'expiry_date' => $info['expiry_date'],
                'reason' => $info['reason'],
                'email' => $info['email_message']
            ));
        }
        return $this;
    }

    public function addExtraInfos($bannedUsernames, $info) {
        $bannedUsernameTable = Engine_Api::_()->getDbTable('bannedusernames', 'core');
        foreach ($bannedUserIds as $bannedUserId) {
            $banned_id = $bannedUsernameTable->select('id')
                    ->where('username IN (?)', $bannedUserId)
                    ->query()
                    ->fetchAll(Zend_Db::FETCH_COLUMN);
            $this->insert(array(
                'banned_id' => $banned_id,
                'banned_type' => 1,
                'expiry_date' => $info['expiry_date'],
                'reason' => $info['reason'],
                'email' => $info['email_message']
            ));
        }
        return $this;
    }
    public function getExtraInfo($bannedId, $type) {
        
            return $this->select()
                    ->where('banned_id = ?', $bannedId)
                    ->where('banned_type = ?', $type)
                    ->query()
                    ->fetchAll();
            
    }
}

?>
