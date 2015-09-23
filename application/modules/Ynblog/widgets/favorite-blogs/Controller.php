<?php
class Ynblog_Widget_FavoriteBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    //Get number of blogs display
    if($this->_getParam('max') != '' && $this->_getParam('max') >= 0){
       $limit = $this->_getParam('max');
    }else{
       $limit = 5;
    }
	$viewer = Engine_Api::_() -> user() -> getViewer();
	$values['favorite_owner_id'] = $viewer -> getIdentity();
	$paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $values );
	$paginator->setItemCountPerPage ( $limit );
    $this->view->blogs = $paginator;
	if($paginator -> getTotalItemCount() <= 0)
	{
		return $this -> setNoRender();
	}
    $this->view->limit = $limit;
  }
}