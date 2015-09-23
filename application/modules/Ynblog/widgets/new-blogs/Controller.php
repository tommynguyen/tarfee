<?php
class Ynblog_Widget_NewBlogsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {

    //Get number of blogs display
    if($this->_getParam('max') != '' && $this->_getParam('max') >= 0){       
        $limitNblog = $this->_getParam('max');
    }else{
        $limitNblog = 8;
    }

    //List params for getting new blogs
    $params = array();
    $params['visible']     = 1;
    $params['draft']       = 0;
    $params['is_approved'] = 1;
    $params['orderby']     = 'creation_date';
    $params['limit']       = $limitNblog;
    
    //Select blogs
    $table  = Engine_Api::_()->getItemTable('blog');
    $select = Engine_Api::_()->ynblog()->getBlogsSelect($params);
    
    $this->view->blogs =$table->fetchAll($select);
    $this->view->limit = $limitNblog;
  }
}
