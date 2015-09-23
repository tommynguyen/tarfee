<?php
class Ynmember_Widget_MostReviewedMembersController extends Engine_Content_Widget_Abstract 
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
		$tableReview = Engine_Api::_() -> getItemTable('ynmember_review');
		$tableReviewName = $tableReview->info('name');
	    $select = $tableReview -> select()  
	   			->from(array('r' => $tableReviewName),
                    array('resource_id', 'count' => 'count(`resource_id`)'));
		$select = $select -> group("r.resource_id");
        $select->order("count DESC")->limit($limit);
		$this -> view -> list_show_users = $list_show_users = $tableReview -> fetchAll($select);
		if(count($list_show_users) <= 0)
		{
			return $this->setNoRender();
		}
	}
}