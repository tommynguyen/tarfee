<?php
class Ynmember_Widget_BrowseBirthdayMembersController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
    	$this -> view -> viewer = Engine_Api::_() -> user() -> getViewer();

	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	    $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page', 1);
	    if (isset($params['year']) && 
	    	isset($params['month']) && 
	    	isset($params['date']))
	    {
	    	$pickedDay = $params['date'];
	    	$pickedMonth = $params['month'];
	    	$pickedYear = $params['year'];
	    	$pickedDate = $params['year'] . "-" . $params['month'] ."-". $params['date'];
	    }
	    else 
	    {
	    	$pickedDay = date('d');
	    	$pickedMonth = date('m');
	    	$pickedYear = date('Y');
	    	$pickedDate = date('Y-m-d');
	    }
	    
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
		
	    $select->where("MONTH(`{$searchTableName}`.birthdate) = ? ", $pickedMonth);
	    $select->where("DAY(`{$searchTableName}`.birthdate) = ? ", $pickedDay);
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
		$limit = $this->_getParam('itemCountPerPage', 15);
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		
		// Load fields view helpers
		$view = $this->view;
		$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> paginator = $paginator;
    	$this -> view -> pickedDate = $pickedDate;
    	$this -> view -> pickedDay = $pickedDay;
    	$this -> view -> pickedMonth = $pickedMonth;
    	$this -> view -> pickedYear = $pickedYear;
    	
    	unset($params['module']);
	    unset($params['controller']);
	    unset($params['action']);
	    unset($params['rewrite']);
	    
	    $this -> view -> formValues = array_filter($params);
	    $this -> view -> viewer = $viewer = Engine_Api::_()->user() ->getViewer();
	}
}