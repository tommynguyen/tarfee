<?php
class Ynfeedback_Plugin_Core {
	
	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getItemTable('ynfeedback_idea');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'feedback');
	}
	
	public function onRenderLayoutDefault($event) {
        // Arg should be an instance of Zend_View
        $view = $event->getPayload();
        $isMobile = Engine_Api::_()->ynfeedback()->isMobile();
        if($view instanceof Zend_View && !$isMobile) {
            $view->headScript()->prependFile($view->layout()->staticBaseUrl . 'application/modules/Ynfeedback/externals/scripts/render-feedback-button.js');
        }
    }
    
    public function onUserCreateAfter($event) {
        // Arg should be an instance of Zend_View
        $user = $event -> getPayload();
        if (!($user instanceof User_Model_User)) {
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!$settings->getSetting('ynfeedback_guest_merge', 1)) {
            return;
        }
        
        $email = $user->email;
        $ideaTable = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $data = array (
            'user_id' => $user->getIdentity()
        );
        $where = $ideaTable->getAdapter()->quoteInto('guest_email = ?', $email);
        $idea = $ideaTable->update($data, $where);
        
        $commentTable = Engine_Api::_()->getDbTable('comments', 'ynfeedback');
        $data = array (
            'poster_id' => $user->getIdentity()
        );
        $where = $commentTable->getAdapter()->quoteInto('poster_email = ?', $email);
        $comment = $commentTable->update($data, $where);
        
        //send notification
        if ($idea || $comment) {
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($user, $user, $user, 'ynfeedback_idea_signupmerge', array('params' => array('route' => 'ynfeedback_general', 'action' => 'manage'), 'innerHTML' => ''));
        }
    }
    
    public function onUserLoginAfter($event)
    {
    	// Arg should be an instance of Zend_View
        $user = $event -> getPayload();
		if(!isset($user -> email))
		{
			return;
		}
        if (!($user instanceof User_Model_User) || empty($user -> email)) {
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!$settings->getSetting('ynfeedback_guest_merge', 1)) {
            return;
        }
        
        $email = $user->email;
        $ideaTable = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $data = array (
            'user_id' => $user->getIdentity()
        );
        $where = $ideaTable->getAdapter()->quoteInto('guest_email = ?', $email);
        $idea = $ideaTable->update($data, $where);
        
        $commentTable = Engine_Api::_()->getDbTable('comments', 'ynfeedback');
        $data = array (
            'poster_id' => $user->getIdentity()
        );
        $where = $commentTable->getAdapter()->quoteInto('poster_email = ?', $email);
        $comment = $commentTable->update($data, $where);
        
        //send notification
        if ($idea || $comment) {
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($user, $user, $user, 'ynfeedback_idea_signupmerge', array('params' => array('route' => 'ynfeedback_general', 'action' => 'manage'), 'innerHTML' => ''));
        }
    }
}
