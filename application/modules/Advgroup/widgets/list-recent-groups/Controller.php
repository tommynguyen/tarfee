<?php
class Advgroup_Widget_ListRecentGroupsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$time = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.advgrouptime', 20);
	$unittime = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('advgroup.advgroupunittime',1);
  	//DATE_ADD(OrderDate,INTERVAL 45 DAY)
  	$type = "MONTH";
  	switch ($unittime) {
    case 1:
        $type = "MONTH";
        break;
    case 2:
        $type = "WEEK";
        break;
    case 3:
        $type = "DAY";
        break;
    }
	
    $count = $this->_getParam('itemCountPerPage');
    if(!is_numeric($count) | $count <=0) $count = 12;
    
    $recentType = $this->_getParam('recentType', 'creation');
    if( !in_array($recentType, array('creation', 'modified')) ) {
      $recentType = 'creation';
    }
    $this->view->recentType = $recentType;
    $this->view->recentCol = $recentCol = $recentType . '_date';
    
    // Get paginator
    $table = Engine_Api::_()->getItemTable('group');
    $select = $table->select()
      ->where('search = ?', 1)
      ->where("is_subgroup = ?",0) 
	  ->where('creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL {$time} {$type})"))
      ->limit($count);
    if( $recentType == 'creation' ) {
      // using primary should be much faster, so use that for creation
      $select->order('group_id DESC');
    } else {
      $select->order($recentCol . ' DESC');
    }
    $this->view->groups = $groups = $table->fetchAll($select);
	
    $this->view->limit = $count;
    // Hide if nothing to show
    if( count($groups) <= 0 ) {
      return $this->setNoRender();
    }
  }
}