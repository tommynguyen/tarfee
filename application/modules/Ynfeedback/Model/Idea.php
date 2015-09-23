<?php
class Ynfeedback_Model_Idea extends Core_Model_Item_Abstract {
	protected $_type = 'ynfeedback_idea';
	protected $_owner_type = 'user';

	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'ynfeedback_specific',
			'action' => 'view',
			'idea_id' => $this -> getIdentity(),
			'reset' => true,
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function delete(){
	    //delete itself
	    $this -> deleted = 1;
		$this -> save();
		
		//delete authors
		$tableAuthors = Engine_Api::_() -> getDbTable('authors', 'ynfeedback');
		$authors = $tableAuthors -> getAuthorsByIdeaId($this -> getIdentity());
		foreach($authors as $author)
		{
			$author -> delete();
		}
		
		//delete follows
		$tableFollows = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
		$follows = $tableFollows -> getAllFollow($this -> getIdentity());
		foreach($follows as $follow)
		{
			$follow -> delete();
		}
		
		//clear comments, likes, notes, votes
		Engine_Api::_()->getDbTable('votes', 'ynfeedback')->deleteVotesByIdea($this->getIdentity());
        Engine_Api::_()->getDbTable('notes', 'ynfeedback')->deleteNotesByIdea($this->getIdentity());
        $this->likes()->deleteLikesByIdea($this->getIdentity());
        $this->comments()->deleteCommentsByIdea($this->getIdentity());
		
		
		//delete actions and attachments
        $streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
        $streamTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynfeedback_idea")');
        $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
        $activityTbl->delete('(`object_id` = '.$this->getIdentity().' AND `object_type` = "ynfeedback_idea")');
        $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
        $attachmentTbl->delete('(`id` = '.$this -> getIdentity().' AND `type` = "ynfeedback_idea")');
		
		//Delete all child (files + screenshoot ... )
		Engine_Api::_()->getDbTable('screenshots', 'ynfeedback')->deleteScreenshotsByIdea($this->getIdentity());
        Engine_Api::_()->getDbTable('files', 'ynfeedback')->deleteFilesByIdea($this->getIdentity());
		
	}
	
	public function getTotalShare(){
		
		$tableAttachments =  Engine_Api::_() -> getDbTable('attachments', 'activity');
		$selectAttachments = $tableAttachments -> select() 
									-> from($tableAttachments->info('name'), 'action_id')
									-> where('type = ?', 'ynfeedback_idea')
									-> where('id = ?', $this -> getIdentity());
		
		$actionIds  =  $tableAttachments -> fetchAll($selectAttachments);
		$arr_actionIds =  array();
		foreach($actionIds as $id)
		{
			$arr_actionIds[] = $id -> action_id;
		}
		if(count($arr_actionIds) > 0)
		{
			$tableAction = Engine_Api::_() -> getItemTable('activity_action');
			$selectAcion = $tableAction -> select() 
								   -> from($tableAction, array('count(*) as amount'))
								   -> where('type = ?', 'share')
								   -> where('action_id IN (?)', $arr_actionIds);
			$row = $tableAction -> fetchRow($selectAcion);
			return ($row -> amount);
		}
		else
		{
			return 0;
		}
	}
	
	public function getCategory() {
        $category = Engine_Api::_()->getItem('ynfeedback_category', $this->category_id);
        if ($category) {
            return $category;
        }
    }
	
    public function getSlug($str = NULL, $maxstrlen = 64) {
        $str = $this -> getTitle();
        if (strlen($str) > 32)
        {
            $str = Engine_String::substr($str, 0, 32) . '...';
        }
        $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
        $str = strtolower($str);
        $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
        $str = preg_replace('/-+/', '-', $str);
        $str = trim($str, '-');
        if (!$str)
        {
            $str = '-';
        }
        return $str;
    }
    
	public function getStatus() {
		$tableStatus = Engine_Api::_() -> getDbTable('status', 'ynfeedback');
		return $tableStatus -> getStatusLabel($this -> status_id);
	}
	
	public function getStatusColor() {
		$tableStatus = Engine_Api::_() -> getDbTable('status', 'ynfeedback');
		return $tableStatus -> getStatusColor($this -> status_id);
	}
	
    public function getTitle() {
        if(isset($this->title)) {
            return $this->title;
        }
        return null;
    }
    
    public function getDescription() {
        if(isset($this->description)) {
            return $this->description;
        }
        return null;
    }
    
    public function getFollowCount() {
        return $this -> follow_count;
    }
    
    public function getViewCount() {
        return $this -> view_count;
    }
    
    public function getLikeCount() {
        return $this -> like_count;
    }
    
    public function getCommentCount() {
        return $this -> comment_count;
    }
    
    public function getVoteCount() {
        return $this -> vote_count;
    }
    
    public function getShareCount() {
        return $this->getTotalShare();
    }
    public function getCreationDate($timezone = true) {
        $creationDate = new Zend_Date(strtotime($this->creation_date));
        if ($timezone) {
            $viewer = Engine_Api::_() -> user() -> getViewer();
            $timezone = Engine_Api::_()->getApi('settings', 'core')
            ->getSetting('core_locale_timezone', 'GMT');
            if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
                $timezone = $viewer->timezone;
            }
            $creationDate->setTimezone($timezone);
        }
        return $creationDate;
    }
    public function getScreenshots() {
        return Engine_Api::_()->getDbTable('screenshots', 'ynfeedback')->getScreenshotsOfIdea($this->getIdentity());
    }
    
    public function getFiles() {
        return Engine_Api::_()->getDbTable('files', 'ynfeedback')->getFilesOfIdea($this->getIdentity());
    }

    public function comments() {
    	return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'ynfeedback'));
    }

    public function likes() {
    	return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'ynfeedback'));
    }
    
	public function votes()
    {
    	return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('votes', 'ynfeedback'));
    }
    
    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view'); 
    }
    
    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit'); 
    }
    
    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete'); 
    }
    
    function isCommentable() {
        return $this->authorization()->isAllowed(null, 'comment');
    }
    
    function hasVoted() {
        //TODO check viewer has voted this idea yet?
        return true;
        //return false;
    }
    
    function getOwner($recurseType = null)
    {
    	if ($this -> user_id)
    	{
    		return parent::getOwner($recurseType = null);
    	}
    	$userTbl = Engine_Api::_()->getItemTable('user');
    	$user = $userTbl -> createRow();
    	$user -> displayname = $this -> guest_name;
    	$user -> email = $this -> guest_email;
    	return $user;
    	
    }
    
    public function getMediaType()
    {
        return 'feedback';
    }
    
    public function getDecisionOwner()
    {
    	if ($this -> decision_owner_id)
    	{
    		return Engine_Api::_()->user() -> getUser($this -> decision_owner_id);
    	}
    	return $this -> getOwner();
    }
    
    public function getNoteCount()
    {
    	$noteTbl = Engine_Api::_()->getDbTable('notes', 'ynfeedback');
    	$select = $noteTbl -> select() 
    	->from($noteTbl->info('name'), new Zend_Db_Expr('COUNT(note_id) as note_count'))
    	->where("idea_id = ? ", $this -> getIdentity());
    	$row = $noteTbl->getAdapter()->fetchRow($select);
		return ($row['note_count']) ? $row['note_count'] : 0;
    }
	
	public function getSeverity() {
    	$view = Zend_Registry::get('Zend_View');
        $tableSeverity = Engine_Api::_() -> getDbTable('severities', 'ynfeedback');
        $types = $tableSeverity -> getSeverityArray();	
		if(!empty($types[$this->severity]))
		{
			return $view -> translate($types[$this->severity]);
		}
		else {
			return $view -> translate('Unknow');
		}
    }
}
