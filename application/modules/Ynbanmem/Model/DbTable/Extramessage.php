<?php

class Ynbanmem_Model_DbTable_ExtraMessage extends Engine_Db_Table {

    
    public function addExtraMessage($message_id, $info) {
        
        $exists = (bool) $this->select()
                        ->from($this, new Zend_Db_Expr('TRUE'))
                        ->where('message_id = ?', $bannedUserId)
                        ->query()
                        ->fetch(Zend_Db::FETCH_COLUMN);
      if($info['from'] == 1)
		{
			$sender = '';
		}
		else
		{
			$sender = '';
		}
		if($info['type'] == 2)
		{
			$reason = $info['reason'];
		}
		else
		{
			$reason = 'NULL';
		}
		
        if (!$exists) {
            $this->insert(array(
                'message_id' => $message_id,
                'sender_email' => $sender,
                'type'=>$info['type'],
                'email_type' => $info['from'],
                'reason' => $reason
            ));
        }
        return $this;
    }

    public function addExtraMessages($messageIds, $info) {
        $bannedUsernameTable = Engine_Api::_()->getDbTable('bannedusernames', 'core');
        foreach ($bannedUserIds as $bannedUserId) {
         $exists = (bool) $this->select()
                        ->from($this, new Zend_Db_Expr('TRUE'))
                        ->where('message_id = ?', $bannedUserId)
                        ->query()
                        ->fetch(Zend_Db::FETCH_COLUMN);
        
        if (!$exists) {
            $this->insert(array(
                'message_id' => $message_id,
                'sender_email' => $info['sender_email'],
                'type'=>$info['type'],
                'email_type' => $info['expiry_date'],
                'reason' => $info['reason']
            ));
        }
        }
        return $this;
    }
    public function getExtraMessage($id) {
        
            return $this->select()
                    ->where('message_id = ?', $id)
                    ->query()
                    ->fetchAll();
            
    }
}

?>
