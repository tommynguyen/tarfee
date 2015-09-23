<?php

class User_Widget_ProfilePreferredClubsSearchController extends Engine_Content_Widget_Abstract
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
	
    $userGroupMappingTable = Engine_Api::_() -> getDbTable('groupmappings', 'user');
	$groupMappings = $userGroupMappingTable -> getGroupByUser($subject -> getIdentity());
	
	$groups = array();
	foreach($groupMappings as $groupMapping){
			$group_id = $groupMapping -> group_id;
			$group = Engine_Api::_() -> getItem('group', $group_id);
			if($group) {
				$groups[] = $group;
			}
	 }
	$this -> view -> groups = $groups;
	
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $max_club = $permissionsTable->getAllowed('user', $subject->level_id, 'max_club');
    if ($max_club == null) {
        $row = $permissionsTable->fetchRow($permissionsTable->select()
        ->where('level_id = ?', $subject->level_id)
        ->where('type = ?', 'user')
        ->where('name = ?', 'max_club'));
        if ($row) {
            $max_club = $row->value;
        }
    }
	
	$this->view->max_club = $max_club;
  }
}