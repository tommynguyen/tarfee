<?php
class Yncomment_LikeController extends Core_Controller_Action_Standard {

    public function likeAction() {

        //GET THE VIEWER.
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET THE VALUE OF RESOURCE ID AND RESOURCE TYPE AND LIKE ID.
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $like_id = $this->_getParam('like_id');
        $status = $this->_getParam('smoothbox', 1);

        $this->view->status = true;

        //GET THE LIKE BUTTON SETTINGS.
        $this->view->like_setting_button = Engine_Api::_()->getApi('settings', 'core')->getSetting('like.setting.button');

        //GET THE RESOURCE.
        if ($resource_type == 'member') {
            $resource = Engine_Api::_()->getItem('user', $resource_id);
        } else {
            $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
        }

        //GET THE CURRENT UESRID AND SETTINGS.
        $this->view->viewer_id = $loggedin_user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ((empty($loggedin_user_id))) {
            return;
        }

        //CHECK THE LIKE ID.
        if (empty($like_id)) {

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $like_id_temp = Engine_Api::_()->getApi('like', 'yncomment')->hasLike($resource_type, $resource_id);

            //CHECK THE THE ITEM IS LIKED OR NOT.
            if (empty($like_id_temp[0]['like_id'])) {

                $likeTable = Engine_Api::_()->getItemTable('core_like');
                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $likeTable->getAdapter();
                $db->beginTransaction();
                try {
                    //START NOTIFICATION WORK.
                    if ($resource_type == 'forum_topic') {
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->user_id;
                        $label = '{"label":"forum topic"}';
                        $object_type = $resource_type;
                    } else if ($resource_type == 'user') {
                        $getOwnerId = $resource_id;
                        $label = '{"label":"profile"}';
                        $object_type = 'user';
                    } else {
                        if ($resource_type == 'album_photo') {
                            $label = '{"label":"photo"}';
                        } else if ($resource_type == 'group_photo') {
                            $label = '{"label":"group photo"}';
                        } else {
                            $label = '{"label":"' . $resource_type . '"}';
                        }
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;
                        $object_type = $resource_type;
                    }
                    if (!empty($getOwnerId) && $getOwnerId != $viewer->getIdentity()) {
                        $notifyData = $notify_table->createRow();
                        $notifyData->user_id = $getOwnerId;
                        $notifyData->subject_type = $viewer->getType();
                        $notifyData->subject_id = $viewer->getIdentity();
                        $notifyData->object_type = $object_type;
                        $notifyData->object_id = $resource_id;
                        $notifyData->type = 'liked';
                        $notifyData->params = $resource->getShortType();
                        $notifyData->date = date('Y-m-d h:i:s', time());
                        $notifyData->save();
                    }
                    //END NOTIFICATION WORK.

                    if (!empty($resource)) {
                        $like_id = $likeTable->addLike($resource, $viewer)->like_id;
                    }

                    //PASS THE LIKE ID VALUE.
                    $this->view->like_id = $like_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Liked.');
            } else {
                $this->view->like_id = $like_id_temp[0]['like_id'];
            }
        } else {

            //START DELETE NOTIFICATION
            Engine_Api::_()->getDbtable('notifications', 'activity')->delete(array('type = ?' => 'liked', 'subject_id = ?' => $viewer->getIdentity(), 'subject_type = ?' => $viewer->getType(), 'object_type = ?' => $resource_type, 'object_id = ?' => $resource_id));
            //END DELETE NOTIFICATION
            //START UNLIKE WORK.
            //HERE 'PAGE OR LIST PLUGIN' CHECK WHEN UNLIKE
            if (!empty($resource) && isset($resource->like_count)) {
                $resource->like_count--;
                $resource->save();
            }
            $contentTable = Engine_Api::_()->getDbTable('likes', 'core')->delete(array('like_id =?' => $like_id));

            //CHECKING IF USER HAS MAKING DUPLICATE ENTRY OF LIKING AN APPLICATION.
            $dislike_id_temp = Engine_Api::_()->getDbTable('dislikes', 'yncomment')->hasDislike($resource_type, $resource_id);

            //CHECK THE THE ITEM IS LIKED OR NOT.
            if (empty($dislike_id_temp[0]['dislike_id'])) {

                $dislikelikeTable = Engine_Api::_()->getItemTable('core_dislike');
                $notify_table = Engine_Api::_()->getDbtable('notifications', 'activity');
                $db = $dislikelikeTable->getAdapter();
                $db->beginTransaction();
                try {

                    //START NOTIFICATION WORK.
                    if ($resource_type == 'forum_topic') {
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->user_id;
                        $label = '{"label":"forum topic"}';
                        $object_type = $resource_type;
                    } else if ($resource_type == 'user') {
                        $getOwnerId = $resource_id;
                        $label = '{"label":"profile"}';
                        $object_type = 'user';
                    } else {
                        if ($resource_type == 'album_photo') {
                            $label = '{"label":"photo"}';
                        } else if ($resource_type == 'group_photo') {
                            $label = '{"label":"group photo"}';
                        } else {
                            $label = '{"label":"' . $resource_type . '"}';
                        }
                        $getOwnerId = Engine_Api::_()->getItem($resource_type, $resource_id)->getOwner()->user_id;
                        $object_type = $resource_type;
                    }
                    if (!empty($getOwnerId) && $getOwnerId != $viewer->getIdentity()) {
                        $notifyData = $notify_table->createRow();
                        $notifyData->user_id = $getOwnerId;
                        $notifyData->subject_type = $viewer->getType();
                        $notifyData->subject_id = $viewer->getIdentity();
                        $notifyData->object_type = $object_type;
                        $notifyData->object_id = $resource_id;
                        $notifyData->type = 'disliked';
                        $notifyData->params = $resource->getShortType();
                        $notifyData->date = date('Y-m-d h:i:s', time());
                        $notifyData->save();
                    }
                    //END NOTIFICATION WORK.

                    if (!empty($resource)) {


                        $dislike_id = $dislikeTable->addDislike($resource, $viewer)->dislike_id;
                    }

                    //PASS THE LIKE ID VALUE.
                    $this->view->dislike_id = $dislike_id;
                    $db->commit();
                } catch (Exception $e) {
                    $db->rollBack();
                    throw $e;
                }
                $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Disliked.');
            } else {
                $this->view->dislike_id = $dislike_id_temp[0]['dislike_id'];
            }

            $like_msg = Zend_Registry::get('Zend_Translate')->_('Successfully Unliked.');
        }
        if (empty($status)) {
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array($like_msg)
                    )
            );
        }
        //HERE THE CONTENT TYPE MEANS MODULE NAME
        $num_of_contenttype = Engine_Api::_()->getApi('like', 'yncomment')->likeCount($resource_type, $resource_id);
        $likes_number = $this->view->translate(array('%s like', '%s likes', $num_of_contenttype), $this->view->locale()->toNumber($num_of_contenttype));
        $this->view->num_of_like = "<a href='javascript:void(0);' onclick='showSmoothBox(); return false;' >" . $likes_number . "</a>";
    }

    //ACTION FOR LIKES THE LISTING
    public function likelistAction() {

        //GET SETTINGS
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies');
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->other = $other = $this->_getParam('other', 0);
        $this->view->notIncludedId = $notIncludedId = 0;
        if($other)
        $this->view->notIncludedId = $notIncludedId = $this->_getParam('notIncludedId');
        $search = $this->_getParam('search', '');
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);

        $this->view->search = $search;
        if (empty($search)) {
            $this->view->search = $this->view->translate('Search Members');
        }
        $likeTableName = Engine_Api::_()->getItemTable('core_like')->info('name');
        $memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        if ($call_status == 'friend') {
            $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($likeTableName, array('poster_id'))
                    ->joinInner($memberTableName, "$memberTableName . user_id = $likeTableName . poster_id", NULL)
                    ->joinInner($userTableName, "$userTableName . user_id = $memberTableName . user_id")
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($likeTableName . '.resource_type = ?', $resource_type)
                    ->where($likeTableName . '.resource_id = ?', $resource_id)
                    ->where($likeTableName . '.poster_id != ?', $viewer_id)
                    ->where($likeTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order('	like_id DESC');
        } else if ($call_status == 'public') {
            $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($likeTableName, array('poster_id'))
                    ->joinInner($userTableName, "$userTableName . user_id = $likeTableName . poster_id")
                    ->where($likeTableName . '.resource_type = ?', $resource_type)
                    ->where($likeTableName . '.resource_id = ?', $resource_id)
                    ->where($likeTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order($likeTableName . '.like_id DESC');
        }
        if($other) {
              $sub_status_select->where($likeTableName . '.poster_id != ?', $notIncludedId);
        }
       
        $fetch_sub = Zend_Paginator::factory($sub_status_select);
        $fetch_sub->setCurrentPageNumber($page);
        $fetch_sub->setItemCountPerPage(10);
        $check_object_result = $fetch_sub->getTotalItemCount();

        $this->view->user_obj = array();
        if (!empty($check_object_result)) {
            $this->view->user_obj = $fetch_sub;
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }

        //TOTAL LIKES
        $this->view->public_count = Engine_Api::_()->getApi('like', 'yncomment')->likeCount($resource_type, $resource_id, $notIncludedId );

        //NUMBER OF FRIENDS LIKES
        $this->view->friend_count = Engine_Api::_()->getApi('like', 'yncomment')->userFriendNumberOflike($resource_type, $resource_id, 'friendNumberOfLike', 5, $notIncludedId);

    }

    //ACTION FOR LIKES THE LISTING
    public function dislikelistAction() {

        //GET SETTINGS
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->resource_type = $resource_type = $this->_getParam('resource_type');
        $this->view->resource_id = $resource_id = $this->_getParam('resource_id');
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->other = $other = $this->_getParam('other', 0);

        $search = $this->_getParam('search', '');
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->search = $search;
        if (empty($search)) {
            $this->view->search = $this->view->translate('Search Members');
        }
        $this->view->showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies');

        $dislikeTableName = Engine_Api::_()->getItemTable('yncomment_dislike')->info('name');

        $memberTableName = Engine_Api::_()->getDbtable('membership', 'user')->info('name');

        $userTable = Engine_Api::_()->getItemTable('user');
        $userTableName = $userTable->info('name');

        if ($call_status == 'friend') {
           $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($dislikeTableName, array('poster_id'))
                    ->joinInner($memberTableName, "$memberTableName . resource_id = $dislikeTableName . poster_id", NULL)
                    ->joinInner($userTableName, "$userTableName . user_id = $dislikeTableName . resource_id")
                    ->where($memberTableName . '.resource_id = ?', $viewer_id)
                    ->where($memberTableName . '.active = ?', 1)
                    ->where($dislikeTableName . '.resource_type = ?', $resource_type)
                    ->where($dislikeTableName . '.resource_id = ?', $resource_id)
                    ->where($dislikeTableName . '.poster_id != ?', $viewer_id)
                    ->where($dislikeTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order('dislike_id DESC');
           
        } else if ($call_status == 'public') {
            $sub_status_select = $userTable->select()
                    ->setIntegrityCheck(false)
                    ->from($dislikeTableName, array('poster_id'))
                    ->joinInner($userTableName, "$userTableName . user_id = $dislikeTableName . poster_id")
                    ->where($dislikeTableName . '.resource_type = ?', $resource_type)
                    ->where($dislikeTableName . '.resource_id = ?', $resource_id)
                    ->where($dislikeTableName . '.poster_id != ?', 0)
                    ->where($userTableName . '.displayname LIKE ?', '%' . $search . '%')
                    ->order($dislikeTableName . '.dislike_id DESC');
        }
        $this->view->other = $other = $this->_getParam('other', 0);
        $this->view->notIncludedId = $notIncludedId = 0;
        if($other)
        $this->view->notIncludedId = $notIncludedId = $this->_getParam('notIncludedId');
        
        if($other) {
              $sub_status_select->where($dislikeTableName . '.poster_id != ?', $notIncludedId);
              
        }
        $fetch_sub = Zend_Paginator::factory($sub_status_select);
        $fetch_sub->setCurrentPageNumber($page);
        $fetch_sub->setItemCountPerPage(10);
        $check_object_result = $fetch_sub->getTotalItemCount();

        $this->view->user_obj = array();
        if (!empty($check_object_result)) {
            $this->view->user_obj = $fetch_sub;
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }

        //TOTAL LIKES
        $this->view->public_count = Engine_Api::_()->getApi('dislike', 'yncomment')->dislikeCount($resource_type, $resource_id, $notIncludedId);

        //NUMBER OF FRIENDS LIKES
        $this->view->friend_count = Engine_Api::_()->getApi('dislike', 'yncomment')->userFriendNumberOfDislike($resource_type, $resource_id, 'friendNumberOfDislike', 5, $notIncludedId);

    }
    
    public function activityLikeAction() {
        
        //GET SETTINGS
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->search = $search = $this->_getParam('search', '');
        if (empty($search)) {
            $this->view->search = $this->view->translate('Search Members');
        }
        
        $this->view->action_id =  $action_id = $this->_getParam('action_id');
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        $this->view->call_status = $call_status = $this->_getParam('call_status');
       
        if($call_status == 'public') {
             $this->view->user_obj = Zend_Paginator::factory($action->likes()->getAllLikesUsers());
        } 
        
        if($this->view->user_obj->getTotalItemCount() > 0) {
            $this->view->user_obj->setCurrentPageNumber($page);
            $this->view->user_obj->setItemCountPerPage(10);
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }
        
        $this->view->public_count = Zend_Paginator::factory($action->likes()->getAllLikesUsers())->getTotalItemCount();
    }
    
    public function activityDislikeAction() {
        
        //GET SETTINGS
        $this->view->call_status = $call_status = $this->_getParam('call_status');
        $this->view->page = $page = $this->_getParam('page', 1);
        $this->view->is_ajax = $is_ajax = $this->_getParam('is_ajax', 0);
        $this->view->search = $search = $this->_getParam('search', '');
        if (empty($search)) {
            $this->view->search = $this->view->translate('Search Members');
        }
        
        $this->view->action_id =  $action_id = $this->_getParam('action_id');
        $action = Engine_Api::_()->getDbtable('actions', 'ynfeed')->getActionById($action_id);
        $this->view->call_status = $call_status = $this->_getParam('call_status');
       
        if($call_status == 'public') {
             $this->view->user_obj = Zend_Paginator::factory($action->dislikes()->getAllDislikesUsers());
        } 
        
        if($this->view->user_obj->getTotalItemCount() > 0) {
            $this->view->user_obj->setCurrentPageNumber($page);
            $this->view->user_obj->setItemCountPerPage(10);
        } else {
            $this->view->no_result_msg = $this->view->translate('No results were found.');
        }
        
        $this->view->public_count = Zend_Paginator::factory($action->dislikes()->getAllDislikesUsers())->getTotalItemCount();
    }    

}