<?php
class Ynmember_Widget_BrowseMembersController extends Engine_Content_Widget_Abstract
{
 	public function indexAction()
 	{
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

	  	//Setup params
	  	$request = Zend_Controller_Front::getInstance()->getRequest();
	    $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
	    $originalOptions = $params;
	    
	    if ($params['controller'] == 'member' && $params['action'] == 'myfriend')
	    {
	    	$params['show'] = 'friend';	
	    	if (!$viewer->getIdentity())
	    	{
	    		return $this->setNoRender();
	    	}
	    }
	    else if ($params['controller'] == 'member' && $params['action'] == 'feature')
 		{
	    	$params['show'] = 'featured';	
	    }
 		else if ($params['controller'] == 'member' && $params['action'] == 'rating')
 		{
	    	$params['order'] = 'most_rating';	
	    }
	    
	  	//Set curent page
		$paginator = Engine_Api::_()->ynmember()->getMemberPaginator($params);
		$limit = $this->_getParam('itemCountPerPage', 15);
		$limit = ($limit == '') ? 15 : $limit;
		if (!isset($params['page']) || $params['page'] == '0')
		{
			$page = 1;
		}
		else
		{
			$page = (int)$params['page'];
		}
		$paginator->setItemCountPerPage($limit);
		$paginator->setCurrentPageNumber($page);
		
		// Load fields view helpers
		$view = $this->view;
		$view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> paginator = $paginator;
		unset($originalOptions['module']);
	    unset($originalOptions['controller']);
	    unset($originalOptions['action']);
	    unset($originalOptions['rewrite']);
		$this -> view -> formValues = array_filter($originalOptions);
	}

}