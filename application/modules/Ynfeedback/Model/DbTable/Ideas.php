<?php
class Ynfeedback_Model_DbTable_Ideas extends Engine_Db_Table {
    protected $_rowClass = 'Ynfeedback_Model_Idea';
    protected $_name = 'ynfeedback_ideas';
    
	public function getAllChildrenIdeasByCategory($node) {
		$return_arr = array();
		$cur_arr = array();
		$list_categories = array();
		Engine_Api::_() -> getItemTable('ynfeedback_category') -> appendChildToTree($node, $list_categories);
		foreach ($list_categories as $category) {
			$tableIdea = Engine_Api::_() -> getItemTable('ynfeedback_idea');
			$select = $tableIdea -> select() -> where('category_id = ?', $category -> category_id) -> where('deleted = 0');
			$cur_arr = $tableIdea -> fetchAll($select);
			if (count($cur_arr) > 0) {
				$return_arr[] = $cur_arr;
			}
		}
		return $return_arr;
	}
	
	public function getIdeasByCategory($category_id) {
		$select = $this -> select() -> where('category_id = ?', $category_id);
		return $this -> fetchAll($select);
	}
	
    public function getIdeasPaginator($params = array()) {
        
        $paginator = Zend_Paginator::factory($this -> getIdeasSelect($params));
        if(isset($params['page']) && !empty($params['page']) ) {
            $paginator->setCurrentPageNumber($params['page']);
        }
        if(isset($params['limit']) && !empty($params['limit']) ) {
            $paginator->setItemCountPerPage($params['limit']);
        }
        return $paginator;
    }
    
    public function getIdeasSelect($params = array()) 
    {
		$ideaTblName = $this -> info('name');
		$userTbl = Engine_Api::_() -> getDbtable('users', 'user');
		$userTblName = $userTbl -> info('name');
		
		if (!isset($params['direction']))
		{
			$params['direction'] = "DESC";
		}
		
		//Init select object
		$select = $this -> select() -> from($ideaTblName);
		$select -> joinLeft("$userTblName as user", "user.user_id = $ideaTblName.user_id", null); 
        $select -> group("$ideaTblName.idea_id");
        
		//User id filter
		if (!empty($params['user_id']) && is_numeric($params['user_id']))
		{
			$select -> where($ideaTblName . '.user_id = ?', $params['user_id']);
		}

		//Category filter
		if (!empty($params['category_id'])) {
		    $categoryTbl = Engine_Api::_()->getItemTable('ynfeedback_category');
		    $node = $categoryTbl -> getNode($params['category_id']);
            if ($node) {
                $tree = array();
                $categoryTbl -> appendChildToTree($node, $tree);
                $categories = array();
                foreach ($tree as $node) {
                    array_push($categories, $node->category_id);
                }
                $select -> where($ideaTblName . '.category_id IN (?)', $categories);
            }
		}

    	//Status filter
		if (!empty($params['status_id'])) 
		{
			$select -> where($ideaTblName . '.status_id = ?', $params['status_id']);
		}

		//Search filter
		if (!empty($params['keyword'])) 
		{
			$select -> where($ideaTblName . ".title LIKE ? ", '%' . $params['keyword'] . '%');
		}

		//Title filter
		if (!empty($params['title'])) 
		{
			$select -> where($ideaTblName . ".title LIKE ? ", '%' . $params['title'] . '%');
		}

    	//Guest name
		if (!empty($params['guest_name'])) 
		{
			$select -> where($ideaTblName . ".guest_name LIKE ? ", '%' . $params['guest_name'] . '%');
		}
		
    	//Guest email
		if (!empty($params['guest_email'])) 
		{
			$select -> where($ideaTblName . ".guest_email LIKE ? ", '%' . $params['guest_email'] . '%');
		}

		//Feature feedback filter
		if (isset($params['highlighted']) && $params['highlighted'] == '1') 
		{
			$select -> where("$ideaTblName.highlighted = 1");
		}

    	if (isset($params['owner']) && $params['owner'] != '') 
    	{
			$select -> where('user.displayname LIKE ?', '%' . $params['owner'] . '%');
		}
		
    	if(!empty($params['from_date']))
		{
			$select -> where("{$ideaTblName}.creation_date >= ?", $params['from_date']->get('yyyy-MM-dd'));
		}
		
		if(!empty($params['to_date'])) 
		{
			$select -> where("{$ideaTblName}.creation_date <= ?", $params['to_date']->get('yyyy-MM-dd'));
		}
		
		//most follow
		if (isset($params['most_follow']) && $params['most_follow'] != '')
		{
			$select -> where("{$ideaTblName}.follow_count > 0");
		}
		
		//My Following
		if(isset($params['follower_id']) && isset($params['follow']))
		{
			$followTable = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
			$ideaIds = array();
			foreach($followTable -> getFollowIdeas($params['follower_id']) as $idea)
			{
				$ideaIds[] = $idea -> idea_id;
			}
			if(!$ideaIds)
			{
				$ideaIds = '';
			}
			$select -> where("{$ideaTblName}.idea_id IN (?)", $ideaIds);
		}
		
    	//Order by filter
		if (isset($params['orderby']) && $params['orderby'] != '') 
		{
			$select = $select -> order( "{$params['orderby']} {$params['direction']}" );
		}
		else 
		{
			$select = $select -> order ( "{$ideaTblName}.idea_id DESC" );
		}
		
        //don't show deleted ideas
        $select->where('deleted = ?', 0);
        
		//Limit option
		if (!empty($params['limit'])) {
			$select -> limit($params['limit']);
		}
		
		//Return query
		return $select;
    }
    
    public function changeStatus($oldId, $newId) {
        $this->update(array('status_id' => $newId), $this->getAdapter()->quoteInto('status_id = ?', $oldId));
    }
    
    public function getFeedbackTitles()
    {
    	$result = array();
    	$select = $this -> getIdeasSelect(); 
    	$feedbacks = $this -> fetchAll($select);
    	foreach ($feedbacks as $f)
    	{
    		$result[] = $f -> title;
    	}
    	$result = array_unique($result);
    	return $result;
    }
}