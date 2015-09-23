<?php
class SocialConnect_Widget_FooterMenuController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Do not show if logged in
		if (Engine_Api::_() -> user() -> getViewer() -> getIdentity())
		{
			$this -> setNoRender();
			return;
		}

		// Get Params
		$this->view->iconsize = $this->_getParam('iconsize', 24);
		$this->view->margintop = $this->_getParam('margintop', 0);
		$this->view->marginright = $this->_getParam('marginright', 0);
		
	}

	public function getCacheKey()
	{
		return false;
	}

}
