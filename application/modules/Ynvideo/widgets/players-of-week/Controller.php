<?php
class Ynvideo_Widget_PlayersOfWeekController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		//get params
		$numberOfVideos = $this->_getParam('numberOfVideos', 5);
		$type = $this->_getParam('type', 'week');
		if ($type == 'week') {
			$date = $this->_getParam('weekDay', 'sunday');	
			$from_time = strtotime('previous '.$date.' midnight -7 days');
			$to_time = strtotime('previous '.$date.' midnight');
		}
		else {
			$hour = $this->getParam('dateHour', 0);
			$now = new DateTime();
			$configTime = new DateTime();
			$configTime->setTime($hour, 0, 0);
			if ($now > $configTime) {
				$from_time = $configTime->sub(new DateInterval('P1D'));
				$from_time = $from_time->getTimestamp();
				$to_time = $configTime->getTimestamp();
				
			}
			else {
				$to_time = $configTime->sub(new DateInterval('P1D'));
				$from_time = $to_time->sub(new DateInterval('P1D'));
				$from_time = $from_time->getTimestamp();
				$to_time = $to_time->getTimestamp();
			}
		}
		
		$from_time = date('Y-m-d H:i:s', $from_time);
		$to_time = date('Y-m-d H:i:s', $to_time);
		
		$share_point = $this->_getParam('share_internal', 2);
		$like_point = $this->_getParam('like', 3);
		$comment_point = $this->_getParam('comment', 2);
		$view_point = $this->_getParam('view', 1);
		$dislike_point = $this->_getParam('dislike', -1);
	//	$unsure_point = $this->_getParam('unsure', 0);	
		
		$table = Engine_Api::_()->getItemTable('video');
		$tableName = $table->info('name');
		$actionTbl = Engine_Api::_()->getDbTable('actions', 'activity');
		$actionTblName = $actionTbl->info('name');
		$likeTbl = Engine_Api::_()->getDbTable('likes', 'core');
		$likeTblName = $likeTbl->info('name');
		$commentTbl = Engine_Api::_()->getDbTable('comments', 'core');
		$commentTblName = $commentTbl->info('name');
		$viewTbl = Engine_Api::_()->getDbTable('views', 'ynvideo');
		$viewTblName = $viewTbl->info('name');
		$dislikeTbl = Engine_Api::_()->getDbTable('dislikes', 'yncomment');
		$dislikeTblName = $dislikeTbl->info('name');
		// $unsureTbl = Engine_Api::_()->getDbTable('unsures', 'yncomment');
		// $unsureTblName = $unsureTbl->info('name');
		$select = $table->select()
			->from($tableName, "$tableName.*,(count($actionTblName.action_id)*$share_point + count($likeTblName.like_id)*$like_point + count($commentTblName.comment_id)*$comment_point + count($viewTblName.view_id)*$view_point + count($dislikeTblName.dislike_id)*$dislike_point) as total_point")
			->setIntegrityCheck(false);
			
		$select->where("parent_type = ?", 'user_playercard');
		
		$select->joinLeft($actionTblName, "$actionTblName.object_type = 'video' AND $actionTblName.object_id = $tableName.video_id AND $actionTblName.type = 'share' AND $actionTblName.date >= '$from_time' AND $actionTblName.date <= '$to_time'", "");
		
		$select->joinLeft($likeTblName, "$likeTblName.resource_type = 'video' AND $likeTblName.resource_id = $tableName.video_id AND $likeTblName.date >= '$from_time' AND $likeTblName.date <= '$to_time'", "");
		
		$select->joinLeft($commentTblName, "$commentTblName.resource_type = 'video' AND $commentTblName.resource_id = $tableName.video_id AND $commentTblName.creation_date >= '$from_time' AND $commentTblName.creation_date <= '$to_time'", "");
		
		$select->joinLeft($viewTblName, "$viewTblName.video_id = $tableName.video_id AND $viewTblName.creation_date >= '$from_time' AND $viewTblName.creation_date <= '$to_time'", "");
		
		$select->joinLeft($dislikeTblName, "$dislikeTblName.resource_type = 'video' AND $dislikeTblName.resource_id = $tableName.video_id AND $dislikeTblName.creation_date >= '$from_time' AND $dislikeTblName.creation_date <= '$to_time'", "");
		
		// $select->joinLeft($unsureTblName, "$unsureTblName.resource_type = 'video' AND $unsureTblName.resource_id = $tableName.video_id AND $unsureTblName.creation_date >= '$from_time' AND $unsureTblName.creation_date <= '$to_time'", "");
		$select -> order('total_point DESC');
		$select->group("$tableName.parent_id");
		
		$rows = $table->fetchAll($select);
		$videos = array();
		foreach ($rows as $row) {
			if (count($videos) >= $numberOfVideos) break;
			$player = Engine_Api::_()->getItem('user_playercard', $row->parent_id);
			if ($player && $player->isViewable() && $row->authorization()->isAllowed(null, 'view')) {
				$videos[] = $row;
				
			}
		}
		
		if (empty($videos)) {
			return $this->setNoRender();
		}
		
		$this->view->videos = $videos;
	}
}
