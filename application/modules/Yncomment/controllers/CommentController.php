<?php
class Yncomment_CommentController extends Core_Controller_Action_Standard {
    public function init() {
        $type = $this -> _getParam('type');
        $identity = $this -> _getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_() -> getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract) {
                if (!Engine_Api::_() -> core() -> hasSubject()) {
                    Engine_Api::_() -> core() -> setSubject($item);
                }
            }
        }
    }

    public function listAction() 
    {
        // In the comments on content of this module, which all types of content should be taggable?
        $this -> view -> taggingContent = $taggingContent = $this -> _getParam('taggingContent');
        // Do you want to enable nested comments feature for this module's content?
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested', 1);
        // Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.)
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        // Which all types of attachments do you want to allow in comments on this module's content?
        $this -> view -> showComposerOptions = $showComposerOptions = $this -> _getParam('showComposerOptions');
        // Check show lightbox
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        // Select the order in which comments should be displayed on your website.
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        // Check show emoticons
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        // Do you want to show the users who have disliked a content, comment and reply?
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        // How do you want to display the Like / Dislike options for content of this module?
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        
        // How do you want to display the Like / Dislike options for comments and replies?
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> viewer_id = $viewer_id = $viewer -> getIdentity();
        $this -> view -> subject = $subject = $this -> getSubjectItem();
        $subjectParent = $subject;

        // Perms
        $this -> view -> canComment = $canComment = $subject -> authorization() -> isAllowed($viewer, 'comment');
        $this -> view -> canDelete = $subject -> authorization() -> isAllowed($viewer, 'edit');
        $autorizationApi = Engine_Api::_() -> authorization();

        // Widget Content Loading Ajax
        $this -> view -> nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('yncomment.comment.pressenter', 1);
        $this -> view -> addHelperPath(APPLICATION_PATH . '/application/modules/Yncomment/View/Helper', 'Yncomment_View_Helper');
        // Likes
        $this -> view -> viewAllLikes = $this -> _getParam('viewAllLikes', false);
        $this -> view -> viewAllDislikes = $this -> _getParam('viewAllDislikes', false);
        $this -> view -> likes = $likes = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($subject) -> getLikePaginator();
        $this -> view -> dislikes = $dislikes = Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> getDislikePaginator($subject);
        $this -> view -> parent_comment_id = $parent_comment_id = $this -> _getParam('parent_comment_id', 0);
        $this -> view -> comment_id = $comment_id = $this -> _getParam('comment_id', 0);
        $this -> view -> parent_div = $parent_div = $this -> _getParam('parent_div', 0);
         // Open comment hidden
        $this -> view -> openHide = $openHide = $this -> _getParam('openHide', 0);

        $this -> view -> format = $this -> _getParam('format');

        if ($commentsorder) {
            $this -> view -> order = $order = $this -> _getParam('order', 'DESC');
        } else {
            $this -> view -> order = $order = $this -> _getParam('order', 'ASC');
        }
		
		// Add filter comments
		$this -> view -> filter = $filter = $this -> _getParam('filter', 'public');
		$userIds = Engine_Api::_() -> user() -> getProfessionalUsers();
		 
        if (empty($parent_comment_id)) {
            $commentCountSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect($order);

            if (!$showAsNested) {
                $commentCountSelect -> where('parent_comment_id =?', 0);
            }
			
			if($filter == 'professional')
			{
				$commentCountSelect -> where("poster_id IN (?)", $userIds);
			}
            $this -> view -> commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect) -> getTotalItemCount();
        }

        if ($parent_comment_id) {
            $comment_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncomment.reply.per.page', 4);
        } else {
            $comment_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncomment.comment.per.page', 10);
        }

        // Comments
        // If has a page, display oldest to newest
        if (0 !== ($page = $this -> _getParam('page', 0))) {
            $commentSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect();
            $commentSelect -> where('parent_comment_id =?', $parent_comment_id);

            $commentSelect -> reset('order');
            if ($order != 'like_count') {
                $commentSelect -> order("comment_id $order");
            } else {
                $commentSelect -> order("$order DESC");
            }
			
			if($filter == 'professional')
			{
				$commentSelect -> where("poster_id IN (?)", $userIds);
			}

            $comments = Zend_Paginator::factory($commentSelect);
            $comments -> setCurrentPageNumber($page + 1);
            $comments -> setItemCountPerPage($comment_per_page);
            $this -> view -> comments = $comments;
            $this -> view -> page = $page;
        }
        // If not has a page, show the
        else {
            $commentSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect();
            $commentSelect -> where('parent_comment_id =?', $parent_comment_id);

            $commentSelect -> reset('order');
            if ($order != 'like_count') {
                $commentSelect -> order("comment_id $order");
            } else {
                $commentSelect -> order("$order DESC");
            }
            if($filter == 'professional')
			{
				$commentSelect -> where("poster_id IN (?)", $userIds);
			}
            $comments = Zend_Paginator::factory($commentSelect);
            $comments -> setCurrentPageNumber(1);
            $comments -> setItemCountPerPage($comment_per_page);
            $this -> view -> comments = $comments;
            $this -> view -> page = $page;
        }

        $this -> view -> nested_comment_id = $subject -> getGuid() . "_" . $parent_comment_id;

        if ($viewer -> getIdentity() && $canComment) {
            $this -> view -> formComment = $form = new Yncomment_Form_Comment_Create( array('textareaId' => $this -> view -> nested_comment_id));
            if ($parent_comment_id) {
                $form -> getElement('submit') -> setLabel('Post Reply');
            }

            $form -> populate(array('identity' => $subject -> getIdentity(), 'type' => $subject -> getType(), 'format' => 'html', 'parent_comment_id' => $parent_comment_id, 'taggingContent' => $taggingContent, 'showComposerOptions' => $showComposerOptions, 'showAsNested' => $showAsNested, 'showAsLike' => $showAsLike, 'showDislikeUsers' => $showDislikeUsers, 'showLikeWithoutIcon' => $showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies, 'showSmilies' => $showSmilies, 'photoLightboxComment' => $photoLightboxComment, 'commentsorder' => $commentsorder));
        }

        if ($showAsLike) {
            $this -> renderScript('comment/list.tpl');
        } else {
            $this -> renderScript('comment/list_both_like_dislike.tpl');
        }
    }

    public function createAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        // get subject
        $subject = $this -> getSubjectItem();
        $subjectParent = $subject;

        $viewer_id = $viewer -> getIdentity();
        $autorizationApi = Engine_Api::_() -> authorization();
        // check permisson comment
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $this -> view -> form = $form = new Yncomment_Form_Comment_Create( array('textareaId' => $subject -> getGuid() . "_0"));

        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid request method");
            return;
        }
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        
        if (!$form -> isValid($params)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid data");
            return;
        }
        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter -> addFilter(new Engine_Filter_Censor());
        $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());
        $body = $form -> getValue('body');
        // fix SE enable link issue
        $body = str_replace('&amp;quot;"', '"', $body);
        $body = str_replace('&quot;</a>', '</a>"', $body);
        
        $body = preg_replace('/<br[^<>]*>/', "\n", $body);
        $db = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            // Add comment
            $comment = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> addComment($viewer, $body);
            $comment -> parent_comment_id = $form -> getValue('parent_comment_id');
            $comment -> save();

            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $subjectOwner = $subject -> getOwner('user');

            //TRY ATTACHMENT GETTING STUFF
            $attachment = null;
            $attachmentData = $this -> getRequest() -> getParam('attachment');
            $showComposerOptions = $this -> _getParam('showComposerOptions');
            $arr_showComposerOptions = array();
            if ($showComposerOptions) {
                $arr_showComposerOptions = explode(',', $showComposerOptions);
            }

            if (!empty($attachmentData) && !empty($attachmentData['type'])) {
                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                {
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                        $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentData['photo_id']);
                    else
                        $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);
                }

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link') 
                {
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
                    $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                }
            }
            // check body exist link
            else if (in_array('addLink', $arr_showComposerOptions) && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) {
                $body_decode = html_entity_decode($body);
                $body_decode = html_entity_decode($body_decode);
                $body_decode = html_entity_decode($body_decode);
                $regex = '/http(s)?:\/\/([^" ]*)/mi';
                preg_match_all($regex, $body_decode, $matches);
                if (count($matches) > 0) {
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
            
            $composerDatas = $this -> getRequest() -> getParam('composer', null);
            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            $action_body = $body;
            if (!empty($tagsArray)) 
            {
                $action_body = $this -> _stringTagToObject($action_body, $tagsArray);
            }
            $action_body = $this -> _smileyToEmoticons($action_body);
            // Add Activity
            if (empty($comment -> parent_comment_id)) 
            {
                $action = $activityApi -> addActivity($viewer, $subject, 'comment_' . $subject -> getType(), $action_body, array('owner' => $subjectOwner -> getGuid(), 'body' => $action_body));
            } 
            else 
            {
                $action = $activityApi -> addActivity($viewer, $subject, 'yncomment_' . $subject -> getType(), $action_body, array('owner' => $subjectOwner -> getGuid(), 'body' => $action_body));
            }

            //TRY TO ATTACH IF NECESSARY
            if ($action && $attachment) 
            {
                $activityApi -> attachActivity($action, $attachment);
            }
           
            if (!empty($tagsArray)) 
            {
                $type_name = Zend_Registry::get('Zend_Translate') -> translate('comment');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'comment';
                }
                $tagSent = array();
                foreach ($tagsArray as $key => $tagStrValue) 
                {
                    if(in_array($key, $tagSent))
                    {
                        continue;
                    }
                    $tag = Engine_Api::_() -> getItemByGuid($key);
                    $tagSent[] = $key;
                    if ($comment && $tag && ($tag instanceof User_Model_User) && !$tag -> isSelf($viewer)) 
                    {
                        $notifyApi -> addNotification($tag, $viewer, $comment, 'tagged', array('object_type_name' => $type_name, 'label' => $type_name, ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) 
                    {
                        $subject_title = $viewer -> getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate') -> translate($tag -> getShortType());
                        $item_title = $tag -> getTitle();
                        $owner = $tag -> getOwner();
                        if ($comment && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                            $notifyApi -> addNotification($owner, $viewer, $comment, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_() -> getItem('user', $offices -> child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                                    $notifyApi -> addNotification($owner, $viewer, $comment, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                                }
                            }
                        }
                    }
                }
                if($action)
                {
                    $data = array_merge((array)$action -> params, array('tags' => $tagsArray));
                }
                else {
                    $data = array('tags' => $tagsArray);
                }
                $comment -> params = Zend_Json::encode($data);
                $comment -> save();
            }
            
            // Update attachment
            if ($attachment) {
                if (isset($comment -> attachment_type))
                    $comment -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($comment -> attachment_id))
                    $comment -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $comment -> save();
            }

            // Notifications
            // Add notification for owner (if user and not viewer)
            $this -> view -> subject = $subject -> getGuid();
            $this -> view -> owner = $subjectOwner -> getGuid();
            if ((strpos($subject -> getType(), "ynlisting") === false) || (strpos($subject -> getType(), "advgroup") === false) || (strpos($subject -> getType(), "ynevent") === false) || (strpos($subject -> getType(), "ynbusinesspages") === false)) {
                if ($subjectOwner -> getType() == 'user' && $subjectOwner -> getIdentity() != $viewer -> getIdentity()) 
                {
                    $notifyApi -> addNotification($subjectOwner, $viewer, $subject, 'commented', array('label' => $subject -> getShortType()));
                }
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach (Engine_Api::_()->getDbtable('comments', 'yncomment')->comments($subject)->getAllCommentsUsers() as $notifyUser) 
            {
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $subjectOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser -> getIdentity();
                $notifyApi -> addNotification($notifyUser, $viewer, $subject, 'commented_commented', array('label' => $subject -> getShortType()));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach (Engine_Api::_()->getDbtable('likes', 'yncomment')->likes($subject)->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser -> getIdentity() == $viewer -> getIdentity() || $notifyUser -> getIdentity() == $subjectOwner -> getIdentity())
                    continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser -> getIdentity(), $commentedUserNotifications))
                    continue;
                $notifyApi -> addNotification($notifyUser, $viewer, $subject, 'liked_commented', array('label' => $subject -> getShortType()));
            }

            // Increment comment count
            Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.comments');

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect('DESC');
        $this -> view -> commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect) -> getTotalItemCount();
        $this -> view -> status = true;
        $this -> view -> message = 'Comment added';
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', array('type' => $this -> _getParam('type'), 'id' => $this -> _getParam('id'), 'format' => 'html', 'page' => 0, 'parent_div' => 1, 'parent_comment_id' => $comment -> parent_comment_id, 'taggingContent' => $this -> _getParam('taggingContent'), 'showComposerOptions' => $this -> _getParam('showComposerOptions'), 'showAsNested' => $showAsNested, 'showAsLike' => $this -> _getParam('showAsLike'), 'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 'showSmilies' => $showSmilies, 'photoLightboxComment' => $photoLightboxComment, 'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

    public function deleteAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $autorizationApi = Engine_Api::_() -> authorization();
        $subject = $this -> getSubjectItem();
        // Comment id
        $comment_id = $this -> _getParam('comment_id');
        if (!$comment_id) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('No comment');
            return;
        }

        // Comment
        $comment = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        if (!$comment) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('No comment or wrong parent');
            return;
        }

        $poster = Engine_Api::_() -> getItem($comment -> poster_type, $comment -> poster_id);
        if (!$subject -> authorization() -> isAllowed($viewer, 'edit') && ($comment -> resource_type != $viewer -> getType() || $comment -> resource_id != $viewer -> getIdentity()) && !$poster -> isSelf($viewer)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Not allowed');
            return;
        }

        // Method
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            Engine_Api::_() -> getDbtable('comments', 'yncomment') -> removeReply($subject, $comment_id);
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect('DESC');
        $this -> view -> commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect) -> getTotalItemCount();
        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Comment deleted');
    }

    public function likeAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

            if (Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> isDislike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> removeDislike($commentedItem, $viewer);
			
			if (Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> isUnsure($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> removeUnsure($commentedItem, $viewer);

            if (!Engine_Api::_() -> getDbtable('likes', 'core') -> isLike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($commentedItem) -> addLike($viewer);

            // Add notification
            $owner = $commentedItem -> getOwner();
            $this -> view -> owner = $owner -> getGuid();
            if ($owner -> getType() == 'user' && $owner -> getIdentity() != $viewer -> getIdentity()) {
                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                $notifyApi -> addNotification($owner, $viewer, $commentedItem, 'liked', array('label' => $commentedItem -> getShortType()));
            }

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like added');
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment, 
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

	public function undolikeAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

            if (Engine_Api::_() -> getDbtable('likes', 'core') -> isLike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('likes', 'core') -> removeLike($commentedItem, $viewer);

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like removed');
        $this -> view -> taggingContent = $taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', array('type' => $type, 'id' => $id, 'format' => 'html', 'parent_comment_id' => $parent_comment_id, 'page' => $this -> view -> page, 'parent_div' => 1, 'taggingContent' => $taggingContent, 'showComposerOptions' => $showComposerOptions, 'showAsNested' => $showAsNested, 'showAsLike' => $showAsLike, 'showDislikeUsers' => $showDislikeUsers, 'showLikeWithoutIcon' => $showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies, 'showSmilies' => $showSmilies, 'photoLightboxComment' => $photoLightboxComment, 'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

	public function unsureAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> unsures($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

			if (Engine_Api::_() -> getDbtable('likes', 'core') -> isLike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('likes', 'core') -> removeLike($commentedItem, $viewer);
			
			if (Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> isDislike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> removeDislike($commentedItem, $viewer);
			
            if (!Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> isUnsure($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> addUnsure($commentedItem, $viewer);

            // Add notification
            $owner = $commentedItem -> getOwner();
            $this -> view -> owner = $owner -> getGuid();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like added');
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment, 
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

	public function undounsureAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> unsures($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

            if (Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> isUnsure($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> removeUnsure($commentedItem, $viewer);

            // Add notification
            $owner = $commentedItem -> getOwner();
            $this -> view -> owner = $owner -> getGuid();
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like added');
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment, 
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

    public function unlikeAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

            if (Engine_Api::_() -> getDbtable('likes', 'core') -> isLike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('likes', 'core') -> removeLike($commentedItem, $viewer);
			
			if (Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> isUnsure($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('unsures', 'yncomment') -> removeUnsure($commentedItem, $viewer);

            if (!Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> isDislike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> addDislike($commentedItem, $viewer);

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like removed');
        $this -> view -> taggingContent = $taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', array('type' => $type, 'id' => $id, 'format' => 'html', 'parent_comment_id' => $parent_comment_id, 'page' => $this -> view -> page, 'parent_div' => 1, 'taggingContent' => $taggingContent, 'showComposerOptions' => $showComposerOptions, 'showAsNested' => $showAsNested, 'showAsLike' => $showAsLike, 'showDislikeUsers' => $showDislikeUsers, 'showLikeWithoutIcon' => $showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies, 'showSmilies' => $showSmilies, 'photoLightboxComment' => $photoLightboxComment, 'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }
	public function undounlikeAction() {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'comment') -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($commentedItem) -> getAdapter();
        $db -> beginTransaction();

        try {

            if (Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> isDislike($commentedItem, $viewer))
                Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> removeDislike($commentedItem, $viewer);

            // Add notification
            $owner = $commentedItem -> getOwner();
            $this -> view -> owner = $owner -> getGuid();

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }

        $this -> view -> status = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Like added');
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment, 
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

    public function getSubjectItem() {
        $type = $this -> _getParam('type');
        $identity = $this -> _getParam('id');
        if ($type && $identity)
            return $subject = Engine_Api::_() -> getItem($type, $identity);
    }

    public function editAction() 
    {
        $this -> view -> comment = Engine_Api::_() -> getItem('core_comment', $this -> _getParam('comment_id'));
        $this -> view -> taggingContent = $taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested', 1);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> viewer_id = $viewer_id = $viewer -> getIdentity();
        $this -> view -> subject = $subject = $this -> getSubjectItem();
        $subjectParent = $subject;

        // Perms
        $this -> view -> canComment = $canComment = $subject -> authorization() -> isAllowed($viewer, 'comment');
        $this -> view -> canEdit = $canDelete = $subject -> authorization() -> isAllowed($viewer, 'edit');
        $autorizationApi = Engine_Api::_() -> authorization();
        $this -> view -> nestedCommentPressEnter = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncomment.comment.pressenter');

        // Likes
        $this -> view -> viewAllLikes = $this -> _getParam('viewAllLikes', false);
        $this -> view -> viewAllDislikes = $this -> _getParam('viewAllDislikes', false);
        $this -> view -> likes = $likes = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($subject) -> getLikePaginator();
        $this -> view -> dislikes = $dislikes = Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> getDislikePaginator($subject);
        $this -> view -> parent_comment_id = $parent_comment_id = $this -> _getParam('parent_comment_id', 0);

        $this -> view -> parent_div = $parent_div = $this -> _getParam('parent_div', 0);
        $this -> view -> format = $this -> _getParam('format');
        $this -> view -> order = $order = $this -> _getParam('order', 'DESC');
        if (empty($parent_comment_id)) {
            $commentCountSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect($order);

            if (!$showAsNested) {
                $commentCountSelect -> where('parent_comment_id =?', 0);
            }
            $this -> view -> commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect) -> getTotalItemCount();
        }

        if ($parent_comment_id) {
            $comment_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncomment.reply.per.page', 4);
        } else {
            $comment_per_page = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncomment.comment.per.page', 10);
        }

        // Comments
        // If has a page, display oldest to newest
        if (0 !== ($page = $this -> _getParam('page', 0))) {
            $commentSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect($order);
            $commentSelect -> where('parent_comment_id =?', $parent_comment_id);
            $commentSelect -> order("comment_id $order");
            $comments = Zend_Paginator::factory($commentSelect);
            $comments -> setCurrentPageNumber($page + 1);
            $comments -> setItemCountPerPage($comment_per_page);
            $this -> view -> comments = $comments;
            $this -> view -> page = $page;
        }
        // If not has a page, show the
        else {
            $commentSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect($order);
            $commentSelect -> where('parent_comment_id =?', $parent_comment_id);
            $commentSelect -> order("comment_id $order");
            $comments = Zend_Paginator::factory($commentSelect);
            $comments -> setCurrentPageNumber(1);
            $comments -> setItemCountPerPage($comment_per_page);
            $this -> view -> comments = $comments;
            $this -> view -> page = $page;
        }

        $this -> view -> nested_comment_id = $subject -> getGuid() . "_" . $parent_comment_id;

        if ($viewer -> getIdentity() && $canComment) {
            $this -> view -> formComment = $form = new Yncomment_Form_Comment_Create( array('textareaId' => $this -> view -> nested_comment_id));
            if ($parent_comment_id) {
                $form -> getElement('submit') -> setLabel('Post Reply');
            }

            $form -> populate(array('identity' => $subject -> getIdentity(), 'type' => $subject -> getType(), 'format' => 'html', 'parent_comment_id' => $parent_comment_id, 'taggingContent' => $taggingContent, 'showComposerOptions' => $showComposerOptions, 'showAsNested' => $showAsNested, 'showAsLike' => $showAsLike, 'showDislikeUsers' => $showDislikeUsers, 'showLikeWithoutIcon' => $showLikeWithoutIcon, 'showLikeWithoutIconInReplies' => $showLikeWithoutIconInReplies, 'showSmilies' => $showSmilies, 'photoLightboxComment' => $photoLightboxComment, 'commentsorder' => $commentsorder));
        }
    }

    public function updateAction() {

        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $subject = $this -> getSubjectItem();
        $subjectParent = $subject;

        $viewer_id = $viewer -> getIdentity();
        $listingtypeName = "";
        $autorizationApi = Engine_Api::_() -> authorization();
        $this -> view -> form = $form = new Yncomment_Form_Comment_Create( array('textareaId' => $subject -> getGuid() . "_0"));

        if (!$this -> getRequest() -> isPost()) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid request method");
            return;
        }
        
        $params = $this -> _getAllParams();
        $body = $params['body'];
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        $params['body'] = $body;
        if (!$form -> isValid($params)) {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Invalid data");
            return;
        }

        // Process
        // Filter HTML
        $filter = new Zend_Filter();
        $filter -> addFilter(new Engine_Filter_Censor());
        $filter -> addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form -> getValue('body');
        // fix SE enable link issue
        $body = str_replace('&amp;quot;"', '"', $body);
        $body = str_replace('&quot;</a>', '</a>"', $body);
            
        $body = preg_replace('/<br[^<>]*>/', "\n", $body);
        
        $db = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentTable() -> getAdapter();
        $db -> beginTransaction();

        try {
            $comment = Engine_Api::_() -> getItem('core_comment', $form -> getValue('comment_id'));
            $comment -> body = $body;
            $comment -> save();
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $subjectOwner = $subject -> getOwner('user');

            //TRY ATTACHMENT GETTING STUFF
            $attachment = null;
            $attachmentData = $this -> getRequest() -> getParam('attachment');
            
            $showComposerOptions = $this -> _getParam('showComposerOptions');
            $arr_showComposerOptions = array();
            if ($showComposerOptions) {
                $arr_showComposerOptions = explode(',', $showComposerOptions);
            }

            if (!$attachmentData && ($comment -> attachment_type)) {
                $attachment = Engine_Api::_() -> getItem($comment -> attachment_type, $comment -> attachment_id);
                $attachment -> delete();
                $comment -> attachment_type = '';
                $comment -> attachment_id = 0;
                $comment -> save();
            }

            if (!empty($attachmentData) && !empty($attachmentData['type'])) 
            {
                if (isset($attachmentData['type']) && $attachmentData['type'] == 'photo' && isset($attachmentData['photo_id']))
                {
                    if (Engine_Api::_()->hasModuleBootstrap('advalbum'))
                        $attachment = Engine_Api::_()->getItem('advalbum_photo', $attachmentData['photo_id']);
                    else
                        $attachment = Engine_Api::_()->getItem('album_photo', $attachmentData['photo_id']);
                }

                if (isset($attachmentData['type']) && $attachmentData['type'] == 'link') 
                {
                    $viewer = Engine_Api::_() -> user() -> getViewer();
                    if (Engine_Api::_() -> core() -> hasSubject()) {
                        $subject = Engine_Api::_() -> core() -> getSubject();
                        if ($subject -> getType() != 'user') {
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

                    $attachment = Engine_Api::_() -> getApi('links', 'core') -> createLink($viewer, $attachmentData);
                }
            }
            // check body exist link
            else if (in_array('addLink', $arr_showComposerOptions) && Engine_Api::_()->authorization()->isAllowed('core_link', null, 'create')) {
                $body_decode = html_entity_decode($body);
                $body_decode = html_entity_decode($body_decode);
                $body_decode = html_entity_decode($body_decode);
                $regex = '/http(s)?:\/\/([^" ]*)/mi';
                preg_match_all($regex, $body, $matches);
                if (count($matches) > 0) {
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

            $composerDatas = $this -> getRequest() -> getParam('composer', null);
            $tagsArray = array();
            parse_str($composerDatas['tag'], $tagsArray);
            
            $action_body = $body;
            if (!empty($tagsArray)) 
            {
                $action_body = $this -> _stringTagToObject($action_body, $tagsArray);
            }
            $action_body = $this -> _smileyToEmoticons($action_body);
            
            // Add Activity
            if (empty($comment -> parent_comment_id)) 
            {
                $action = $activityApi -> addActivity($viewer, $subject, 'comment_' . $subject -> getType(), $action_body, array('owner' => $subjectOwner -> getGuid(), 'body' => $action_body));
            } 
            else 
            {
                $action = $activityApi -> addActivity($viewer, $subject, 'yncomment_' . $subject -> getType(), $action_body, array('owner' => $subjectOwner -> getGuid(), 'body' => $action_body));
            }

            //TRY TO ATTACH IF NECESSARY
            if ($action && $attachment) {
                $activityApi -> attachActivity($action, $attachment);
            }
            if (!empty($tagsArray)) {

                $viewer = Engine_Api::_() -> _() -> user() -> getViewer();
                $type_name = Zend_Registry::get('Zend_Translate') -> translate('comment');
                if (is_array($type_name)) {
                    $type_name = $type_name[0];
                } else {
                    $type_name = 'comment';
                }
                foreach ($tagsArray as $key => $tagStrValue) {
                    $tag = Engine_Api::_() -> getItemByGuid($key);
                    if ($comment && $tag && ($tag instanceof User_Model_User) && !$tag -> isSelf($viewer)) {
                        $notifyApi -> addNotification($tag, $viewer, $comment, 'tagged', array('object_type_name' => $type_name, 'label' => $type_name, ));
                    } else if ($tag && ($tag instanceof Core_Model_Item_Abstract)) {
                        $subject_title = $viewer -> getTitle();
                        $item_type = Zend_Registry::get('Zend_Translate') -> translate($tag -> getShortType());
                        $item_title = $tag -> getTitle();
                        $owner = $tag -> getOwner();
                        if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                            $notifyApi -> addNotification($owner, $viewer, $comment, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                        }
                        if (($tag instanceof Group_Model_Group)) {
                            foreach ($tag->getOfficerList()->getAll() as $offices) {
                                $owner = Engine_Api::_() -> getItem('user', $offices -> child_id);
                                if ($action && $owner && ($owner instanceof User_Model_User) && !$owner -> isSelf($viewer)) {
                                    $notifyApi -> addNotification($owner, $viewer, $comment, 'yncomment_tagged', array('subject_title' => $subject_title, 'label' => $type_name, 'object_type_name' => $type_name, 'item_title' => $item_title, 'item_type' => $item_type));
                                }
                            }
                        }
                    }
                }
                if($action)
                {
                    $data = array_merge((array)$action -> params, array('tags' => $tagsArray));
                }
                else {
                    $data = array('tags' => $tagsArray);
                }
                $comment -> params = Zend_Json::encode($data);
                $comment -> save();
            }

            if ($attachment) {
                if (isset($comment -> attachment_type))
                    $comment -> attachment_type = ($attachment ? $attachment -> getType() : '');
                if (isset($comment -> attachment_id))
                    $comment -> attachment_id = ($attachment ? $attachment -> getIdentity() : 0);
                $comment -> save();
            }

            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
        $commentCountSelect = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getCommentSelect('DESC');
        $this -> view -> commentsCount = $commentsCount = Zend_Paginator::factory($commentCountSelect) -> getTotalItemCount();
        $this -> view -> status = true;
        $this -> view -> message = 'Comment updated';
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
            array(
                'identity' => $subject -> getIdentity(), 
                'type' => $subject -> getType(), 
                'format' => 'html', 
                'parent_comment_id' => $comment -> parent_comment_id, 
                'page' => 0, 'parent_div' => 1, 
                'taggingContent' => $this -> _getParam('taggingContent'), 
                'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                'showAsNested' => $this -> _getParam('showAsNested'), 
                'showAsLike' => $this -> _getParam('showAsLike'), 
                'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                'showSmilies' => $showSmilies, 
                'photoLightboxComment' => $photoLightboxComment, 
                'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }

    public function hideItemAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $this -> view -> type = $type = $this -> _getParam('type', null);
        $this -> view -> id = $id = $this -> _getParam('id', null);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        if (empty($type) || empty($id))
            return;
        $this -> view -> status = true;
        Engine_Api::_() -> getDbtable('hide', 'yncomment') -> insert(array('user_id' => $viewer_id, 'hide_resource_type' => $type, 'hide_resource_id' => $id));
    }

    public function unHideItemAction() {
        if (!$this -> _helper -> requireUser() -> isValid())
            return;

        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $hideTable = Engine_Api::_() -> getDbtable('hide', 'yncomment');
        $type = $this -> _getParam('type', null);
        $id = $this -> _getParam('id', null);
        if (empty($type) || empty($id))
            return;
        $this -> view -> status = true;
        $hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $type, 'hide_resource_id =?' => $id));
    }
    
    public function openHideCommentAction() 
    {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        $subject = $this -> getSubjectItem();
         // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }
        $this -> view -> status = true;
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');
        $this -> view -> openHide = $openHide = $comment_id;

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment,
                    'openHide' => $openHide, 
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
    }
    public function unHideCommentAction() 
    {
        if (!$this -> _helper -> requireUser() -> isValid()) {
            return;
        }
        $comment_id = $this -> _getParam('comment_id');
        $parent_comment_id = $this -> _getParam('parent_comment_id');
        $subject = $this -> getSubjectItem();
        
        $comment = Engine_Api::_() -> getDbtable('comments', 'yncomment') -> comments($subject) -> getComment($comment_id);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $viewer_id = $viewer -> getIdentity();
        $hideTable = Engine_Api::_() -> getDbtable('hide', 'yncomment');
        $this -> view -> status = false;
        if (!$comment)
            return;
        $hideTable -> delete(array('user_id = ?' => $viewer_id, 'hide_resource_type =? ' => $comment -> getType(), 'hide_resource_id =?' => $comment -> getIdentity()));
       
        
         // For comments, render the resource
        if ($subject -> getType() == 'core_comment') {
            $type = $subject -> resource_type;
            $id = $subject -> resource_id;
            Engine_Api::_() -> core() -> clearSubject();
        } else {
            $type = $subject -> getType();
            $id = $subject -> getIdentity();
        }
        
        $this -> view -> status = true;
        $this -> view -> taggingContent = $this -> _getParam('taggingContent');
        $this -> view -> showComposerOptions = $this -> _getParam('showComposerOptions');
        $this -> view -> showAsNested = $this -> _getParam('showAsNested');
        $this -> view -> page = $this -> _getParam('page', 0);
        $this -> view -> showAsLike = $showAsLike = $this -> _getParam('showAsLike', 1);
        $this -> view -> showDislikeUsers = $showDislikeUsers = $this -> _getParam('showDislikeUsers', 1);
        $this -> view -> showLikeWithoutIcon = $showLikeWithoutIcon = $this -> _getParam('showLikeWithoutIcon', 1);
        $this -> view -> showLikeWithoutIconInReplies = $showLikeWithoutIconInReplies = $this -> _getParam('showLikeWithoutIconInReplies', 1);
        $this -> view -> showSmilies = $showSmilies = $this -> _getParam('showSmilies');
        $this -> view -> photoLightboxComment = $photoLightboxComment = 0;//$this -> _getParam('photoLightboxComment');
        $this -> view -> commentsorder = $commentsorder = $this -> _getParam('commentsorder');

        $this -> view -> body = $this -> view -> action('list', 'comment', 'yncomment', 
                array('type' => $type, 
                    'id' => $id, 
                    'format' => 'html', 
                    'parent_comment_id' => $parent_comment_id, 
                    'page' => $this -> view -> page, 
                    'parent_div' => 1, 
                    'taggingContent' => $this -> _getParam('taggingContent'), 
                    'showComposerOptions' => $this -> _getParam('showComposerOptions'), 
                    'showAsNested' => $this -> _getParam('showAsNested'), 
                    'showAsLike' => $this -> _getParam('showAsLike'), 
                    'showDislikeUsers' => $this -> _getParam('showDislikeUsers'), 
                    'showLikeWithoutIcon' => $this -> _getParam('showLikeWithoutIcon'), 
                    'showLikeWithoutIconInReplies' => $this -> _getParam('showLikeWithoutIconInReplies'), 
                    'showSmilies' => $showSmilies, 
                    'photoLightboxComment' => $photoLightboxComment,
                    'commentsorder' => $commentsorder));
        $this -> _helper -> contextSwitch -> initContext();
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
    protected function _stringTagToObject($content, $tags) 
    {
        foreach ($tags as $key => $tagStrValue) {
            $tag = Engine_Api::_() -> getItemByGuid($key);
            if (!$tag) {
                continue;
            }
            $replaceStr = '<a ' . 'href="' . $tag -> getHref() . '" ' . 'rel="' . $tag -> getType() . ' ' . $tag -> getIdentity() . '" >' . $tag -> getTitle() . '</a>';
            $content = preg_replace("/" . preg_quote($tagStrValue) . "/", $replaceStr, $content);
        }
        return $content;
    }
    protected function _smileyToEmoticons($string) 
    {
        $view = Zend_Registry::get('Zend_View');
        $baseUrl = $view -> layout() -> staticBaseUrl;
        foreach (Engine_Api::_() -> yncomment() -> getEmoticons() as $emoticon) 
        {
            $string = str_replace($emoticon -> text, "<img class='emotions_use' title = '{$view -> translate(ucwords($emoticon -> title))}' src='{$baseUrl}/application/modules/Yncomment/externals/images/emoticons/{$emoticon -> image}'/>", $string);
        }
        return ($view -> BBCode($string, array('link_no_preparse' => true)));
    }
}
