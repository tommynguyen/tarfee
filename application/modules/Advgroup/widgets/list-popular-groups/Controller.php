<?php
class Advgroup_Widget_ListPopularGroupsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $count = $this->_getParam('itemCountPerPage');
    if(!is_numeric($count) | $count <=0) $count = 12;
    
    $popularType = $this->_getParam('popularType', 'member');
    if( !in_array($popularType, array('view', 'member')) ) {
      $popularType = 'member';
    }
    $this->view->popularType = $popularType;
    $this->view->popularCol = $popularCol = $popularType . '_count';
    
    // Get paginator
    $table = Engine_Api::_()->getItemTable('group');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where("is_subgroup = ?",0)
      ->order($popularCol . ' DESC')
      ->limit($count);
    $this->view->groups = $groups = $table->fetchAll($select);
    $this->view->limit = $count;
    // Hide if nothing to show
    if( count($groups) <= 0 ) {
      return $this->setNoRender();
    }
  }
}