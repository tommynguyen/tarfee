<?php
class Yncomment_FriendsController extends Core_Controller_Action_User {

    public function init() {
        // Try to set subject
        $user_id = $this->_getParam('user_id', null);
        if ($user_id && !Engine_Api::_()->core()->hasSubject()) {
            $user = Engine_Api::_()->getItem('user', $user_id);
            if ($user) {
                Engine_Api::_()->core()->setSubject($user);
            }
        }
    }

    public function suggestTagAction() {
        $subject_guid = $this->_getParam('subject', null);
        $enableContent = explode(",", $this->_getParam('taggingContent', null));
        $limit = (int) $this->_getParam('limit', 10);
        $viewer = Engine_Api::_()->user()->getViewer();
        
        $userTagged = array();
        $groupTagged = array();
        $arrTagged = explode(",", $this->_getParam('tagged', null));
        foreach ($arrTagged as $value) 
        {
            $arr = explode("_", $value);
            if(current($arr) == 'user')
            {
                $userTagged[] = end($arr);
            }
            elseif(current($arr) == 'group')
            {
                $groupTagged[] = end($arr);
            }
        }

        if ($subject_guid && (stripos($subject_guid, 'event') !== false || stripos($subject_guid, 'group') !== false)) {
            $subject = Engine_Api::_()->getItemByGuid($subject_guid);
        } else {
            $subject = $viewer;
        }
        if (!$viewer->getIdentity()) {
            $data = null;
        } else {
            $data = array();
            if (in_array('friends', $enableContent)) {
                $table = Engine_Api::_()->getItemTable('user');
                $select = $subject->membership()->getMembersObjectSelect();

                if ($this->_getParam('includeSelf', false) 
                && stripos($viewer->getTitle(), $this->_getParam('search', $this->_getParam('value'))) !== false
                && !in_array($viewer -> getIdentity(), $userTagged)) {
                    $data[] = array(
                        'type' => 'user',
                        'id' => $viewer->getIdentity(),
                        'guid' => $viewer->getGuid(),
                        'label' => $viewer->getTitle().'&shy;',
                        'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
                        'url' => $viewer->getHref(),
                    );
                }

                if (0 < ($limit = (int) $this->_getParam('limit', 10))) {
                    $select->limit($limit);
                }

                if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) 
                {
                    $select->where('`' . $table->info('name') . '`.`displayname` LIKE ?', '%' . $text . '%');
                }
                $select->where('`' . $table->info('name') . '`.`user_id` <> ?', $viewer->getIdentity());
                if($userTagged)
                    $select->where('`' . $table->info('name') . '`.`user_id` NOT IN (?)', $userTagged);
                $select->order("{$table->info('name')}.displayname ASC");
                $ids = array();
                foreach ($select->getTable()->fetchAll($select) as $friend) {
                    $data[] = array(
                        'type' => 'user',
                        'id' => $friend->getIdentity(),
                        'guid' => $friend->getGuid(),
                        'label' => $friend->getTitle().'&shy;',
                        'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
                        'url' => $friend->getHref(),
                    );
                }
            }


            if ((in_array('group', $enableContent) || in_array('advgroup', $enableContent)) && Engine_Api::_()->hasItemType('group')) {
                $remaningLimit = $limit - @count($data);
                if ($remaningLimit > 0) {
                    $table = Engine_Api::_()->getItemTable('group');
                    $tableName = $table->info('name');
                    $select = $table->select();
                    $select->where('search = ?', (bool) 1);
                    if (null !== ($text = $this->_getParam('search', $this->_getParam('value')))) {
                        $select->where('`' . $table->info('name') . '`.`title` LIKE ?', '%' . $text . '%');
                    }
                    if($groupTagged)
                        $select->where('`' . $tableName . '`.`group_id` NOT IN (?)', $groupTagged);
                    $select->order("{$tableName}.title ASC");
                    foreach ($select->getTable()->fetchAll($select) as $group) {
                        $data[] = array(
                            'type' => $group->getShortType(true),
                            'id' => $group->getIdentity(),
                            'guid' => $group->getGuid(),
                            'label' => $group->getTitle().'&shy;',
                            'photo' => $this->view->itemPhoto($group, 'thumb.icon'),
                            'url' => $group->getHref(),
                        );
                    }
                }
            }
        }
        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

}
