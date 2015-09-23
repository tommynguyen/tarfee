<?php

class Ynadvsearch_Widget_Ynadvsearch2Controller extends Engine_Content_Widget_Abstract
{
	public function indexAction() {
	    
        $session = new Zend_Session_Namespace('mobile');
        if ($session -> mobile) {
            $this->setNoRender();
            return;
        }
		$params = $this->_getAllParams();
		$this->view->align = isset($params['align']) ? $params['align'] : 0;
		$this->view->maxRe = $maxRe = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynadvsearch_num_searchitem', 10);
		// trunglt
		$headLink = new Zend_View_Helper_HeadLink();
        $headLink->prependStylesheet('application/modules/Ynadvsearch/externals/styles/main.css');
		$tokens = Zend_Controller_Front::getInstance ()->getRequest ()-> getParam('token', '');
		$tokens = explode(',', $tokens);
		
		$query = Zend_Controller_Front::getInstance ()->getRequest ()-> getParam('query', '');
		
		$text = explode(',', $query);
		$tokens = array();
		foreach ($text as $key=>$value) {
			if ($value != '') {
				$tokens[] = array(
					'id' => $key,
					'name' => $value
				);
			}
		}
		$this->view->tokens = $tokens;
		$type = array_keys(Engine_Api::_()->ynadvsearch()->getAllowSearchTypes());
		$type[] = 'all';	
		$this->view->type = Zend_Controller_Front::getInstance ()->getRequest ()->getParam('type', $type);
		$sport = array_keys(Engine_Api::_()->getDbTable('sportcategories', 'user')->getCategoriesLevel1Assoc());
		$sport[] = 'all';
		$this->view->sport = Zend_Controller_Front::getInstance ()->getRequest ()->getParam('sport', $sport);
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$level_id = ($viewer->getIdentity()) ? $viewer->level_id : 5;
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	    $max_keywords = $permissionsTable->getAllowed('user', $level_id, 'max_keyword');
	    if ($max_keywords == null) {
	        $row = $permissionsTable->fetchRow($permissionsTable->select()
	        ->where('level_id = ?', $level_id)
	        ->where('type = ?', 'user')
	        ->where('name = ?', 'max_keyword'));
	        if ($row) {
	            $max_keywords = $row->value;
	        }
	    }
		
		$this->view->max_keywords = $max_keywords;
		//advanced search campaign
		$this->view->sports = $sport = Engine_Api::_()->getDbTable('sportcategories', 'user')->getCategoriesLevel1Assoc();	
		$this->view->continents = $continents = Engine_Api::_()->ynadvsearch()->getContinents();
		$this->view->services = $services = Engine_Api::_()->getDbTable('services', 'user')->getAllServices();
		$this->view->relations = $relations = Engine_Api::_() -> getDbTable('relations','user') -> getRelationSearchArray();
	
		$viewer = Engine_Api::_()->user()->getViewer();
		$level_id = ($viewer->getIdentity()) ? $viewer->level_id : 5;
		$this->view->isPro = $isPro = ($level_id == 6 || $level_id == 7 || $viewer->isAdmin()) ? true : false;
		
		$to = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.min_year', 1985);
		$age_to = intval(date('Y')) - intval($to);
		$from = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.max_year', 2003);
		$age_from = intval(date('Y')) - intval($from);
		
		$this->view->age_from = $this->view->max_age_from = $age_from;
		$this->view->age_to = $this->view->max_age_to = $age_to;
		$this->view->rating_from = 0;
		$this->view->rating_to = 5;
		$this->view->params = $params = Zend_Controller_Front::getInstance ()->getRequest ()->getParams();
		
		if (!empty($params['age_from'])) {
			$this->view->age_from = $params['age_from'];
		}
		
		if (!empty($params['age_to'])) {
			$this->view->age_to = $params['age_to'];
		}
		
		if (!empty($params['rating_from'])) {
			$this->view->rating_from = $params['rating_from'];
		}
		
		if (!empty($params['rating_to'])) {
			$this->view->rating_to = $params['rating_to'];
		}
		
		if (!empty($params['continent'])) {
			$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesByContinent($params['continent']);
			$html = '';
			foreach ($countries as $country) {
				$html .= '<option value="' . $country -> getIdentity() . '" label="' . $country -> getTitle() . '" >' . $country -> getTitle() . '</option>';
			}
			$this->view->countriesOption = $html;
		}
		
		if (!empty($params['country_id'])) {
			$subLocations = Engine_Api::_() -> getDbTable('locations', 'user') -> getLocations($params['country_id']);
			$html = '';
			foreach ($subLocations as $subLocation) {
				$html .= '<option value="' . $subLocation -> getIdentity() . '" label="' . $subLocation -> getTitle() . '" >' . $subLocation -> getTitle() . '</option>';
			}
			$this->view->provincesOption = $html;
		}
		
		if (!empty($params['province_id'])) {
			$subLocations = Engine_Api::_() -> getDbTable('locations', 'user') -> getLocations($params['province_id']);
			$html = '';
			foreach ($subLocations as $subLocation) {
				$html .= '<option value="' . $subLocation -> getIdentity() . '" label="' . $subLocation -> getTitle() . '" >' . $subLocation -> getTitle() . '</option>';
			}
			$this->view->citiesOption = $html;
		}
		
		if (!empty($params['sport']) && !empty($params['advsearch']) && ($params['advsearch'] == 'player')) {
			$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
			$node = $sportCattable -> getNode($params['sport']);
			$categories = $node -> getChilren();
			$html = '';
			foreach ($categories as $category) {
				$html .= '<option value="' . $category -> getIdentity() . '" label="' . $category -> title . '" >' . $category -> title . '</option>';
				$node = $sportCattable -> getNode($category -> getIdentity());
				$positions = $node -> getChilren();
				foreach ($positions as $position)
				{
					$html .= '<option value="' . $position -> getIdentity() . '" label="-- ' . $position -> title . '" >' . '-- ' . $position -> title . '</option>';
				}
			}
			$this->view->positionsOption = $html;
		}
	}
}
