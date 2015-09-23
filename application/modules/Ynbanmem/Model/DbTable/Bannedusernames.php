<?php

class Ynbanmem_Model_DbTable_BannedUsernames extends Engine_Db_Table {

    protected $_name = 'core_bannedusernames';
    protected $user;
    protected $extra_info;

    //protected $_rowClass = "Ynbanmem_Model_BannedUsername";
    public function setBannedUsernames($usernames, $info) {

        $extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');
        $usernames = array_map('strtolower', array_values($usernames));
        $usersTable = Engine_Api::_()->getDbTable('users', 'user');

        $data = $this->select()
                ->from($this, 'username')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
        //$mergedata = array_merge_recursive($data, $usernames);
        // New emails
        $newUsernames = array_diff($usernames, $data);

        foreach ($newUsernames as $newUsername) {

            $user = $usersTable->select()
                    ->where('username = ?', $newUsername)
                    ->query()
                    ->fetchAll();

            if (count($user) != 0) {
                $banned_id = $this->insert(array(
                    'username' => $newUsername,
                        ));
				$recipient = Engine_Api::_()->getItem('user',$user[0]['user_id']);
			
                $extraInfoTable->addExtraInfo($banned_id, $info);
                unset($bannedIDs);
				$curUser = Engine_Api::_()->getItem('user', $info['admin']);
                 if (count($curUser) != 0){
                    // Send mail
                   
                    $mailParams = array(
                      'host' => $_SERVER['HTTP_HOST'],
                      'email' => $recipient->email,
					  'recipient_title'=>$recipient->getTitle(),
                      'date' => time(),
                      'sender_email' => $curUser->email,
                      'sender_title' => $curUser->getTitle(),
                      'message' => $info['email_message'],
                    );
					//print_r($mailParams);die;
                    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                      $recipient->email,
                      'ban',
                      $mailParams
                    );
                 }
            }
        }
        return $this;
    }

    public function getAllBannedUsers() {
        $bannedUserList = array();
        $extraInfoTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
        $userTable = Engine_Api::_()->getDBTable('users', 'user');
        $bannedUsers = $this->select()
                ->from($this, array('banned_id' => 'bannedusername_id', 'username'))
                ->query()
                ->fetchAll();

        foreach ($bannedUsers as $bannedUser) {

            $extraInfo = $extraInfoTable->getExtraInfo($bannedUser['banned_id'], 0);
            //print_r($extraInfo);die;
            $user = $userTable->select()
                    ->where('username = ?', $bannedUser['username'])
                    ->query()
                    ->fetchAll();

//            if (count($extraInfo) != 0 && count($user) != 0) 
            if (count($user) != 0) {
                $bannedUser['user'] = $user;
                $bannedUser['extra_info'] = $extraInfo;
                $bannedUserList[] = $bannedUser;
                if (count($curUser) != 0) {
                    // Send mail
                    $mailType = ( $inviteOnlySetting == 2 ? 'invite_code' : 'invite' );
                    $mailParams = array(
                        'host' => $_SERVER['HTTP_HOST'],
                        'email' => $newEmail,
                        'date' => time(),
                        'sender_email' => $curUser->email,
                        'sender_title' => $curUser->getTitle(),
                        'subject' => $info['email_subject'],
                        'message' => $info['email_message'],
                    );

                    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                            $recipient, $mailType, $mailParams
                    );
                }
            }
        }
        return $bannedUserList;
    }

    public function getBannedUsernameByUsername($username) {
        $result = array();
        $extraInfoTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
        $userTable = Engine_Api::_()->getDBTable('users', 'user');
        $bannedUsers = $this->select()
                ->from($this, array('banned_id' => 'bannedusername_id', 'username'))
                ->where('username = ?', $username)
                ->query()
                ->fetchAll();
        if (count($bannedUsers) != 0) {
            foreach ($bannedUsers as $bannedUser) {

                $extraInfo = $extraInfoTable->getExtraInfo($bannedUser['banned_id'], 0);

                $user = $userTable->select()
                        ->where('username = ?', $bannedUser['username'])
                        ->query()
                        ->fetchAll();

                //if (count($extraInfo) != 0 && count($user) != 0) 
                if (count($user) != 0) {
                    $bannedUser['user'] = $user;
                    $bannedUser['extra_info'] = $extraInfo;
                    $result = $bannedUser;
                }
                break;
            }
        }
        return $result;
    }

    public function isUsernameBanned($username) {
        $data = $this->select()
                ->from($this, 'username')
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $isBanned = false;

        foreach ($data as $test) {
            if (false === strpos($test, '*')) {
                if (strtolower($username) == $test) {
                    $isBanned = true;
                    break;
                }
            } else {
                $pregExpr = preg_quote($test, '/');
                $pregExpr = str_replace('*', '.*?', $pregExpr);
                $pregExpr = '/' . $pregExpr . '/i';
                if (preg_match($pregExpr, $username)) {
                    $isBanned = true;
                    break;
                }
            }
        }

        return $isBanned;
    }

    public function unBanUsernames($ids) {

        if (!empty($ids)) {
            $this->delete(array(
                'bannedusername_id IN(?)' => $ids,
            ));

            $extraTable = Engine_Api::_()->getDBTable('extrainfo', 'ynbanmem');
            $extraTable->delete(array(
                'banned_id IN(?)' => $ids,
                'banned_type = 0'
            ));
        }

        return $this;
    }

    public function unBanUsername($id) {

        $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
        $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');
        if (!empty($ids)) {
            $exists = $extraInfoTable->select()
                    ->where('banned_id = ?', $id)
                    ->where('banned_type = ?', 0)
                    ->query()
                    ->fetch();

            if (count($exists) != 0) {
                $extraInfoTable->delete(array(
                    'banned_id = ?' => $id,
                    'banned_type = ?' => 0
                ));
            }
            $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $id));
        }

        return $this;
    }

}
