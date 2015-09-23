<?php
class Advgroup_Widget_ProfileInfoClubController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}

		// Get subject and check auth
		$group = Engine_Api::_() -> core() -> getSubject('group');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($group -> is_subgroup && !$group -> isParentGroupOwner($viewer)) {
			$parent_group = $group -> getParentGroup();
			if (!$parent_group -> authorization() -> isAllowed($viewer, "view")) {
				return $this -> setNoRender();
			} else if (!$group -> authorization() -> isAllowed($viewer, "view")) {
				return $this -> setNoRender();
			}
		} else if (!$group -> authorization() -> isAllowed($viewer, "view")) {
			return $this -> setNoRender();
		}

		$menu = new Advgroup_Plugin_Menus();
		$aJoinButton = $menu -> onMenuInitialize_AdvgroupProfileMember();
		$this -> view -> aJoinButton = $aJoinButton;
		
		$this -> view -> group = $group;
		$followTable = Engine_Api::_() -> getDbTable('follow', 'advgroup');
		$row = $followTable -> getFollowGroup($group -> getIdentity(), $viewer -> getIdentity());
		if ($row) {
			$this -> view -> follow = $row -> follow;
		} else {
			$this -> view -> follow = false;
		}
	}

}
