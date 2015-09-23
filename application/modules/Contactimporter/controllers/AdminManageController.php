<?php
class Contactimporter_AdminManageController extends Core_Controller_Action_Admin
{
	// @todo add in stricter settings for admin level checking
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_admin_main', array(), 'contactimporter_admin_main_providers');
		$table = $this -> _helper -> api() -> getDbtable('providers', 'Contactimporter');
		$select = $table -> select() -> order('order');

		$this -> view -> paginator = $table -> fetchAll($select);
	}

	public function editAction()
	{

		$name = $this -> _getParam('name', null);
		$provider = Engine_Api::_() -> getItem('contactimporter_provider', $name);
		$this -> view -> form = $form = new Contactimporter_Form_Admin_Manage_Edit();
		$form -> title -> setValue(htmlspecialchars_decode($provider -> title));
		$form -> enable -> setValue(htmlspecialchars_decode($provider -> enable));
		$form -> order -> setValue($provider -> order);
		//CHECK IF PROVIDER IS EMAIL OR SOCIAL

		// Posting form
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$provider -> title = htmlspecialchars($form -> getValue('title'));
		$provider -> enable = htmlspecialchars($form -> getValue('enable'));
		$provider -> order = htmlspecialchars($form -> getValue('order'));
		if ($provider -> type == 'email')
		{
			$provider -> default_domain = htmlspecialchars($form -> getValue('default_domain'));

		}
		elseif ($provider -> type == 'social')
		{
			if ($provider -> photo_import)
			{
				$provider -> photo_enable = htmlspecialchars($form -> getValue('photo_enable'));

			}
		}
		$provider -> save();
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Provider Edited.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

}
