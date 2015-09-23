<?php

class Ynevent_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject() || !$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}

		// Get subject and check auth
		$subject = Engine_Api::_() -> core() -> getSubject('event');

		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_profile');
	}

}
