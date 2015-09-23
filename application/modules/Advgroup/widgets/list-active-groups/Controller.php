<?php
class Advgroup_Widget_ListActiveGroupsController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
    $count = $this->_getParam('itemCountPerPage');
    if(!is_numeric($count) | $count <=0) $count = 12;

    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
    $topicName =$topicTable ->info('name');
    $groupTable = Engine_Api::_()->getItemTable('group');
    $groupName = $groupTable->info('name');
    $select = $groupTable->select()->from($groupName,array("$groupName.*","COUNT('topic_id') AS topic_count"))
                ->setIntegrityCheck(false)
                ->joinRight($topicName, "$topicName.group_id = $groupName.group_id","$topicName.topic_id")
                ->where("$groupName.search = ?", 1)
                ->where("$groupName.is_subgroup = ?",0)
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
