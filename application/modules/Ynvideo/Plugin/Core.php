<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Plugin_Core
{

	public function onStatistics($event)
	{
		$table = Engine_Api::_() -> getDbTable('videos', 'ynvideo');
		$select = new Zend_Db_Select($table -> getAdapter());
		$select -> from($table -> info('name'), 'COUNT(*) AS count');
		$event -> addResponse($select -> query() -> fetchColumn(0), 'ynvideo');
	}

	public function onUserDeleteBefore($event)
	{
		$payload = $event -> getPayload();
		if ($payload instanceof User_Model_User)
		{

			// Delete videos
			$videoTable = Engine_Api::_() -> getDbtable('videos', 'ynvideo');
			$videoSelect = $videoTable -> select() -> where('owner_id = ?', $payload -> getIdentity());
			foreach ($videoTable->fetchAll($videoSelect) as $video)
			{
				Engine_Api::_() -> getApi('core', 'ynvideo') -> deleteVideo($video);
			}

			// Delete playlists
			$playlistTable = Engine_Api::_() -> getDbtable('playlists', 'ynvideo');
			$playlistSelect = $playlistTable -> select() -> where('user_id = ?', $payload -> getIdentity());
			foreach ($playlistTable->fetchAll($playlistSelect) as $playlist)
			{
				$playlist -> delete();
			}

			// Delete signatures
			$signatureTable = Engine_Api::_() -> getDbtable('signatures', 'ynvideo');
			$signatureTable -> delete("user_id = {$payload->getIdentity()}");
		}
	}
	
	public function onItemCreateAfter($event) {
		$payload = $event -> getPayload();
	}
	
	public function onItemDeleteBefore($event) {
		$payload = $event -> getPayload();
        if ($payload->getType() == 'activity_action' && $payload->object_type == 'video' && $payload->type == 'share') {
            $id = $payload->object_id;
            $video = Engine_Api::_()->getItem('video', $id);
            if ($video) {
                $video->share_count = $video->share_count - 1;
                $video->save();
            }   
        }
    }
}
