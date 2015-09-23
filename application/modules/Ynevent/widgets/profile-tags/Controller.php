<?php
class Ynevent_Widget_ProfileTagsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  	{
	    $t_table = Engine_Api::_()->getDbtable('tags', 'core');
	    $tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
	    $e_table = Engine_Api::_()->getItemTable('event');
	    
	    $tName = $t_table->info('name');
	    $tmName = $tm_table->info('name');
	    $eName = $e_table->info('name');

	    $filter_select = $tm_table->select()->distinct()->from($tmName,array("$eName.repeat_group", "$tmName.tag_id", "$tmName.resource_type", "$tmName.resource_id"))
    	                     ->setIntegrityCheck(false)
    	                     ->joinLeft($eName,"$eName.event_id = $tmName.resource_id",'')
    	                     ->where("$eName.search = ?", "1");
	    
		
	    $select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
	    $select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
	    $select  ->order("$tName.text");
	    $select  ->group("$tName.text");
	    $select  ->where("t.resource_type = ?","event");
	    
	    if(Engine_Api::_()->core()->hasSubject('user')){
	      $user = Engine_Api::_()->core()->getSubject('user');
	      $select -> where("t.tagger_id = ?", $user->getIdentity());
	    }
	    else if( Engine_Api::_()->core()->hasSubject('event') ) {
	      $event = Engine_Api::_()->core()->getSubject('event');
	      $select -> where("t.resource_id = ?", $event->getIdentity());
	    }

	    $result = $t_table->fetchAll($select);
	    if (count($result) == 0) {
	    	return $this->setNoRender();
	    }
	    $this->view->tags = $result;
	    
	    $filter_select = $tm_table->select()->distinct()->from($tmName,array("$eName.repeat_group", "$tmName.tag_id", "$tmName.resource_type"))
		    ->setIntegrityCheck(false)
		    ->joinLeft($eName,"$eName.event_id = $tmName.resource_id",'')
		    ->where("$eName.search = ?", "1");
	    
	    $countSelect = $t_table->select()->from($tName,array("$tName.tag_id","Count($tName.tag_id) as count"));
	    $countSelect  -> joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
	    $countSelect  ->order("$tName.text");
	    $countSelect  ->group("$tName.text");
	    $countSelect  ->where("t.resource_type = ?","event");

	    $tagCounter = array();
	    foreach ($t_table->fetchAll($countSelect) as $tag)
	    {
	    	$tagCounter[$tag->tag_id] = $tag->count;
	    }
	    $this->view->tagCounter = $tagCounter;
	}
}