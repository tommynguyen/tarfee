<?php
class Ynadvsearch_Widget_SearchResults2Controller extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request->getParams();
		
		$from = $request-> getParam('from', 0);
		$from = intval($from);
		
		$limit = 24;
		
		$viewer = Engine_Api::_()->user()->getViewer();
		$level_id = ($viewer->getIdentity()) ? $viewer->level_id : 5;
		$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	    $max_results = $permissionsTable->getAllowed('user', $level_id, 'max_result');
	    if ($max_results == null) {
	        $row = $permissionsTable->fetchRow($permissionsTable->select()
	        ->where('level_id = ?', $level_id)
	        ->where('type = ?', 'user')
	        ->where('name = ?', 'max_result'));
	        if ($row) {
	            $max_results = $row->value;
	        }
	    }

		if ($max_results && (($from + $limit) >= $max_results)) {
			$limit = $max_results - $from;
			$this->view->reachLimit = true;
		}
		
		$advsearch = $request-> getParam('advsearch', '');
		if ($advsearch == '') {
			$query = $request -> getParam('query', '');
			$this->view->text = $text = explode(',', $query);
			
			$this->view->type = $type = $request->getParam('type',array_keys(Engine_Api::_()->ynadvsearch()->getAllowSearchTypes()));
			$sport = array_keys(Engine_Api::_()->getDbTable('sportcategories', 'user')->getCategoriesLevel1Assoc());
			$sport[] = 'all';
			$this->view->sport = $sport = $request->getParam('sport', $sport);
			$results = Engine_Api::_()->getApi('search', 'ynadvsearch')->getBasicResults( $text, $type, $sport, $from, $limit, $params );
			
			$params['type'] = $type;
			$params['sport'] = $sport;
		}
		else {
			$this->view->text = '';
			$results = Engine_Api::_()->getApi('search', 'ynadvsearch')->getAdvsearchResults($advsearch, $params, $from, $limit );
		} 
		
		$this->view->limit = $limit;
		$this->view->from = $from;
		$this->view->results = $results;
		$this->view->params = $params;
	}
}
