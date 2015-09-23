<?php
class Yncomment_Api_Dislike extends Core_Api_Abstract {

    /**
     * check the item is dislike or not.
     *
     * @param Stirng $resource_type
     * @param Int $resource_id
     * @return results
     */
    public function hasDislike($resource_type, $resource_id) {

        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer();
        $dislikeTable = Engine_Api::_()->getItemTable('yncomment_dislike');
        $dislikeTableName = $dislikeTable->info('name');
        $sub_status_select = $dislikeTable->select()
                ->from($dislikeTableName, array('dislike_id'))
                ->where('resource_type = ?', $resource_type)
                ->where('resource_id = ?', $resource_id)
                ->where('poster_type =?', $viewer->getType())
                ->where('poster_id =?', $viewer->getIdentity())
                ->limit(1);
        return $sub_status_select->query()->fetchAll();
    }

    /**
     * Function for showing 'Number of Likes'.
     *
     * @param Stirng $resource_type
     * @param Int $resource_id
     * @return number of dislikes
     */
    public function dislikeCount($resource_type, $resource_id, $notIncludeId) {
        //GET THE VIEWER (POSTER) AND RESOURCE.
        $poster = Engine_Api::_()->user()->getViewer();
        $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

        $nestedcommentDisLikesTable = Engine_Api::_()->getDbtable('dislikes', 'yncomment');
        $nestedcommentDisLikesTableName = $nestedcommentDisLikesTable->info('name');
        $select = new Zend_Db_Select($nestedcommentDisLikesTable->getAdapter());
        $select
                ->from($nestedcommentDisLikesTableName, new Zend_Db_Expr('COUNT(1) as count'));

        $select->where('resource_type = ?', $resource->getType());
        $select->where('resource_id = ?', $resource->getIdentity());

        if ($notIncludeId)
            $select->where('poster_id != ?', $notIncludeId);
        $data = $select->query()->fetchAll();
        return (int) $data[0]['count'];
    }

    /**
     * THIS FUNCTION SHOW PEOPLE LIKES.
     *
     * @param String $resource_type
     * @param Int $resource_id
     * @param int $limit
     * @return array of result
     */
    public function peopleDislike($resource_type, $resource_id, $limit = null) {

        $dislikeTable = Engine_Api::_()->getItemTable('yncomment_dislike');
        $dislikeTableName = $dislikeTable->info('name');
        $select = $dislikeTable->select()
                ->from($dislikeTableName, array('poster_id'))
                ->where('resource_type = ?', $resource_type)
                ->where('resource_id = ?', $resource_id)
                ->order('dislike_id DESC');

        if ($limit)
            $select->limit($limit);
        return $select->query()->fetchAll();
    }

    /**
     * THIS FUNCTION SHOW PEOPLE LIKES OR FRIEND LIKES.
     *
     * @param String $call_status
     * @param String $resource_type
     * @param int $resource_id
     * @param Int $user_id
     * @param Int $search
     * @return results
     */
    public function friendPublicDislike($call_status, $resource_type, $resource_id, $user_id, $search) {

        $dislikeTableName = Engine_Api::_()->getItemTable('yncomment_dislike')->info('name');
        $membershipTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        $select = $userTable->select()
                ->setIntegrityCheck(false)
                ->from($dislikeTableName, array('poster_id'))
                ->where($dislikeTableName . '.resource_type = ?', $resource_type)
                ->where($dislikeTableName . '.resource_id = ?', $resource_id)
                ->where($dislikeTableName . '.poster_id != ?', 0)
                ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                ->order('dislike_id DESC');

        if ($call_status == 'friend' || $call_status == 'myfrienddislikes') {
            $select->joinInner($membershipTableName, "$membershipTableName . resource_id = $dislikeTableName . poster_id", NULL)
                    ->joinInner($userTableName, "$userTableName . user_id = $membershipTableName . resource_id")
                    ->where($membershipTableName . '.user_id = ?', $user_id)
                    ->where($membershipTableName . '.active = ?', 1)
                    ->where($dislikeTableName . '.poster_id != ?', $user_id);
        } else if ($call_status == 'public') {
            $select->joinInner($userTableName, "$userTableName . user_id = $dislikeTableName . poster_id");
        }
        return $select;
    }

    /**
     * THIS FUNCTION USE FOR USER OR FRIEND NUMBER OF LIKES.
     *
     * @param String $resource_type
     * @param Int $resource_id
     * @param String $params
     * @param Int $limit
     * @return count results
     */
    public function userFriendNumberOfDislike($resource_type, $resource_id, $params, $limit = null, $notIncludeId) {

        //GET THE USER ID.
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

        $dislikeTable = Engine_Api::_()->getItemTable('yncomment_dislike');
        $dislikeTableName = $dislikeTable->info('name');

        $memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

        $select = $dislikeTable->select();

        if ($params == 'friendNumberOfDislike') {
            $select->from($dislikeTableName, array('COUNT(' . $dislikeTableName . '.dislike_id) AS dislike_count'));
        } elseif ($params == 'userFriendDislikes') {
            $select->from($dislikeTableName, array('poster_id'));
        }

        $select->joinInner($memberTableName, "$memberTableName . resource_id = $dislikeTableName . poster_id", NULL)
                ->where($memberTableName . '.resource_id = ?', $viewer_id)
                ->where($memberTableName . '.active = ?', 1)
                ->where($dislikeTableName . '.resource_type = ?', $resource_type)
                ->where($dislikeTableName . '.resource_id = ?', $resource_id)
                ->where($dislikeTableName . '.poster_id != ?', $viewer_id)
                ->where($dislikeTableName . '.poster_id != ?', 0);

        if ($notIncludeId)
            $select->where('poster_id != ?', $notIncludeId);

        if ($params == 'friendNumberOfDislike') {
            $select->group($dislikeTableName . '.resource_id');
        } elseif ($params == 'userFriendDislikes') {
            $select->order($dislikeTableName . '.dislike_id DESC')->limit($limit);
        }
        $fetch_count = $select->query()->fetchColumn();
        if (!empty($fetch_count)) {
            return $fetch_count;
        } else {
            return 0;
        }
    }

}