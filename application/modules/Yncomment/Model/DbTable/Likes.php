<?php
class Yncomment_Model_DbTable_Likes extends Core_Model_DbTable_Likes {

    protected $_rowClass = 'Yncomment_Model_Like';
    protected $_custom = false;
    protected $_name = 'core_likes';

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_()->getDbtable('likes', 'core'));
    }

}