<?php
class Ynmember_Widget_MemberBirthdayController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$month = date('m');
		$day = date('d');
		
		$table = Engine_Api::_()->getItemTable('user');
	    $userTableName = $table->info('name');
	    
	    $searchTable = Engine_Api::_()->fields()->getTable('user', 'search');
	    $searchTableName = $searchTable->info('name');
	    
	    // Contruct query
	    $select = $table->select()
	      ->from($userTableName)
	      ->joinLeft($searchTableName, "`{$searchTableName}`.`item_id` = `{$userTableName}`.`user_id`", null)
	      ->where("{$userTableName}.search = ?", 1)
	      ->where("{$userTableName}.enabled = ?", 1);
		
	    $select->where("MONTH(`{$searchTableName}`.birthdate) = ? ", $month);
	    $select->where("DAY(`{$searchTableName}`.birthdate) = ? ", $day);
	    $users = $table->fetchAll($select);
	    
		$members = array();
		if (count($users))
		{
			foreach ($users as $user)
			{
				if (!Engine_Api::_() -> ynmember()-> canFilterByBirthday($user))
				{
					continue;
				}
				else 
				{
					$members[] = $user;
				}				
			}	
		}
	    
	  	//Set curent page
		$paginator = Zend_Paginator::factory($members);
		$limit = $this->_getParam('itemCountPerPage', 4);
		$paginator->setItemCountPerPage($limit);
		$this->view->paginator = $paginator;
	}
}