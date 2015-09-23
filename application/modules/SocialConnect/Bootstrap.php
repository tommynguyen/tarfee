<?php
class SocialConnect_Bootstrap extends Engine_Application_Bootstrap_Abstract {
	public function __construct($application) {
		parent::__construct($application);
		$application -> getApplication() -> getAutoloader() -> register('SocialConnect', $this -> getModulePath());
	}

	public function getModuleName() {
		return 'social-connect';
	}

	protected function _initViewHelper() {

		// add javacsript
		$view = Zend_Registry::get('Zend_View');
		$view -> headScript() -> appendFile($view -> layout() -> staticBaseUrl . 'application/modules/SocialConnect/externals/scripts/core.js');

		// add view helper
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/SocialConnect/View/Helper/', 'SocialConnect_View_Helper_');

	}
}