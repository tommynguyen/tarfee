<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Plugin_Menus {

    public function canCreateVideos() {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnvideoMainFavoriteVideo($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }
    
    public function onMenuInitialize_YnvideoMainManage($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnvideoMainPlaylist($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnvideoMainWatchLater($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return false;
        }

        return true;
    }

    public function onMenuInitialize_YnvideoMainCreate($row) {
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->authorization()->isAllowed('video', $viewer, 'create')) {
            return false;
        }

        return true;
    }

}