<?php
class Ynadvsearch_Bootstrap extends Engine_Application_Bootstrap_Abstract {

	/**
	 * init CSS
	 */
	public function _initCss() {
		$view = Zend_Registry::get('Zend_View');

		// add font Awesome 4.1.0
		$url = $view -> baseUrl() . '/application/modules/Ynadvsearch/externals/styles/font-awesome.css';
		$url1 = $view -> baseUrl() . '/application/modules/Ynadvsearch/externals/styles/token-input.css';
		$url2 = $view -> baseUrl() . '/application/modules/Ynadvsearch/externals/styles/token-input-mac.css';
		$url3 = $view -> baseUrl() . '/application/modules/Ynadvsearch/externals/styles/token-input-facebook.css';
		
		$view -> headLink() -> appendStylesheet($url);
		$view -> headLink() -> appendStylesheet($url1);
		$view -> headLink() -> appendStylesheet($url2);
		$view -> headLink() -> appendStylesheet($url3);
	}
	
	public function _initJs() {
		$view = Zend_Registry::get('Zend_View');

		$url = $view -> baseUrl() . '/application/modules/Ynadvsearch/externals/scripts/jquery-1.7.1.min.js';
		$view -> headScript() -> appendFile($url);
	}
}
?>