<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListMyPlaylistsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        
        $order_by = strtoupper($request->getParam('order_by', 'desc'));
        if ($order_by != 'ASC' || $order_by != 'DESC') {
            $order_by != 'DESC';
        }
        
        $table = Engine_Api::_()->getDbTable('playlists', 'ynvideo');
        $select = $table->select();
        $select->where('user_id = ?', $viewer->getIdentity());
        $select->order("creation_date $order_by");

        $this->view->order_by = $order_by;
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        $this->view->can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynvideo_playlist', null, 'create')->checkRequire();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $playlistPerPage = $settings->getSetting('ynvideo.playlist.per.page', 10);
        
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($playlistPerPage);
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
    }

}