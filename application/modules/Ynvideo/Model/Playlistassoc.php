<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Playlistassoc extends Core_Model_Item_Abstract {
    protected function _postDelete() {
        parent::_postDelete();
        $playlist = Engine_Api::_()->getItem('ynvideo_playlist', $this->playlist_id);
        if ($playlist) {
            $playlist->video_count = new Zend_Db_Expr('video_count - 1');
            $playlist->save();
        }
    }
}