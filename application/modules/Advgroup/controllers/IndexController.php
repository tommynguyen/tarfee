<?php
class Advgroup_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    // Set group as subject if it exists.
    if( !$this->_helper->requireAuth()->setAuthParams('group', null, 'view')->isValid() ){
        return;
    }
    $id = $this->_getParam('group_id', $this->_getParam('id', null));
    if( $id ) {
      $group = Engine_Api::_()->getItem('group', $id);
      if( $group ) {
        Engine_Api::_()->core()->setSubject($group);
      }
    }
  }
  
  public function followAction() 
  {
  		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
        $group = Engine_Api::_() -> getItem('group', $this ->_getParam('group_id'));
        $viewer = Engine_Api::_()->user()->getViewer();
        $followTable = Engine_Api::_()->getDbTable('follow', 'advgroup');
        $row = $followTable->getFollowGroup($group->getIdentity(), $viewer->getIdentity());
        $this->view->viewer_id = $viewer->getIdentity();
        if ($row) 
        {
            $this->view->follow = $row->follow;
        } 
        else if($this->getRequest()->isPost())
		{
			$row = $followTable->createRow();
		    $row->resource_id = $group->getIdentity();
		    $row->user_id = $viewer->getIdentity();
		}
		else
        {
            return $this->_helper->viewRenderer->setNoRender(true);
        }
        $option_id = $this->getRequest()->getParam('option_id');
        $row->follow = $option_id;
        $row->save();
  }
  
  public function browseAction()
  {
    // Setting to use landing page.
    $this->_helper->content->setNoRender()->setEnabled();
  }

  public function createAction()
  {
    // Return if guest try to access to create link.
    if( !$this->_helper->requireUser->isValid() ) return;
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Check authorization to create group.
    if( !$this->_helper->requireAuth()->setAuthParams('group', null, 'create')->isValid() ) return;
	
    // Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('advgroup_main');
    
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;   
	
	//check user have only one clubs
	$checkGroupUser = Engine_Api::_() -> advgroup() -> checkGroupUser();
	if($checkGroupUser) {
		//already have club
		$this -> view -> errorMessage = $this -> view -> translate('You are already have club.');
		return;
	}
	
	
    // Create Form
     if($this->_getParam('parent') && is_numeric($this->_getParam('parent'))){
        $this->view->form = $form = new Advgroup_Form_Create(array('parent_id'=> $this->_getParam('parent')));
     }
     else{
      $this->view->form = $form = new Advgroup_Form_Create();
     }
    
	
    if($this->_getParam('parent')){
      $form->removeElement('auth_sub_group');
      $parent_id = $this->_getParam('parent');
      if(is_numeric($parent_id)){
        $parent_group = Engine_Api::_()->advgroup()->getParent($parent_id);
        if(!$parent_group || $parent_group->is_subgroup){
            return $this->_helper->requireSubject->forward();
        }
        else if(!$parent_group->membership()->isMember($viewer)|| !$parent_group->authorization()->isAllowed(null,'sub_group')){
            return $this->_helper->requireAuth->forward();
        }
        $subGroupNum = Engine_Api::_()->advgroup()->getNumberValue('group',$viewer->level_id,'numberSubgroup');
        if($parent_group->countSubGroups()>= $subGroupNum){
            $this -> view -> errorMessage = $this -> view -> translate("You have reached the limit of sub group.");
            return;
        }
      }
    }

    $arrPlugins = array('ynvideo' => 'auth_video', 'ynwiki' => 'auth_wiki', 'ynevent' => 'auth_event', 'ynlistings' => 'auth_listing');
    foreach($arrPlugins as $key => $permission)
    {
        if(!Engine_Api::_()->advgroup()->checkYouNetPlugin($key)){
            $form->removeElement($permission);
        }
    }
    
    if(!Engine_Api::_()->advgroup()->checkYouNetPlugin('ynfilesharing')){
            $form->removeElement('auth_folder');
            $form->removeElement('auth_file_upload');
            $form->removeElement('auth_file_down');
    }

    $music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('music');
    $mp3music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('mp3music');      
    if (!$music_enable)
        {
            if (!$mp3music_enable)
            {
                $form->removeElement('auth_music');
            }
        }
        
    // Populate category list.
    $categories = Engine_Api::_()->getDbtable('categories', 'advgroup')->getAllCategoriesAssoc();
    $form->category_id->setMultiOptions($categories);

    if( count($form->category_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('category_id');
    }
	
	// Populate sport list.
	$tableCategory = Engine_Api::_() -> getItemTable('user_sportcategory');
	$categories = $tableCategory -> getCategoriesLevel1();
	foreach ($categories as $item) {
		$form -> sportcategory_id -> addMultiOption($item['sportcategory_id'], $item['title']);
	}
	
	if( count($form->sportcategory_id->getMultiOptions()) <= 1 ) {
      $form->removeElement('sportcategory_id');
    }
	
    // Check method and data validity.
    if( !$this->getRequest()->isPost() ) {
      return;
    }
	
	// Location
	$_post =  $this -> getRequest() -> getPost();
	$provincesAssoc = array();
	$country_id = $_post['country_id'];
	if ($country_id) {
		$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
		$provincesAssoc = array('0'=>'') + $provincesAssoc;
	}
	$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
	
	$citiesAssoc = array();
	if(isset($_post['province_id']))
	{
		$province_id = $_post['province_id'];
		if ($province_id) {
			$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
			$citiesAssoc = array('0'=>'') + $citiesAssoc;
		}
		$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
	}
		
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    
    $values['location'] = Zend_Json::encode(array(
        'location' => $values['location_address'],
        'latitude' => $values['lat'],
        'longitude' => $values['long'],
    ));     
    $values['latitude'] = $values['lat'];
    $values['longitude'] = $values['long'];
    $values['user_id'] = $viewer->getIdentity();
    if($this->_getParam('parent') && is_numeric($this->_getParam('parent'))){
      $values['is_subgroup'] = 1;
      $values['parent_id'] = $this->_getParam('parent');
    }
    $db = Engine_Api::_()->getDbtable('groups', 'advgroup')->getAdapter();
    $db->beginTransaction();

    try {
      // Create group
      $table = Engine_Api::_()->getDbtable('groups', 'advgroup');
      $group = $table->createRow();
      $group->setFromArray($values);
      $group->save();

      // Add tags
      $tags = preg_split('/[,]+/', $values['tags']);
      $group->tags()->addTagMaps($viewer, $tags);

      $search_table = Engine_Api::_()->getDbTable('search', 'core');
      $select = $search_table->select()
                      ->where('type = ?','group')
                      ->where('id = ?',$group->getIdentity());
      $row = $search_table->fetchRow($select);
      if($row)
      {
        $row->keywords = $values['tags'];
        $row->save();
      }
      else
      {
        $row = $search_table->createRow();
        $row->type = 'group';
        $row->id = $group->getIdentity();
        $row->title = $group->title;
        $row->description = $group->description;
        $row->keywords = $values['tags'];
        $row->save();
      }

      // Add owner as member
      $group->membership()->addMember($viewer)
          ->setUserApproved($viewer)
          ->setResourceApproved($viewer);

      // Set photo
      if( !empty($values['photo']) ) {
        $group->setPhoto($form->photo);
      }

      // Add Cover photo
    if (!empty($values['cover_thumb'])) {
        $group -> setCoverPhoto($form -> cover_thumb);
    }
	 /*
      // Add fields
      $customfieldform = $form->getSubForm('fields');
      $customfieldform->setItem($group);
      $customfieldform->saveValues();
      
	 */
      // Process privacy
      $auth = Engine_Api::_()->authorization()->context;

      $roles = array(
                'owner',
                'officer',
                'member',
                'registered',
                'everyone'
            );

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }

      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'registered';
      }

      if( empty($values['auth_poll']) ) {
        $values['auth_poll'] = 'member';
      }

      if( empty($values['auth_event']) ) {
        $values['auth_event'] = 'registered';
      }

      if( empty($values['auth_sub_group'])){
        $values['auth_sub_group'] = 'member';
      }

      if( empty($values['auth_video'])){
        $values['auth_video'] = 'member';
      }

      if( empty($values['auth_wiki'])){
        $values['auth_wiki'] = 'member';
      }
      
      if( empty($values['auth_music'])){
        $values['auth_music'] = 'member';
      }
      
      if( empty($values['auth_listing'])){
        $values['auth_listing'] = 'member';
      }
          
      if( empty($values['auth_folder'])){
        $values['auth_folder'] = 'member';
      }

      if( empty($values['auth_file_upload'])){
        $values['auth_file_upload'] = 'member';
      }
    
      if( empty($values['auth_file_down'])){
        $values['auth_file_down'] = 'member';
      }

      
      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);
      $photoMax = array_search($values['auth_photo'], $roles);
      $groupMax = array_search($values['auth_event'], $roles);
      $pollMax = array_search($values['auth_poll'],$roles);
      $inviteMax = array_search($values['auth_invite'], $roles);
      $subGroupMax = array_search($values['auth_sub_group'], $roles);
      $videoMax = array_search($values['auth_video'], $roles);
      $wikiMax = array_search($values['auth_wiki'], $roles);
      $musicMax = array_search($values['auth_music'], $roles);
      $folderMax = array_search($values['auth_folder'], $roles);
      $fileuploadMax = array_search($values['auth_file_upload'], $roles);
      $filedownloadMax = array_search($values['auth_file_down'], $roles);
      $listingMax = array_search($values['auth_listing'], $roles);
    
      $officerList = $group->getOfficerList();

      foreach( $roles as $i => $role ) {
        if( $role === 'officer' ) {
          $role = $officerList;
        }
        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
        $auth->setAllowed($group, $role, 'event', ($i <= $groupMax));
        $auth->setAllowed($group, $role, 'poll', ($i <= $pollMax));
        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
        $auth->setAllowed($group, $role, 'sub_group', ($i <= $subGroupMax));
        $auth->setAllowed($group, $role, 'video', ($i <= $videoMax));
        $auth->setAllowed($group, $role, 'wiki', ($i <= $wikiMax));
        $auth->setAllowed($group, $role, 'music', ($i <= $musicMax));
        $auth->setAllowed($group, $role, 'folder', ($i <= $folderMax));
        $auth->setAllowed($group, $role, 'file_upload', ($i <= $fileuploadMax));
        $auth->setAllowed($group, $role, 'file_down', ($i <= $filedownloadMax));
        $auth->setAllowed($group, $role, 'listing', ($i <= $listingMax));
      }

      // Create some auth stuff for all officers
      $auth->setAllowed($group, $officerList, 'edit', 1);
      $auth->setAllowed($group, $officerList, 'style', 1);
      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
      $auth->setAllowed($group, $officerList, 'announcement', 1);
      $auth->setAllowed($group, $officerList, 'sponsor', 1);
      $auth->setAllowed($group, $officerList, 'invitation', 1);
      $auth->setAllowed($group, $officerList, 'file.edit', 1);
      $auth->setAllowed($group, $officerList, 'member.edit', 1);
      $auth->setAllowed($group, $officerList, 'topic.edit', 1);
      $auth->setAllowed($group, $officerList, 'poll.edit', 1);
      
      
      // Add auth for invited users
      $auth->setAllowed($group, 'member_requested', 'view', 1);

      // Add action
      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');

      if($group->is_subgroup){
          $parent_group = $group->getParentGroup();
          $action = $activityApi->addActivity($viewer, $parent_group, 'advgroup_sub_create');
      }
      else {
          $action = $activityApi->addActivity($viewer, $group, 'advgroup_create');
      }
      if( $action ) {
        $activityApi->attachActivity($action, $group);
      }
      // Commit
      $db->commit();

      // Redirect
      return $this->_helper->redirector->gotoRoute(array('id' => $group->getIdentity()), 'group_profile', true);
    } catch( Engine_Image_Exception $e ) {
      $db->rollBack();
      $form->addError(Zend_Registry::get('Zend_Translate')->_('The image you selected was too large.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  public function listingAction(){
    // Setting to use landing page.
    $this->_helper->content->setNoRender()->setEnabled();
  }

  public function manageAction()
  {
    $this->_helper->content
        //->setNoRender()
        ->setEnabled()
    ;
    //Require User 
    if(!$this->_helper->requireUser->isValid()) return;
    
    //Get Main and Quick Navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
          ->getNavigation('advgroup_main');
    
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
         ->getNavigation('advgroup_quick');

    //Create & modify search form.
    $this->view->form = $search_form = new Advgroup_Form_Search();
    $search_form->removeElement('view');
    $search_form->addElement('Select', 'view', array(
      'label' => 'View:',
      'multiOptions' => array(
        '0' => 'All My Clubs',
        '2' => 'Only Clubs I Lead',
      ),
      'onchange' => '$(this).getParent("form").submit();',
    ));
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $search_form->isValid($request->getParams());
    $params = $search_form->getValues();
    
    //Get form values
    $this->view->formValues = $params = $search_form->getValues();
    
    //Get viewer
    $viewer = Engine_Api::_()->user()->getViewer();

    //Filter parameters
    if($params['view'] != "2"){
        $memTable = Engine_Api::_()->getDbtable('membership', 'advgroup');
        $select = $memTable->select()
                    ->where('user_id = ?',$viewer->getIdentity())
                    ->where('active = 1');
        $memberships = $memTable->fetchAll($select);
        $group_ids = array(0);
        foreach($memberships as $membership) $group_ids[] = $membership->resource_id;
        $params['group_ids'] = $group_ids;
    }
    else {
        $params['user_id'] = $viewer->getIdentity();
    }

    //Get data
    $this->view->paginator = $paginator =  Engine_Api::_()->getItemTable('group')->getGroupPaginator($params);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $itemsPerPage = Engine_Api::_()->getApi('settings', 'core')->getSetting('advgroup.page', 10);
    $paginator->setItemCountPerPage($itemsPerPage);
        // Check create
    $this->view->canCreate = Engine_Api::_()->authorization()->isAllowed('group', null, 'create');
 }
 /**
  * Handles HTTP POST requests to create an activity feed item
  *
  * Uses the default route and can be accessed from
  *  - /activity/index/post
  *
  * If URL acccessed directly, the follwoing view script is use:
  *  - /Activity/views/scripts/index/post.tpl
  *
  * @return void
  */
 public function postAction()
 {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;
 
    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if( $subject_guid ) {
        $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if( null === $subject ) {
        $subject = $viewer;
    }
 
    // Make form
    $form = $this->view->form = new Activity_Form_Post();
 
    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
        return $this->_helper->requireAuth()->forward();
    }
 
    // Check if post
    if( !$this->getRequest()->isPost() ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
        return;
    }
     
    // Check token
    if( !($token = $this->_getParam('token')) ) {
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('No token, please try again');
        return;
    }
 
    $session = new Zend_Session_Namespace('ActivityFormToken');
 
    
    
    $session->unsetAll();
 
    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    $body = @$postData['body'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;
 
    if( !$form->isValid($postData) ) {
        $this->view->status = false;
        $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
        return;
    }
 
    // Check one more thing
    if( $form->body->getValue() === '' && $form->getValue('attachment_type') === '' ) {
        $this->view->status = false;
        $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
        return;
    }
 
    // set up action variable
    $action = null;
 
    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'activity')->getAdapter();
    $db->beginTransaction();
 
    try {
        // Try attachment getting stuff
        $attachment = null;
        $attachmentData = $this->getRequest()->getParam('attachment');
        if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
            $type = $attachmentData['type'];
            $config = null;
            foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
                if( !empty($data['composer'][$type]) ) {
                    $config = $data['composer'][$type];
                }
            }
            if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
                $config = null;
            }
            if( $config ) {
                $plugin = Engine_Api::_()->loadClass($config['plugin']);
                $method = 'onAttach'.ucfirst($type);
                $attachment = $plugin->$method($attachmentData);
            }
        }
 
 
        // Get body
        $body = $form->getValue('body');
        $body = preg_replace('/<br[^<>]*>/', "\n", $body);
 
        // Is double encoded because of design mode
        //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
        //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
 
        // Special case: status
        if( !$attachment && $viewer->isSelf($subject) ) {
            if( $body != '' ) {
                $viewer->status = $body;
                $viewer->status_date = date('Y-m-d H:i:s');
                $viewer->save();
 
                $viewer->status()->setStatus($body);
            }
 
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, 'status', $body);
 
        } else { // General post
 
            $type = 'post';
            if( $viewer->isSelf($subject) ) {
                $type = 'post_self';
            }
 
            // Add notification for <del>owner</del> user
            $subjectOwner = $subject->getOwner();
 
            if( !$viewer->isSelf($subject) &&
                    $subject instanceof User_Model_User ) {
                $notificationType = 'post_'.$subject->getType();
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
                'url1' => $subject->getHref(),
                ));
            }
 
            // Add activity
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $subject, $type, $body);
 
            // Try to attach if necessary
            if( $action && $attachment ) {
                Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $attachment);
            }
        }
 
 
 
        // Preprocess attachment parameters
        $publishMessage = html_entity_decode($form->getValue('body'));
        $publishUrl = null;
        $publishName = null;
        $publishDesc = null;
        $publishPicUrl = null;
        // Add attachment
        if( $attachment ) {
            $publishUrl = $attachment->getHref();
            $publishName = $attachment->getTitle();
            $publishDesc = $attachment->getDescription();
            if( empty($publishName) ) {
                $publishName = ucwords($attachment->getShortType());
            }
            if( ($tmpPicUrl = $attachment->getPhotoUrl()) ) {
                $publishPicUrl = $tmpPicUrl;
            }
            // prevents OAuthException: (#100) FBCDN image is not allowed in stream
            if( $publishPicUrl &&
                    preg_match('/fbcdn.net$/i', parse_url($publishPicUrl, PHP_URL_HOST)) ) {
                $publishPicUrl = null;
            }
        } else {
            $publishUrl = !$action ? null : $action->getHref();
        }
        // Check to ensure proto/host
        if( $publishUrl &&
                false === stripos($publishUrl, 'http://') &&
                false === stripos($publishUrl, 'https://') ) {
            $publishUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishUrl;
        }
        if( $publishPicUrl &&
                false === stripos($publishPicUrl, 'http://') &&
                false === stripos($publishPicUrl, 'https://') ) {
            $publishPicUrl = 'http://' . $_SERVER['HTTP_HOST'] . $publishPicUrl;
        }
        // Add site title
        if( $publishName ) {
            $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title
            . ": " . $publishName;
        } else {
            $publishName = Engine_Api::_()->getApi('settings', 'core')->core_general_site_title;
        }
 
 
 
        // Publish to facebook, if checked & enabled
        if( $this->_getParam('post_to_facebook', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
                try {
 
                    $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
                    $facebook = $facebookApi = $facebookTable->getApi();
                    $fb_uid = $facebookTable->find($viewer->getIdentity())->current();
 
                    if( $fb_uid &&
                            $fb_uid->facebook_uid &&
                            $facebookApi &&
                            $facebookApi->getUser() &&
                            $facebookApi->getUser() == $fb_uid->facebook_uid ) {
                        $fb_data = array(
                                'message' => $publishMessage,
                        );
                        if( $publishUrl ) {
                            $fb_data['link'] = $publishUrl;
                        }
                        if( $publishName ) {
                            $fb_data['name'] = $publishName;
                        }
                        if( $publishDesc ) {
                            $fb_data['description'] = $publishDesc;
                        }
                        if( $publishPicUrl ) {
                            $fb_data['picture'] = $publishPicUrl;
                        }
                        $res = $facebookApi->api('/me/feed', 'POST', $fb_data);
                    }
                } catch( Exception $e ) {
                    // Silence
                }
        } // end Facebook
 
        // Publish to twitter, if checked & enabled
        if( $this->_getParam('post_to_twitter', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_twitter_enable ) {
                try {
                    $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
                    if( $twitterTable->isConnected() ) {
                        // @todo truncation?
                        // @todo attachment
                        $twitter = $twitterTable->getApi();
                        $twitter->statuses->update($publishMessage);
                    }
                } catch( Exception $e ) {
                    // Silence
                }
        }
 
        // Publish to janrain
        if( //$this->_getParam('post_to_janrain', false) &&
                'publish' == Engine_Api::_()->getApi('settings', 'core')->core_janrain_enable ) {
                try {
                    $session = new Zend_Session_Namespace('JanrainActivity');
                    $session->unsetAll();
 
                    $session->message = $publishMessage;
                    $session->url = $publishUrl ? $publishUrl : 'http://' . $_SERVER['HTTP_HOST'] . _ENGINE_R_BASE;
                    $session->name = $publishName;
                    $session->desc = $publishDesc;
                    $session->picture = $publishPicUrl;
 
                } catch( Exception $e ) {
                    // Silence
                }
        }
 
        $db->commit();
    } catch( Exception $e ) {
        $db->rollBack();
        throw $e; // This should be caught by error handler
    }
 
 
 
    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');
 
    // Check if action was created
    $post_fail = "";
    if( !$action ){
        $post_fail = "?pf=1";
    }
 
    // Redirect if in normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
        $return_url = $form->getValue('return_url', false);
        if( $return_url ) {
            return $this->_helper->redirector->gotoUrl($return_url.$post_fail, array('prependBase' => false));
        }
    }
 }
    /**
     * Add location + check google map
     */
    public function getMyLocationAction()
    {   
        $latitude = $this->_getParam('latitude');
        $longitude = $this->_getParam('longitude');
        $values  =  file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
        echo $values;die;
    }
    
    //view map view
    public function displayMapViewAction()
    {
        
        $tab = $this->_getParam('tab','tab_events_popular');
        $itemCount = $this->_getParam('itemCount',5);
        $viewer = Engine_Api::_() -> user() -> getViewer();
        
        

        $table = Engine_Api::_()->getItemTable('group');
        switch ($tab) {
            case 'tab_groups_newest':
                
                $recentType = $this->_getParam('recentType', 'creation');
                if( !in_array($recentType, array('creation', 'modified')) ) {
                  $recentType = 'creation';
                }
                $this->view->recentType = $recentType;
                $this->view->recentCol = $recentCol = $recentType . '_date';
                
                // Get paginator
                $table = Engine_Api::_()->getItemTable('group');
                $select = $table->select()
                  ->where('search = ?', 1)
                  ->where("is_subgroup = ?",0) 
                  ;
                 
                if( $recentType == 'creation' ) {
                  // using primary should be much faster, so use that for creation
                  $select->order('group_id DESC');
                } else {
                  $select->order($recentCol . ' DESC');
                }
                $groups = $table->fetchAll($select);
                break;
            
            case 'tab_groups_popular':
                        
                $popularType = $this->_getParam('popularType', 'member');
                if( !in_array($popularType, array('view', 'member')) ) {
                  $popularType = 'member';
                }
                $this->view->popularType = $popularType;
                $this->view->popularCol = $popularCol = $popularType . '_count';
                
                // Get paginator
                $table = Engine_Api::_()->getItemTable('group');
                $select = $table->select()
                  ->where('search = ?', 1)
                  ->where("is_subgroup = ?",0)
                  ->order($popularCol . ' DESC');
    
                $groups = $table->fetchAll($select);                
                break;
            
            case 'tab_groups_active':
                    
                $time = $this->_getParam('time',1);
                if( !in_array($time, array(1, 2, 3)) ) {
                    $time = 1;
                }
            
                $date = date('Y-m-d H:i:s');
                switch($time){
                    case 1:
                        $newdate = strtotime ( '-30 day' , strtotime ($date)) ;
                        break;
                    case 2:
                        $newdate = strtotime ( '-60 day' , strtotime ($date)) ;
                        break;
                    case 3:
                        $newdate = strtotime ( '-90 day' , strtotime ($date)) ;
                }
                $newdate = date ( 'Y-m-d H:i:s' , $newdate );
                
                $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
                $topicName =$topicTable ->info('name');
                $groupTable = Engine_Api::_()->getItemTable('group');
                $groupName = $groupTable->info('name');
                
                $select = $groupTable->select()->from($groupName,array("$groupName.*","COUNT('topic_id') AS topic_count"))
                            ->setIntegrityCheck(false)
                            ->joinRight($topicName, "$topicName.group_id = $groupName.group_id","$topicName.topic_id")
                            ->where("$groupName.search = ?", 1)
                            ->where("$groupName.is_subgroup = ?",0)
                            ->where("$topicName.creation_date > ?",$newdate)
                            ->group("$groupName.group_id")
                            ->order("COUNT('topic_id') DESC")
                            ->limit($count);    
                $groups = $table->fetchAll($select);                
                break;  
        
        }
        
        $datas = array();
        $contents = array();
        $http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://' ;
        $icon_clock = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/advgroup-maps-time.png';
        $icon_persion = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/advgroup-maps-person.png';
        $icon_star = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/advgroup-maps-close-black.png';
        $icon_home = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/advgroup-maps-location.png';
        $icon_new = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/icon-New.png';
        $icon_guest = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/advgroup-maps-person.png';
        
        foreach($groups as $group)
        {           
            if($group -> latitude)  
            {               
                $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/maker.png';
                
                if($group->featured)
                {
                    $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/feature_maker.png';
                }
                else
                {
                    if(!$group->isNewGroup())
                    {
                        $icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Advgroup/externals/images/old-maker.png';
                    }
                }
                $datas[] = array(   
                        'group_id' => $group -> getIdentity(),              
                        'latitude' => $group -> latitude,
                        'longitude' => $group -> longitude,
                        'icon' => $icon
                    );
                if($group->isNewGroup())
                {
                    $new = "<img src='".$icon_new."' style='float: left; margin-right: 10px;'/>";
                }else{
                    $new = "";
                }
                $memicon = "<img src='".$icon_guest."' />";
                $contents[] = '
                    <div class="advgroup-maps-main" style="width: auto;">   
                        <div class="advgroup-maps-content" style="overflow: hidden; line-height: 20px;">
                            '.$new.'
                            <div style="overflow:hidden; float: left;">
                                <a href="'.$group->getHref().'" class="advgroup-maps-title" style="color: #679ac0; font-weight: bold; font-size: 14px; text-decoration: none; float: left; clear: both;" target="_parent">
                                    '.$group->getTitle().'
                                </a>
                                <div class="advgroup-maps-info" style="float: left; clear: both;">
                                    <span style="margin-right: 10px; font-size: 12px;">
                                        '.$memicon.'
                                        '.$this->view->translate(array("%s member", "%s member", $group->countGroupMembers()),$group->countGroupMembers()).'
                                    </span>                             
                                </div>                          
                            </div>
                        </div>
                    </div>
                ';
            }
        }

        echo $this ->view -> partial('_map_view.tpl', 'advgroup',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
        exit();
    }
    public function viewFanAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view-> club = $club = Engine_Api::_() -> getItem('group', $this -> _getParam('club_id', 0));
		if (!$club) {
			return $this -> _helper -> requireSubject() -> forward();
		}
	}

	public function moreInfoAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view-> group = $group = Engine_Api::_() -> getItem('group', $this -> _getParam('club_id', 0));
		if (!$group) {
			return $this -> _helper -> requireSubject() -> forward();
		}
	}
	
	public function importClubAction()
	{
		ignore_user_abort(true);
		set_time_limit(0);
		$path = APPLICATION_PATH. "/Clubs_Database.csv";
		$fh = fopen($path, "r");
		// read header of file
		$row = fgetcsv($fh, 0, ',');
		
		while (($row = fgetcsv($fh, 0, ',')) != false)
		{
			$db = Engine_Api::_()->getDbtable('groups', 'advgroup')->getAdapter();
		    $db->beginTransaction();
		
		    try {
		      // Create group
		      if($row[3] == 'England')
			  {
			  	$row[3] = 'United Kingdom';
			  }
		      $countryId = Engine_Api::_()->getDbtable('locations', 'user') -> getConuntyIdByName($row[3]);
			  $city = Engine_Api::_()->getDbtable('locations', 'user') -> getCityByName($row[1]);
		      $values = array(
			  'user_id' => 1,
			  'title' => $row[0],
			  'country_id' => $countryId
			  );
			  if($city)
			  {
			  	$values['city_id'] = $city -> location_id;
				$values['province_id'] = $city -> parent_id;
			  }
			  if($row[4])
			  {
			  	$values['establish_date'] = $row[4]."-01-01";
			  }
		      $table = Engine_Api::_()->getDbtable('groups', 'advgroup');
		      $group = $table->createRow();
		      $group->setFromArray($values);
		      $group->save();
				
			  $user = Engine_Api::_() -> getItem('user', 1);
		      // Add owner as member
		      $group->membership()->addMember($user)
		          ->setUserApproved($user)
		          ->setResourceApproved($user);
		
		      // Set photo
		      if( !empty($row[6]) ) {
		        $group->setPhoto(APPLICATION_PATH.'/logos/'.$row[6]);
		      }
		
		      // Process privacy
		      $auth = Engine_Api::_()->authorization()->context;
		      $roles = array(
		                'owner',
		                'officer',
		                'member',
		                'registered',
		                'everyone'
		            );
		
		      if( empty($values['auth_view']) ) {
		        $values['auth_view'] = 'everyone';
		      }
		
		      if( empty($values['auth_comment']) ) {
		        $values['auth_comment'] = 'registered';
		      }
		
		      if( empty($values['auth_poll']) ) {
		        $values['auth_poll'] = 'member';
		      }
		
		      if( empty($values['auth_event']) ) {
		        $values['auth_event'] = 'registered';
		      }
		
		      if( empty($values['auth_sub_group'])){
		        $values['auth_sub_group'] = 'member';
		      }
		
		      if( empty($values['auth_video'])){
		        $values['auth_video'] = 'member';
		      }
		
		      if( empty($values['auth_wiki'])){
		        $values['auth_wiki'] = 'member';
		      }
		      
		      if( empty($values['auth_music'])){
		        $values['auth_music'] = 'member';
		      }
		      
		      if( empty($values['auth_listing'])){
		        $values['auth_listing'] = 'member';
		      }
		          
		      if( empty($values['auth_folder'])){
		        $values['auth_folder'] = 'member';
		      }
		
		      if( empty($values['auth_file_upload'])){
		        $values['auth_file_upload'] = 'member';
		      }
		    
		      if( empty($values['auth_file_down'])){
		        $values['auth_file_down'] = 'member';
		      }
		
		      
		      $viewMax = array_search($values['auth_view'], $roles);
		      $commentMax = array_search($values['auth_comment'], $roles);
		      $photoMax = array_search($values['auth_photo'], $roles);
		      $groupMax = array_search($values['auth_event'], $roles);
		      $pollMax = array_search($values['auth_poll'],$roles);
		      $inviteMax = array_search($values['auth_invite'], $roles);
		      $subGroupMax = array_search($values['auth_sub_group'], $roles);
		      $videoMax = array_search($values['auth_video'], $roles);
		      $wikiMax = array_search($values['auth_wiki'], $roles);
		      $musicMax = array_search($values['auth_music'], $roles);
		      $folderMax = array_search($values['auth_folder'], $roles);
		      $fileuploadMax = array_search($values['auth_file_upload'], $roles);
		      $filedownloadMax = array_search($values['auth_file_down'], $roles);
		      $listingMax = array_search($values['auth_listing'], $roles);
		    
		      $officerList = $group->getOfficerList();
		
		      foreach( $roles as $i => $role ) {
		        if( $role === 'officer' ) {
		          $role = $officerList;
		        }
		        $auth->setAllowed($group, $role, 'view', ($i <= $viewMax));
		        $auth->setAllowed($group, $role, 'comment', ($i <= $commentMax));
		        $auth->setAllowed($group, $role, 'photo', ($i <= $photoMax));
		        $auth->setAllowed($group, $role, 'event', ($i <= $groupMax));
		        $auth->setAllowed($group, $role, 'poll', ($i <= $pollMax));
		        $auth->setAllowed($group, $role, 'invite', ($i <= $inviteMax));
		        $auth->setAllowed($group, $role, 'sub_group', ($i <= $subGroupMax));
		        $auth->setAllowed($group, $role, 'video', ($i <= $videoMax));
		        $auth->setAllowed($group, $role, 'wiki', ($i <= $wikiMax));
		        $auth->setAllowed($group, $role, 'music', ($i <= $musicMax));
		        $auth->setAllowed($group, $role, 'folder', ($i <= $folderMax));
		        $auth->setAllowed($group, $role, 'file_upload', ($i <= $fileuploadMax));
		        $auth->setAllowed($group, $role, 'file_down', ($i <= $filedownloadMax));
		        $auth->setAllowed($group, $role, 'listing', ($i <= $listingMax));
		      }
		
		      // Create some auth stuff for all officers
		      $auth->setAllowed($group, $officerList, 'edit', 1);
		      $auth->setAllowed($group, $officerList, 'style', 1);
		      $auth->setAllowed($group, $officerList, 'photo.edit', 1);
		      $auth->setAllowed($group, $officerList, 'announcement', 1);
		      $auth->setAllowed($group, $officerList, 'sponsor', 1);
		      $auth->setAllowed($group, $officerList, 'invitation', 1);
		      $auth->setAllowed($group, $officerList, 'file.edit', 1);
		      $auth->setAllowed($group, $officerList, 'member.edit', 1);
		      $auth->setAllowed($group, $officerList, 'topic.edit', 1);
		      $auth->setAllowed($group, $officerList, 'poll.edit', 1);
		      
		      // Add auth for invited users
		      $auth->setAllowed($group, 'member_requested', 'view', 1);
		      // Commit
		      $db->commit();
		
		    } catch( Engine_Image_Exception $e ) 
		    {
		      $db->rollBack();
		    } 
		}
		fclose($fh);
		exit();
	}
}
