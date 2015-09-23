<?php

class Ynevent_Widget_ProfileVideoController extends Engine_Content_Widget_Abstract
{
	public function indexAction() 
	{
		// Don't render this if not authorized
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		
		// Prepare data
		$this->view->event = $event = $subject;
		$highlightTbl = Engine_Api::_()->getDbTable('highlights', 'ynevent');
		$select = $highlightTbl->select()
		->where("event_id = ?", $event->getIdentity())
		->where("type = ?", 'video')
		->where("highlight = 1")
		->limit(1);
		
		$this->view->highlight = $highlight = $highlightTbl->fetchRow($select);
		$video = null;
		if($highlight)
		{
			$video = Engine_Api::_()->getItem('video', $highlight->item_id);
		}
		if (is_null($video))
		{
			return $this->setNoRender();
		}
		
		$this->view->video = $video;
		
		//get video player
		$view = true; $params = array();
		$session = new Zend_Session_Namespace('mobile');
		$mobile = $session -> mobile;
		$count_video = 0;
		if (isset($session -> count))
			$count_video = ++$session -> count;
		$paramsForCompile = array_merge(array(
				'video_id' => $video -> video_id,
				'code' => $video -> code,
				'view' => $view,
				'mobile' => $mobile,
				'duration' => $video -> duration,
				'count_video' => $count_video
		), $params);
		if ($video -> type == Ynevent_Plugin_Factory::getUploadedType())
		{
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_() -> ynresponsive1() -> isMobile();
			}
			if (!empty($video -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file1_id);
				}
				if ($storage_file)
				{
					$paramsForCompile['location1'] = $storage_file -> getHref();
					$paramsForCompile['location'] = '';
				}
			}
			else
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$paramsForCompile['location'] = $storage_file -> getHref();
					$paramsForCompile['location1'] = '';
				}
			}
		}
		else if ($video -> type == Ynevent_Plugin_Factory::getVideoURLType())
		{
			$paramsForCompile['location'] = $video -> code;
		}
		$videoEmbedded = Ynevent_Plugin_Factory::getPlugin((int)$video -> type) -> compileVideo($paramsForCompile);

		$this->view->videoEmbedded = $videoEmbedded; 
	}
}