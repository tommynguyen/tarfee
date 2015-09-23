<?php

class Ynbanmem_Model_DbTable_Bannedemails extends Engine_Db_Table {

    protected $_name = 'core_bannedemails';

    public function getAllBannedEmails() {

        $bannedEmailList = array();
        $extraInfoTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
        $userTable = Engine_Api::_()->getDBTable('users', 'user');
        $bannedEmails = $this->select()
                ->from($this, array('banned_id' => 'bannedemail_id', 'email'))
                ->query()
                ->fetchAll();

        if (count($bannedEmails) != 0) {
            
            foreach ($bannedEmails as $bannedEmail) {

                $extraInfo = $extraInfoTable->getExtraInfo($bannedEmail['banned_id'], 2);

                $user = $userTable->select()
                        ->where('email = ?', $bannedEmail['email'])
                        ->query()
                        ->fetchAll();
                if (count($user) != 0) {
                    $bannedEmail['user'] = $user;
                    $bannedEmail['extra_info'] = $extraInfo;
                    $bannedEmailList[] = $bannedEmail;
                    

                }
            }
            
            
        }
        return $bannedEmailList;
    }

    
    public function getBannedEmailByEmail($email) {

        $result;
        $extraInfoTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
        $userTable = Engine_Api::_()->getDBTable('users', 'user');
        $bannedEmails = $this->select()
                ->from($this, array('banned_id' => 'bannedemail_id', 'email'))
                ->where('email = ?', $email)
                ->query()
                ->fetchAll();

       // if (count($bannedEmails) != 0) 
            {

            foreach ($bannedEmails as $bannedEmail) {

                $extraInfo = $extraInfoTable->getExtraInfo($bannedEmail['banned_id'], 2);

                $user = $userTable->select()
                        ->where('email = ?', $bannedEmail['email'])
                        ->query()
                        ->fetchAll();
                //if (count($extraInfo) != 0 && count($user) != 0) {
                    if ( count($user) != 0) {
                    $bannedEmail['user'] = $user;
                    $bannedEmail['extra_info'] = $extraInfo;
                    $result = $bannedEmail;
                }
                break;
            }
            return $result;
        }
    }
    public function setEmails($emails, $info) {

        $extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');
        $emails = array_unique(array_map('strtolower', array_values($emails)));
        $usersTable = Engine_Api::_()->getDbTable('users', 'user');

        $data = $this->select()
                ->from($this, 'email')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        // ensure that each email is trimmed
        $data = !empty($data) ? array_map('trim', $data) : array();
        $emails = !empty($emails) ? array_map('trim', $emails) : array();

        // New emails
        $newEmails = array_diff($emails, $data);
        foreach ($newEmails as $newEmail) {
            $user = $usersTable->select()
                    ->where('email = ?', $newEmail)
                    ->query()
                    ->fetchAll();

            if (count($user) != 0) {
                $banned_id = $this->insert(array(
                    'email' => $newEmail,
                        ));
				$recipient = Engine_Api::_()->getItem('user',$user[0]['user_id']);
				
                $extraInfoTable->addExtraInfo($banned_id, $info);
                unset($banned_id);
                $curUser = Engine_Api::_()->getItem('user', $info['admin']);
                 if (count($curUser) != 0){
                    // Send mail
                   
                    $mailParams = array(
                      'host' => $_SERVER['HTTP_HOST'],
                      'email' => $newEmail,
					  'recipient_title'=>$recipient->getTitle(),
                      'date' => time(),
                      'sender_email' => $curUser->email,
                      'sender_title' => $curUser->getTitle(),
                      'message' => $info['email_message'],
                    );
					//print_r($mailParams);die;
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                      $newEmail,
                      'ban',
                      $mailParams
                    );
                 }

            }
        }


        return $this;
    }

    public function unBanEmails($ids) {

        if (!empty($ids)) {
            $this->delete(array(
                'bannedemail_id IN(?)' => $ids,
            ));

            $extraTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
            $extraTable->delete(array(
                'banned_id IN(?)' => $ids,
                'banned_type = 0'
            ));
        }

        return $this;
    }
    
     public function unBanEmail($id) {

        
		 
                $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
                $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');
        if (!empty($id)) {
           $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $id)
                                ->where('banned_type = ?', 2)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {

                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $id,
                                'banned_type = ?' => 2
                            ));
                        }
                        $bannedEmailsTable->delete(array('bannedemail_id = ?' => $id));
        }

        return $this;
    }
public function isEmailBanned($email)
  {
    $email = trim($email);
    
    $data = $this->select()
        ->from($this, 'email')
        ->query()
        ->fetchAll(Zend_Db::FETCH_COLUMN);

    $isBanned = false;

    foreach( $data as $test ) {
      if( false === strpos($test, '*') ) {
        if( strtolower($email) == $test ) {
          $isBanned = true;
          break;
        }
      } else if( $test[0] == '/' ) {
        if( @preg_match($test, $email) ) {
          $isBanned = true;
          break;
        }
      } else {
        $pregExpr = preg_quote($test, '/');
        $pregExpr = str_replace('\\*', '.*', $pregExpr);
        $pregExpr = '/^' . $pregExpr . '$/i';
        if( preg_match($pregExpr, $email) ) {
          $isBanned = true;
          break;
        }
      }
    }

    return $isBanned;
  }
}
