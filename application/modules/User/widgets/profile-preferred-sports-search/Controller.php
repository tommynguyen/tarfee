<?php

class User_Widget_ProfilePreferredSportsSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') || !$viewer -> isSelf($subject) ) {
      return $this->setNoRender();
    }
	
    $this->view->sports = $sports = $subject->getSports();
	
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_sport = $permissionsTable->getAllowed('user', $subject->level_id, 'max_sport');
    if ($max_sport == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $subject->level_id)
        ->where('type = ?', 'user')
        ->where('name = ?', 'max_sport'));
        if ($row) {
            $max_sport = $row->value;
        }
    }
	
	$this->view->max_sport = $max_sport;
  }
}