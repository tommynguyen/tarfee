<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListMyFavoriteVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        
        $select = Engine_Api::_()->ynvideo()->getVideosSelect($params);

        $videoTable = Engine_Api::_()->getDbTable('videos', 'ynvideo');
        $videoTableName = $videoTable->info('name');
        $favoriteTable = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
        $favoriteTableName = $favoriteTable->info('name');
        $select->setIntegrityCheck(false)
            ->join($favoriteTableName, $favoriteTableName . ".video_id = " . $videoTableName . ".video_id")
            ->where("$favoriteTableName.user_id = ?", $viewer->getIdentity());

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($request->getParam('page'), 1);
        $paginator->setItemCountPerPage($settings->getSetting('ynvideo.page', 10));
    }

}