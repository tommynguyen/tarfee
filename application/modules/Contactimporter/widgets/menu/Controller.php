<?php
class Contactimporter_Widget_MenuController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');
	}
}
