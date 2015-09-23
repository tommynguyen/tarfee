<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListLikedVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }
        
        $numberOfVideos = $this->_getParam('numberOfVideos', 4);
        
        $videoTable = Engine_Api::_()->getItemTable('video');
        $videoTableName = $videoTable->info('name');
        $select = $videoTable->select()->from($videoTableName)->setIntegrityCheck(false);
        $select->where('search = ?', 1)
                ->where('status = ?', 1);
        $likeTable = Engine_Api::_()->getDbTable('likes', 'core');
        $likeTableName = $likeTable->info('name');
        $likeVideoTableSelect = $likeTable->select()->where('resource_type = ?', 'video');
        $select->joinLeft($likeVideoTableSelect, "t.resource_id = $videoTableName.video_id");
        $select->group("$videoTableName.video_id");
        $select->order("count(t.like_id) DESC");
        $select->limit($numberOfVideos);
        
        $this->view->videos = $videoTable->fetchAll($select);
        
        // Hide if nothing to show
        if ($this->view->videos->count() == 0) {
            return $this->setNoRender();
        }
    }

}