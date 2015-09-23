<?php

class Ynbanmem_Model_DbTable_Users extends Engine_Db_Table {

    protected $_name = 'users';

    public function updateUserNote($user_id, $note) {
        $exists = (bool) $this->select()
                        ->from($this, new Zend_Db_Expr('TRUE'))
                        ->where('user_id = ?', $user_id)
                        ->query()
                        ->fetch();

        if ($exists) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            $user->note = $note;
            $user->save();
        }
        return $user;
    }

    public function getUser($user_id) {
        return $this->select()
                        ->where('user_id = ?', $user_id)
                        ->query()
                        ->fetch();
    }

    public function getUserByUusername($username) {
        return $this->select()
                        ->where('username = ?', $username)
                        ->query()
                        ->fetch();
    }

}
