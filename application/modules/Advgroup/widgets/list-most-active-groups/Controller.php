<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    AdvGroup
 * @author     YouNet Company
 */
class Advgroup_Widget_ListMostActiveGroupsController extends Engine_Content_Widget_Abstract
{
	
  public function indexAction(){
    $count = $this->_getParam('itemCountPerPage');
    if(!is_numeric($count) | $count <=0) $count = 12;
	
    $time = $this->_getParam('time',1);
    if( !in_array($time, array(1, 2, 3)) ) {
    	$time = 1;
    }

    $date = date('Y-m-d H:i:s');
    switch($time){
    	case 1:
    		$newdate = strtotime ( '-30 day' , strtotime ($date)) ;
    		break;
    	case 2:
    		$newdate = strtotime ( '-60 day' , strtotime ($date)) ;
    		break;
    	case 3:
    		$newdate = strtotime ( '-90 day' , strtotime ($date)) ;
    }
    $newdate = date ( 'Y-m-d H:i:s' , $newdate );
    
    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
    $topicName =$topicTable ->info('name');
    $groupTable = Engine_Api::_()->getItemTable('group');
    $groupName = $groupTable->info('name');
    
    $select = $groupTable->select()->from($groupName,array("$groupName.*","COUNT('topic_id') AS topic_count"))
                ->setIntegrityCheck(false)
                ->joinRight($topicName, "$topicName.group_id = $groupName.group_id","$topicName.topic_id")
                ->where("$groupName.search = ?", 1)
                ->where("$groupName.is_subgroup = ?",0)
                ->where("$topicName.creation_date > ?",$newdate)
                ->group("$groupName.group_id")
                ->order("COUNT('topic_id') DESC")
                ->limit($count);
    $this->view->groups = $groups = $groupTable->fetchAll($select);
    $this->view->limit = $count;
    if( count($groups) <= 0 ) {
      return $this->setNoRender();
    }
  }
}

?>
