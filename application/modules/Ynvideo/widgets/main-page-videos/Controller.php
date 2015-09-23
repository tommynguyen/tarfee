<?php
class Ynvideo_Widget_MainPageVideosController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		$params = $request->getParams();
		
		$from = $request-> getParam('from', 0);
		$beforeStr = $request-> getParam('strIds', '');
		$from = intval($from);
		
		$limit = 20;
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer || !$viewer->getIdentity()) {
			return $this->setNoRender();
		}
		
		$user_ids = array();
		$friends = $viewer->getFriendsList();
		foreach ($friends as $friend) {
			$user_ids[] = $friend->getIdentity();
		}
		$group_ids = array();
		$followGroups = Engine_Api::_()->getDbTable('follow', 'advgroup')->getFollowGroups($viewer->getIdentity());		
		foreach ($followGroups as $group) {
			$group_ids[] = $group->resource_id;
		}
		
		$arrNot = explode(',', $beforeStr);
		
		$table = Engine_Api::_()->getItemTable('video');
		$db = $table -> getDefaultAdapter();
		$select = $table->select()->from($table -> info('name'), array("*", new Zend_Db_Expr ("'1' AS order_type")));
		if (!empty($user_ids) && !empty($group_ids)) {
			$select->where("(owner_type = 'user' AND owner_id IN (".join(',', $user_ids).")) OR (parent_type = 'group' AND parent_id IN (".join(',', $group_ids)."))");
		}
		elseif(!empty($user_ids)) {
			$select->where("owner_type = 'user' AND owner_id IN (?)", $user_ids);
		}
		elseif(!empty($group_ids)) {
			$select->where("parent_type = 'group' AND parent_id IN (?)", $group_ids);
		}
		else {
			$select->where('0 = 1');
		}
		if($arrNot)
		{
			$select -> where("video_id NOT IN (?)", $arrNot);
		}
		$select -> where('search = ?', 1) -> where('status = ?', 1);
		$select1 = $table->select() -> from($table -> info('name'), array("*", new Zend_Db_Expr ("'0' AS order_type")));
		if($arrNot)
		{
			$select1 -> where("video_id NOT IN (?)", $arrNot);
		}
		$select1 -> where('search = ?', 1) -> where('status = ?', 1);
		$selectUnion = $db -> select() -> distinct()
		    -> union(array($select, $select1))
		    -> order("order_type DESC")
			-> order('video_id DESC')
			-> limit($limit+1);
		//echo $selectUnion; die;
		$results = $db -> fetchAll($selectUnion);
		// Process ids
		$ids = array();
		foreach ($results as $data) {
			$ids[] = $data['video_id'];
		}
		$this -> view -> count = count($ids);
		$ids = array_unique($ids);
		// Finally get video
		$strIds =  join(',', $ids);
		$this -> view -> strIds = $strIds.','. $beforeStr;
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$results = $table -> fetchAll(
			$table 
				-> select()
				-> where("owner_id NOT IN (?)", $deactiveIds) 
				-> where('video_id IN(' . $strIds . ')')
				-> order(new Zend_Db_Expr("FIELD(video_id,  $strIds)"))
			);
		}
		else {
			$results = $table -> fetchAll(
			$table 
				-> select() 
				-> where('video_id IN(' . $strIds . ')')
				-> order(new Zend_Db_Expr("FIELD(video_id,  $strIds)"))
			);
		}
		
		if (!count($results)) {
			return $this->setNoRender();
		}
		$this->view->limit = $limit;
		$this->view->from = $from;
		$this->view->results = $results;
	}
}
