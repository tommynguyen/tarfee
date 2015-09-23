<?php

if (!function_exists('array_column')) {
    function array_column($array, $column_key, $index_key = null)  {
        return array_reduce($array, function ($result, $item) use ($column_key, $index_key) 
        {
            if (null === $index_key) {
                $result[] = $item[$column_key];
            } else {
                $result[$item[$index_key]] = $item[$column_key];
            }

            return $result;
        }, array());
    }
}

class Ynadvsearch_IndexController extends Core_Controller_Action_Standard {
	public function indexAction() {

	}

	public function usersGroupsListAction() {

	}
	
	public function suggestKeywordsAction() {
		$this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
		$search = $this->_getParam('q' ,'');
		$search = trim($search);
		
		$types = Engine_Api::_()->ynadvsearch()->getAllowSearchTypes();
		$types = array_keys($types);
		$searchTbl = Engine_Api::_()->getDbTable('search', 'core');
		$searchSelect = $searchTbl->select()
			->where('title LIKE ?', '%'.$search.'%')
			->where('type IN (?)', $types);
		$rows = $searchTbl->fetchAll($searchSelect);
		$i = 0;
		foreach ($rows as $row) {
			$result[] = array (
				'id' => $i,
				'name' => $row->title
			);
			$i++;
		}
		echo json_encode($result);
        return;
	}
}
