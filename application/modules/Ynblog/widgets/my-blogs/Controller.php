<?php
class Ynblog_Widget_MyBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    //Get number of blogs display
    if($this->_getParam('max') != '' && $this->_getParam('max') >= 0){
       $limit = $this->_getParam('max');
    }else{
       $limit = 5;
    }
	$viewer = Engine_Api::_ ()->user ()->getViewer ();
	if(!$viewer -> getIdentity())
	{
		return $this -> setNoRender();
	}
    //Select blogs
    $btable = Engine_Api::_()->getItemTable('blog');
    $bName = $btable->info('name');
    
    $select = $btable->select()->from($bName) -> where('owner_id = ?', $viewer -> getIdentity()) -> order("creation_date DESC")
                     ->limit($limit);

    $this->view->blogs = $blogs = $btable->fetchAll($select);
	if(count($blogs) <= 0)
	{
		return $this -> setNoRender();
	}
    $this->view->limit = $limit;
  }
}