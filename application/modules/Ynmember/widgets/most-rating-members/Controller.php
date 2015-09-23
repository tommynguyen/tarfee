<?php
class Ynmember_Widget_MostRatingMembersController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$params = $this->_getAllParams();
		if(!empty($params['itemCountPerPage']))
		{
			$limit = $params['itemCountPerPage'];
		}
		else
		{
			$limit = 3;
		}		
		$tableUser = Engine_Api::_() -> getItemTable('user');
		$select = $tableUser -> select() -> where('enabled = 1') -> where('verified = 1') -> where('approved = 1');
		$select->order("rating DESC")->limit($limit);
		$this -> view -> list_show_users = $tableUser -> fetchAll($select);
	}
}