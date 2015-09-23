<?php
class Advgroup_Widget_ProfileFollowersController extends Engine_Content_Widget_Abstract {
	protected $_childCount;

	public function indexAction() {
		
		//get viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// Don't render this if not authorized
		$this->view->viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}

		// Get subject and check auth
		$this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject('group');
		$tableFollow = Engine_Api::_() -> getDbTable('follow', 'advgroup');
		$this -> view -> followers = $followers = $tableFollow -> getUserFollow($group -> getIdentity());
		$count = count($followers);
		if($count == 0) {
			return $this -> setNoRender();
		}
		$this -> _childCount = $count;

	}

	public function getChildCount() {
		return $this -> _childCount;
	}

	public function removeAction() {
		echo $this -> render('index');
	}

}
