<?php
class Ynblog_Widget_MostCommentedBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    //Get number of blogs display
    if($this->_getParam('max') != '' && $this->_getParam('max') >= 0){       
      $limitMCblog = $this->_getParam('max');
    }
    else{
      $limitMCblog = 4;
    }

    //Get blogs
    $b_table = Engine_Api::_()->getItemtable('blog');
    $bName = $b_table->info('name');
    $select = $b_table->select()->from($bName)
                      ->order('comment_count DESC')
                      ->where("search = ?","1")
                      ->where("draft = ?","0")
                      ->where("is_approved = ?","1")
                      ->where("comment_count > ?","0")
                      ->limit($limitMCblog);
    
    $this->view->blogs = $b_table->fetchAll($select);
    $this->view->limit = $limitMCblog;
  }
}