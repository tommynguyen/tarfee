<?php
class User_Model_Location extends Core_Model_Item_Abstract {
    protected $_type = 'user_location';
    protected $_parent_type = 'user';
    protected $_searchTriggers = false;
	
	public function getHref($params = array()) {
		$slug = $this -> getSlug();
		$params = array_merge(array(
			'route' => 'admin_default',
			'reset' => true,
			'module' => 'user',
			'controller' => 'locations',
			'action' => 'index',
			'id' => $this -> getIdentity(),
			'slug' => $slug,
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	
	public function getTitle() {
    	$view = Zend_Registry::get('Zend_View');
        return $view->translate($this->title);
    }
	
	public function getContinent() {
		$view = Zend_Registry::get('Zend_View');
        return $view->translate($this->continent);
	}
	
	public function getParent($recurseType = null) {
		return Engine_Api::_()->getItem('user_location', $this->parent_id);
	}
	public function getParents() {
		$parents = array();
		$node = $this;
		while ($node->parent_id) {
			$parent = $node->getParent();
			$parents[] = $parent;
			$node = $parent;
		}
		$parents = array_reverse($parents);
		return $parents;
	}
}
