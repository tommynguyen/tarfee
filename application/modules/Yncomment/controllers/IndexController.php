<?php
class Yncomment_IndexController extends Core_Controller_Action_Standard {

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /yncomment/index/reply
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function replyAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Make form
        $this -> view -> form = $form = new Yncomment_Form_Reply();
        // Not post
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Not a post');
            return;
        }
        $settings = Engine_Api::_() -> getApi('settings', 'core');
        
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        
        // Not valid
        if (!$form -> isValid($params)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
            return;
        }
        
        // Start transaction
        $db = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getAdapter();
        $db -> beginTransaction();

        try {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $action_id = $this -> view -> action_id = $this -> _getParam('action_id', $this -> _getParam('action', null));
            $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
            $actionOwner = Engine_Api::_() -> getItemByGuid($action -> subject_type . "_" . $action -> subject_id);
            // Filter HTML
            $filter = new Zend_Filter();
            $filter -> addFilter(new Engine_Filter_Censor());
            $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
            
            $body = $form -> getValue('body');
            // fix SE enable link issue
            $body = str_replace('&amp;quot;"', '"', $body);
            $body = str_replace('&quot;</a>', '</a>"', $body);
            
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);

            if($action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to comment on this item.');
            }

            // Add the comment
            $subject = $viewer;
            $row = $action -> comments() -> addComment($subject, $body);
            $row -> parent_comment_id = $this -> _getParam('comment_id', null);
            $row -> save();

            // Notifications
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');

            // Add notification for owner of activity (if user and not viewer)
            if ($action -> subject_type == 'user' && $action -> subject_id != $viewer -> getIdentity()) {
                $notifyApi -> addNotification($actionOwner, $subject, $action, 'replied', array('label' => 'post'));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($action->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser -> getType() == 'user' && $notifyUser -> getIdentity() != $viewer -> getIdentity() && $notifyUser -> getIdentity() != $actionOwner -> getIdentity()) {

                    $commentedUserNotifications[] = $notifyUser -> getIdentity();
                    $notifyApi -> addNotification($notifyUser, $subject, $action, 'replied_replied', array('label' => 'post'));
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            foreach ($action->likes()->getAllLikesUsers() as $notifyUser) {
                if (in_array($notifyUser -> getIdentity(), $commentedUserNotifications))
                    continue;

                if ($notifyUser -> getType() == 'user' && $notifyUser -> getIdentity() != $viewer -> getIdentity() && $notifyUser -> getIdentity() != $actionOwner -> getIdentity()) {
                    $notifyApi -> addNotification($notifyUser, $subject, $action, 'liked_replied', array('label' => 'post'));
                }
            }

            // Stats
            $attachment = null;
            $attachmentPhotoValue = $this -> _getParam('photo_id');
            $attachmentType = $this -> _getParam('type');
            
            $linkEnabled = $this -> _getParam('linkEnabled');

            if ($attachmentPhotoValue && $attachmentType) {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                    $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentPhotoValue);
                else
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
            }
            // check body exist link
            else if ($linkEnabled && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) 
            {
                $body_decode = html_entity_decode($body);
                $body_decode = html_entity_decode($body_decode);
                $body_decode = html_entity_decode($body_decode);
                $regex = '/http(s)?:\/\/([^" ]*)/mi';
                preg_match_all($regex, $body_decode, $matches);
                if (count($matches) > 0) 
                {
                    $link = $matches[0][0];
                    if ($link) 
                    {
                        $info = parse_url($link);
                        try
                        {
                          $client = new Zend_Http_Client($link, array(
                            'maxredirects' => 2,
                            'timeout'      => 30,
                          ));
                    
                          // Try to mimic the requesting user's UA
                          $client->setHeaders(array(
                            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                            'X-Powered-By' => 'Zend Framework'
                          ));
                          $response = $client->request();
                    
                          // Get content-type
                          list($contentType) = explode(';', $response->getHeader('content-type'));
                          $this->view->contentType = $contentType;
                    
                          // Handling based on content-type
                          switch( strtolower($contentType) ) 
                          {
                            // Images
                            case 'image/gif':
                            case 'image/jpeg':
                            case 'image/jpg':
                            case 'image/tif': // Might not work
                            case 'image/xbm':
                            case 'image/xpm':
                            case 'image/png':
                            case 'image/bmp': // Might not work
                              $attachmentData = $this->_previewImage($link, $response);
                              break;
                    
                            // HTML
                            case '':
                            case 'text/html':
                              $attachmentData = $this->_previewHtml($link, $response);
                              break;
                    
                            // Plain text
                            case 'text/plain':
                              $attachmentData = $this->_previewText($link, $response);
                              break;
                          }
                        }
                        catch( Exception $e )
                        {
                          throw $e;
                        }
                        if($attachmentData)
                        {
                            $attachmentData['uri'] = $link;
                            if (Engine_Api::_() -> core() -> hasSubject()) {
                                $subject = Engine_Api::_() -> core() -> getSubject();
                                if ($subject -> getType() != 'user') 
                                {
                                    $attachmentData['parent_type'] = $subject -> getType();
                                    $attachmentData['parent_id'] = $subject -> getIdentity();
                                }
                            }
                            if (!empty($attachmentData['title'])) {
                                $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                            }
                            if (!empty($attachmentData['description'])) {
                                $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                            }
                            else {
                                $attachmentData['description'] = $attachmentData['title'];
                            }
                            $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                        }
                    }
                }
            }
            if (isset($row->attachment_type))
                $row->attachment_type = ( $attachment ? $attachment->getType() : '' );
            if (isset($row->attachment_id))
                $row->attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
            $row->save();

            $composerDatas = $this -> getRequest() -> getParam('composer', null);
            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {
                $viewer = Engine_Api::_() -> _() -> user() -> getViewer();
                $type_name = Zend_Registry::get('Zend_Translate') -> translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_() -> getDbtable('notifications', 'activity');

                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_() -> getItemByGuid($key);
                    if ($tag && ($tag instanceof User_Model_User) && !$tag -> isSelf($viewer)) {
                        $notifyApi -> addNotification($tag, $viewer, $action, 'tagged', array('object_type_name' => $type_name, 'label' => $type_name, ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer -> getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate') -> translate($tag -> getShortType());
                        $item_title = $tag -> getTitle();
                        $owner = $tag -> getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                            $notifyApi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_() -> getItem('user', $offices -> child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                                    $notifyApi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array)$action -> params, array('tags' => $tagsArray));
                    $row -> params = Zend_Json::encode($data);
                }
                $row -> save();
            }

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Assign message for json
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Comment posted');

        // Redirect if not json
        if (null === $this -> _getParam('format', null)) {
            $this -> _redirect($form -> return_url -> getValue(), array('prependBase' => false));
        } else if ('json' === $this -> _getParam('format', null)) {
            $helper = 'ynfeed';
            $method = 'update';
            $show_all_comments = $this -> _getParam('show_all_comments');
            if(is_array($show_all_comments) && count($show_all_comments) > 1)
            {
                 $show_all_comments = $show_all_comments[1];
            }

            $onViewPage = $this -> _getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this -> view -> body = $this -> view -> $helper($action, array('noList' => false, 'submitReply' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => true, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }

    public function replyEditAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Make form
        $this -> view -> form = $form = new Yncomment_Form_Reply();
        // Not post
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Not a post');
            return;
        }
        $settings = Engine_Api::_() -> getApi('settings', 'core');
        
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        // Not valid
        if (!$form -> isValid($params)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getAdapter();
        $db -> beginTransaction();

        try {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $action_id = $this -> view -> action_id = $this -> _getParam('action_id', $this -> _getParam('action', null));
            $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
            $actionOwner = Engine_Api::_() -> getItemByGuid($action -> subject_type . "_" . $action -> subject_id);
            // Filter HTML
            $filter = new Zend_Filter();
            $filter -> addFilter(new Engine_Filter_Censor());
            $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
            $body = $form -> getValue('body');
            // fix SE enable link issue
            $body = str_replace('&amp;quot;"', '"', $body);
            $body = str_replace('&quot;</a>', '</a>"', $body);
            
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);

            if($action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to comment on this item.');
            }

            // Add the comment
            $subject = $viewer;
            $row = $action -> comments() -> getComment($this -> _getParam('comment_id', null));
            $row -> body = $body;
            $row -> save();
            
            $attachment = null;
            $attachmentPhotoValue = $this -> _getParam('photo_id');
            $attachmentType = $this -> _getParam('type');
            
            $linkEnabled = $this -> _getParam('linkEnabled');
            
            if ($attachmentPhotoValue && $attachmentType) 
            {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                    $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentPhotoValue);
                else
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
                if (isset($row -> attachment_type))
                    $row -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($row -> attachment_id))
                    $row -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $row -> save();
            } 
            elseif (!$attachmentPhotoValue && !$attachmentType) 
            {
                if ($linkEnabled && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) 
                {
                    $body_decode = html_entity_decode($body);
                    $body_decode = html_entity_decode($body_decode);
                    $body_decode = html_entity_decode($body_decode);
                    $regex = '/http(s)?:\/\/([^" ]*)/mi';
                    preg_match_all($regex, $body_decode, $matches);
                    if (count($matches) > 0) 
                    {
                        $link = $matches[0][0];
                        if ($link) 
                        {
                            $info = parse_url($link);
                            try
                            {
                              $client = new Zend_Http_Client($link, array(
                                'maxredirects' => 2,
                                'timeout'      => 30,
                              ));
                        
                              // Try to mimic the requesting user's UA
                              $client->setHeaders(array(
                                'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                'X-Powered-By' => 'Zend Framework'
                              ));
                              $response = $client->request();
                        
                              // Get content-type
                              list($contentType) = explode(';', $response->getHeader('content-type'));
                              $this->view->contentType = $contentType;
                        
                              // Handling based on content-type
                              switch( strtolower($contentType) ) 
                              {
                                // Images
                                case 'image/gif':
                                case 'image/jpeg':
                                case 'image/jpg':
                                case 'image/tif': // Might not work
                                case 'image/xbm':
                                case 'image/xpm':
                                case 'image/png':
                                case 'image/bmp': // Might not work
                                  $attachmentData = $this->_previewImage($link, $response);
                                  break;
                        
                                // HTML
                                case '':
                                case 'text/html':
                                  $attachmentData = $this->_previewHtml($link, $response);
                                  break;
                        
                                // Plain text
                                case 'text/plain':
                                  $attachmentData = $this->_previewText($link, $response);
                                  break;
                              }
                            }
                            catch( Exception $e )
                            {
                              throw $e;
                            }
                            if($attachmentData)
                            {
                                $attachmentData['uri'] = $link;
                                if (Engine_Api::_() -> core() -> hasSubject()) {
                                    $subject = Engine_Api::_() -> core() -> getSubject();
                                    if ($subject -> getType() != 'user') 
                                    {
                                        $attachmentData['parent_type'] = $subject -> getType();
                                        $attachmentData['parent_id'] = $subject -> getIdentity();
                                    }
                                }
                                if (!empty($attachmentData['title'])) {
                                    $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                                }
                                if (!empty($attachmentData['description'])) {
                                    $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                                }
                                else {
                                    $attachmentData['description'] = $attachmentData['title'];
                                }
                                $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                            }
                        }
                    }
                }
                if (isset($row -> attachment_type))
                    $row -> attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($row -> attachment_id))
                    $row -> attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $row -> save();
            }
            $composerDatas = $this -> getRequest() -> getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {
                $viewer = Engine_Api::_() -> _() -> user() -> getViewer();
                $type_name = Zend_Registry::get('Zend_Translate') -> translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_() -> getDbtable('notifications', 'activity');

                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_() -> getItemByGuid($key);
                    if ($tag && ($tag instanceof User_Model_User) && !$tag -> isSelf($viewer)) {
                        $notificationAPi -> addNotification($tag, $viewer, $action, 'tagged', array('object_type_name' => $type_name, 'label' => $type_name, ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer -> getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate') -> translate($tag -> getShortType());
                        $item_title = $tag -> getTitle();
                        $owner = $tag -> getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                            $notificationAPi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_() -> getItem('user', $offices -> child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                                    $notificationAPi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array)$action -> params, array('tags' => $tagsArray));
                    $row -> params = Zend_Json::encode($data);
                }
                $row -> save();
            }
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Assign message for json
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Comment posted');

        // Redirect if not json
        if (null === $this -> _getParam('format', null)) {
            $this -> _redirect($form -> return_url -> getValue(), array('prependBase' => false));
        } else if ('json' === $this -> _getParam('format', null)) {
            $helper = 'ynfeed';
            $method = 'update';
            $show_all_comments = $this -> _getParam('show_all_comments');
            if(is_array($show_all_comments) && count($show_all_comments) > 1)
            {
                 $show_all_comments = $show_all_comments[1];
            }

            $onViewPage = $this -> _getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this -> view -> body = $this -> view -> $helper($action, array('noList' => false, 'submitReply' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => true, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }

    /**
     * Handles HTTP POST request to comment on an activity feed item
     *
     * Uses the default route and can be accessed from
     *  - /yncomment/index/comment
     *
     * @throws Engine_Exception If a user lacks authorization
     * @return void
     */
    public function commentEditAction() {
        // Make sure user exists
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        // Make form
        $this -> view -> form = $form = new Yncomment_Form_Feed_Comment();
        // Not post
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Not a post');
            return;
        }
        $settings = Engine_Api::_() -> getApi('settings', 'core');
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        // Not valid
        if (!$form -> isValid($params)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid data');
            return;
        }
        // Start transaction
        $db = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getAdapter();
        $db -> beginTransaction();

        try {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $action_id = $this -> view -> action_id = $this -> _getParam('action_id', $this -> _getParam('action', null));
            $action = Engine_Api::_() -> getDbtable('actions', 'ynfeed') -> getActionById($action_id);
            $actionOwner = Engine_Api::_() -> getItemByGuid($action -> subject_type . "_" . $action -> subject_id);
            
            // Filter HTML
            $filter = new Zend_Filter();
            $filter -> addFilter(new Engine_Filter_Censor());
            $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
            $body = $form -> getValue('body');
            // fix SE enable link issue
            $body = str_replace('&amp;quot;"', '"', $body);
            $body = str_replace('&quot;</a>', '</a>"', $body);
            
            $body = preg_replace('/<br[^<>]*>/', "\n", $body);

            if($action->getCommentObject() && !in_array($action->getCommentObject() -> getType(), array('core_link', 'ynfeed_map')))
            {
                 // Check authorization
                if (!Engine_Api::_()->authorization()->isAllowed($action->getCommentObject(), null, 'comment'))
                    throw new Engine_Exception('This user is not allowed to comment on this item.');
            }

            // Add the comment
            $subject = $viewer;
            $row = $action -> comments() -> getComment($this -> _getParam('comment_id', null));
            $row -> body = $body;
            $row -> save();

            $attachment = null;
            $attachmentPhotoValue = $this -> _getParam('photo_id');
            $attachmentType = $this -> _getParam('type');
            $linkEnabled = $this -> _getParam('linkEnabled');
            
            if ($attachmentPhotoValue && $attachmentType) 
            {
                if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                    $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentPhotoValue);
                else
                    $attachment = Engine_Api::_()->getItem('album_photo', $attachmentPhotoValue);
                if (isset($row -> attachment_type))
                    $row -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($row -> attachment_id))
                    $row -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $row -> save();
            } 
            elseif (!$attachmentPhotoValue && !$attachmentType) 
            {
                if ($linkEnabled && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) 
                {
                    $body_decode = html_entity_decode($body);
                    $body_decode = html_entity_decode($body_decode);
                    $body_decode = html_entity_decode($body_decode);
                    $regex = '/http(s)?:\/\/([^" ]*)/mi';
                    preg_match_all($regex, $body_decode, $matches);
                    if (count($matches) > 0) 
                    {
                        $link = $matches[0][0];
                        if ($link) 
                        {
                            $info = parse_url($link);
                            try
                            {
                              $client = new Zend_Http_Client($link, array(
                                'maxredirects' => 2,
                                'timeout'      => 30,
                              ));
                        
                              // Try to mimic the requesting user's UA
                              $client->setHeaders(array(
                                'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                'X-Powered-By' => 'Zend Framework'
                              ));
                              $response = $client->request();
                        
                              // Get content-type
                              list($contentType) = explode(';', $response->getHeader('content-type'));
                              $this->view->contentType = $contentType;
                        
                              // Handling based on content-type
                              switch( strtolower($contentType) ) 
                              {
                                // Images
                                case 'image/gif':
                                case 'image/jpeg':
                                case 'image/jpg':
                                case 'image/tif': // Might not work
                                case 'image/xbm':
                                case 'image/xpm':
                                case 'image/png':
                                case 'image/bmp': // Might not work
                                  $attachmentData = $this->_previewImage($link, $response);
                                  break;
                        
                                // HTML
                                case '':
                                case 'text/html':
                                  $attachmentData = $this->_previewHtml($link, $response);
                                  break;
                        
                                // Plain text
                                case 'text/plain':
                                  $attachmentData = $this->_previewText($link, $response);
                                  break;
                              }
                            }
                            catch( Exception $e )
                            {
                              throw $e;
                            }
                            if($attachmentData)
                            {
                                $attachmentData['uri'] = $link;
                                if (Engine_Api::_() -> core() -> hasSubject()) {
                                    $subject = Engine_Api::_() -> core() -> getSubject();
                                    if ($subject -> getType() != 'user') 
                                    {
                                        $attachmentData['parent_type'] = $subject -> getType();
                                        $attachmentData['parent_id'] = $subject -> getIdentity();
                                    }
                                }
                                if (!empty($attachmentData['title'])) {
                                    $attachmentData['title'] = $filter -> filter($attachmentData['title']);
                                }
                                if (!empty($attachmentData['description'])) {
                                    $attachmentData['description'] = $filter -> filter($attachmentData['description']);
                                }
                                else {
                                    $attachmentData['description'] = $attachmentData['title'];
                                }
                                $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                            }
                        }
                    }
                }
                if (isset($row -> attachment_type))
                    $row -> attachment_type = ( $attachment ? $attachment->getType() : '' );
                if (isset($row -> attachment_id))
                    $row -> attachment_id = ( $attachment ? $attachment->getIdentity() : 0 );
                $row -> save();
            }
            $composerDatas = $this -> getRequest() -> getParam('composer', null);

            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            if (!empty($tagsArray)) {
                $viewer = Engine_Api::_() -> _() -> user() -> getViewer();
                $type_name = Zend_Registry::get('Zend_Translate') -> translate('post');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'post';
                }
                $notificationAPi = Engine_Api::_() -> getDbtable('notifications', 'activity');

                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_() -> getItemByGuid($key);
                    if ($tag && ($tag instanceof User_Model_User) && !$tag -> isSelf($viewer)) {
                        $notificationAPi -> addNotification($tag, $viewer, $action, 'tagged', array('object_type_name' => $type_name, 'label' => $type_name, ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer -> getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate') -> translate($tag -> getShortType());
                        $item_title = $tag -> getTitle();
                        $owner = $tag -> getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                            $notificationAPi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_() -> getItem('user', $offices -> child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                                    $notificationAPi -> addNotification($owner, $viewer, $action, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                                }
                            }
                        }
                    }
                }

                if ($action) {
                    $data = array_merge((array)$action -> params, array('tags' => $tagsArray));
                    $row -> params = Zend_Json::encode($data);
                }
                $row -> save();
            }

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // Assign message for json
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Comment edited');

        // Redirect if not json
        if (null === $this -> _getParam('format', null)) {
            $this -> _redirect($form -> return_url -> getValue(), array('prependBase' => false));
        } else if ('json' === $this -> _getParam('format', null)) {
            $helper = 'ynfeed';
            $method = 'update';
            $show_all_comments = $this -> _getParam('show_all_comments');
            if(is_array($show_all_comments) && count($show_all_comments) > 1)
            {
                 $show_all_comments = $show_all_comments[1];
            }

            $onViewPage = $this -> _getParam('onViewPage');
            if ($onViewPage) {
                $show_all_comments = true;
            }
            $this -> view -> body = $this -> view -> $helper($action, array('noList' => false, 'submitComment' => false, 'onViewPage' => $onViewPage, 'viewAllLikes' => $onViewPage, 'viewAllComments' => $onViewPage, 'hideReply' => false, 'ynfeed_comment_like_box_show' => 1), $method, $show_all_comments);
        }
    }
    protected function _previewImage($uri, Zend_Http_Response $response) 
    {
        return array('thumb' => $uri);
    }

    protected function _previewText($uri, Zend_Http_Response $response) {
        $body = $response -> getBody();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches)) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        // Reduce whitespace
        $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);
        return array('title' => substr($body, 0, 63), 'description' => substr($body, 0, 255));
    }

    protected function _previewHtml($uri, Zend_Http_Response $response) 
    {
        $arr_return = array();
        $body = $response -> getBody();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getHeader('content-type'), $matches) 
            || preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response -> getBody(), $matches)) 
        {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        if (function_exists('mb_convert_encoding')) {
            $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        }

        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null;
            // Maybe add b/c later
        }

        $title = null;
        if ($dom) {
            $titleList = $dom -> query('title');
            if (count($titleList) > 0) {
                $title = trim($titleList -> current() -> textContent);
                $title = substr($title, 0, 255);
            }
        }
        $arr_return['title'] = $title;

        $description = null;
        if ($dom) {
            $descriptionList = $dom -> queryXpath("//meta[@name='description']");
            // Why are they using caps? -_-
            if (count($descriptionList) == 0) {
                $descriptionList = $dom -> queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList -> current() -> getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $arr_return['description'] = $description;

        $thumb = null;
        if ($dom) 
        {
            $mediumList = $dom -> queryXpath("//meta[@property='og:image']");
            if (count($mediumList) > 0) {
                $thumb = $mediumList -> current() -> getAttribute('content');
            }
            if(!$thumb)
            {
                $thumbList = $dom -> queryXpath("//link[@rel='image_src']");
                if (count($thumbList) > 0) {
                    $thumb = $thumbList -> current() -> getAttribute('href');
                }
             }
        }
        $arr_return['thumb'] = $thumb;
        return $arr_return;
    }
}
