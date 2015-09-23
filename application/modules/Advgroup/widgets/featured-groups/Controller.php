<?php
class Advgroup_Widget_FeaturedGroupsController extends Engine_Content_Widget_Abstract
{
	public function indexAction(){
		$table = Engine_Api::_()->getItemTable('group');
		$select = $table->select()
		->where('featured = 1')
		->where('is_subgroup = 0')
		->where('search = 1')
		->order(" RAND() ")
		->limit(9);
		
		$this->view->groups = $groups = $table->fetchAll($select);
		 
		if ($this -> _getParam('max') != '')
		{
			$this -> view -> limit = $this -> _getParam('max');
			if ($this -> view -> limit <= 0)
			{
				$this -> view -> limit = 9;
			}
		}
		else
		{
			$this -> view -> limit = 9;
		}
		 
	}
}
?>