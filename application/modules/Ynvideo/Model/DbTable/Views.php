<?php

class YnVideo_Model_DbTable_Views extends Engine_Db_Table {
    protected $_name = 'ynvideo_views';
	
	public function addView($video) {
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity()) return false;
		$row = $this->createRow();
		$row->user_id = $viewer->getIdentity();
		$row->video_id = $video->getIdentity();
		$row->creation_date = date('Y-m-d H:i:s');
		$row->save();
		return true;
	}
}