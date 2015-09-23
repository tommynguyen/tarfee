<?php
/*
 * Company: YounetCo
 * Author: 	LuanND
 */

class Ynevent_Widget_ProfileBlogsController extends Engine_Content_Widget_Abstract {

	public function indexAction() {
		
		if (!Engine_Api::_() -> hasItemType('blog')) {
			return $this -> setNoRender();
		}
		if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> setNoRender();
		}
		$viewer = Engine_Api::_()->user()->getViewer();
		// Get subject and check auth
		$this -> view -> event = $subject = Engine_Api::_() -> core() -> getSubject();
		if (!$subject -> authorization() -> isAllowed($viewer, 'view')) {
			return $this -> setNoRender();
		}
		
		// Get paginator
		$table = Engine_Api::_() -> getItemTable('blog');
		
		$name = $table -> info('name');
		$h_table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
		$h_name = $h_table -> info('name');
		$select = $table -> select() -> from($name)
			-> join($h_name, "$h_name.item_id = $name.blog_id AND $h_name.type = 'blog'", '') 
			-> where('search = ?', 1) 
			-> where('event_id = ?', $subject -> getIdentity()) 
			-> order("$name.creation_date" . ' DESC');
		$itemCountPerPage = $this -> _getParam('itemCountPerPage', 5);
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($itemCountPerPage);
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$paginator -> setCurrentPageNumber($request->getParam('page', 1));
	}
}
