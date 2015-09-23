<?php
class Ynfeedback_Widget_HighlightFeedbackController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
		//Search Params
		$params = array('highlighted' => 1);
		
		// Get Ideas Paginator
		$ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
		$paginator = $ideaTbl -> getIdeasPaginator($params);
		$items_per_page = $this -> _getParam('itemCountPerPage', 8);
		$paginator->setItemCountPerPage($items_per_page);
		$paginator->setCurrentPageNumber($this->_getParam('page', 1));
		if ($paginator -> getTotalItemCount() == 0)
		{
			return $this->setNoRender();
		}
		$this->view->paginator = $paginator;
    }
}
