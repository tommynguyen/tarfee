<?php
class Ynevent_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract 
{
   public function indexAction() {
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params = $request -> getParams();
		$categories = array();
		if(isset($params['categories']))
		{
			$categories = $params['categories'];
		}
		$by_authors = array();
		if(isset($params['by_authors']))
		{
			$by_authors = $params['by_authors'];
		}
		$this -> view -> categories = $categories;
		$this -> view -> by_authors = $by_authors;
		$this -> view -> keyword = isset($params['keyword'])?$params['keyword']:'';
		$this -> view -> page = isset($params['page'])?$params['page']:1;
	}

}
