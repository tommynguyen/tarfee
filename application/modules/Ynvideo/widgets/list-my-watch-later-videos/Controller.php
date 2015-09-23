<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListMyWatchLaterVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $settings = Engine_Api::_()->getApi('settings', 'core');
        
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideo');
        $videoTblName = $videoTbl->info('name');
        $watchLaterTbl = Engine_Api::_()->getDbTable('watchlaters', 'ynvideo');
        $watchLaterTblName = $watchLaterTbl->info('name');
        
        $params = $request->getParams();
        $select = Engine_Api::_()->ynvideo()->getVideosSelect($params, false);        
        $select->setIntegrityCheck(false)
                ->join($watchLaterTblName, $watchLaterTblName . ".video_id = " . $videoTblName . ".video_id", "$watchLaterTblName.watched")
                ->order(array("$watchLaterTblName.watched ASC", "$watchLaterTblName.creation_date DESC"))
                ->where("$watchLaterTblName.user_id = ?", $viewer->getIdentity())
                ->where("$videoTblName.search = 1")
                ->where("$videoTblName.status = 1");
        $this->view->params = $_GET;
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        // Set item count per page and current page number
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
        $paginator->setItemCountPerPage($settings->getSetting('ynvideo.page', 10));
    }

}