<?php
class Ynfeedback_Widget_FeedbackListingController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$viewer = Engine_Api::_()->user()->getViewer();

		//Search Params
		$params = array();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$params = $request->getParams();
		$this->view->formValues = $params;
		
		// Get Ideas Paginator
		$ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
		$paginator = $ideaTbl -> getIdeasPaginator($params);
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynfeedback_max_idea', 20);
		$paginator->setItemCountPerPage($items_per_page);

		if(isset($params['page'])){
			$paginator->setCurrentPageNumber($params['page']);
		}
		$this->view->paginator = $paginator;
		$this->view->viewSuggestForm = ($params['module'] == 'ynfeedback' && $params['controller'] == 'index' && $params['action'] == 'listing');
		unset($params['module']);
	    unset($params['controller']);
	    unset($params['action']);
	    unset($params['rewrite']);
	    $this->view->formValues = array_filter($params);
	    $feedbackTitles = $ideaTbl -> getFeedbackTitles();
	    $feedbackTitles = implode('","', $feedbackTitles);
	    $this->view->feedbackTitles = '"' . $feedbackTitles . '"';
	}
}
