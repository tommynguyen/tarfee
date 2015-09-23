<?php
class Ynblog_Widget_MostViewedBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    //Get number of blogs display
    if($this->_getParam('max') != ''  && $this->_getParam('max') >= 0){       
      $limitMVblog = $this->_getParam('max');
    }else{
      $limitMVblog = 4;
    }

    //Select glogs
    $table = Engine_Api::_()->getItemTable('blog');
    $name = $table->info('name');
    $select = $table->select()->from($name) 
                    ->order('view_count DESC')
                    ->where("search = 1")
                    ->where("draft = 0")
                    ->where('is_approved = 1')
                    ->where("view_count > 0")
                    ->limit($limitMVblog);
    
    $this->view->blogs = $table->fetchAll($select);
    $this->view->limit = $limitMVblog;
  }
}