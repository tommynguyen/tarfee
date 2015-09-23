<?php
class User_Widget_ProfileSectionsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return $this->setNoRender();
	    }
	
	    // Get subject and check auth
	    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
	    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
	      return $this->setNoRender();
	    }
		
		//change profile base on level
		$table = Engine_Api::_()->getApi('core', 'fields')->getTable('user', 'values');
	    $select = $table->select();
	    $select->where('field_id = ?', 1);
	    $select->where('item_id = ?', $subject -> getIdentity());
	    $value_profile = $table->fetchRow($select);
		if($value_profile)
		{
			$profile_id = Engine_Api::_() -> user() -> getProfileTypeBaseOnLevel($subject->level_id);
			if($value_profile -> value != $profile_id)
			{
				$value_profile -> value = $profile_id;
				$value_profile -> save();
			}
		}
		else {
			$value_profile = $table -> createRow();
			$value_profile -> field_id = 1;
			$value_profile -> item_id = $subject -> getIdentity();
			$profile_id = Engine_Api::_() -> user() -> getProfileTypeBaseOnLevel($subject->level_id);
			$value_profile -> value = $profile_id;
			$value_profile -> save();
		}
	}
}
