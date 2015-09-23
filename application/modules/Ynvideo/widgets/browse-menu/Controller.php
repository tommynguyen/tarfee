<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{

	public function indexAction()
	{
		$videoActivePage = NULL;

		if (Zend_Registry::isRegistered('VIDEO_ACTIVE_PAGE'))
		{
			$videoActivePage = Zend_Registry::get('VIDEO_ACTIVE_PAGE');
		}

		// Get navigation
		$this -> view -> navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideo_main', array(), $videoActivePage);
	}

}
