<?php
class Yncomment_Model_DbTable_Comments extends Core_Model_DbTable_Comments {

    protected $_rowClass = 'Yncomment_Model_Comment';
    protected $_serializedColumns = array('params');
    protected $_custom = false;
    protected $_name = 'core_comments';

    public function updateComment(Core_Model_Item_Abstract $resource, Core_Model_Item_Abstract $poster, $body) {
        $table = $this -> getCommentTable();
        $row = $table -> createRow();

        if (isset($row -> resource_type)) {
            $row -> resource_type = $resource -> getType();
        }

        $row -> resource_id = $resource -> getIdentity();
        $row -> poster_type = $poster -> getType();
        $row -> poster_id = $poster -> getIdentity();
        $row -> creation_date = date('Y-m-d H:i:s');
        $row -> body = $body;
        $row -> save();

        return $row;
    }

    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments($subject) {
        return new Engine_ProxyObject($subject, Engine_Api::_() -> getDbtable('comments', 'core'));
    }

    public function removeReply($subject, $comment_parent_id)
    {
        if($replies = $this -> getAllReplies($subject, $comment_parent_id))
        {
            foreach ($replies as $reply) {
                $this -> removeReply($subject, $reply -> getIdentity());
            }
        }
        $this -> comments($subject)->removeComment($comment_parent_id);
    }
    public function getAllReplies($subject, $comment_parent_id)
    {
        $commentSelect = $this -> comments($subject)->getCommentSelect();
        $commentSelect->where('parent_comment_id =?', $comment_parent_id);
        return $this -> fetchAll($commentSelect);
    }
}
