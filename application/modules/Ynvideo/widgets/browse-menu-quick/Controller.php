<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_BrowseMenuQuickController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Get quick navigation
        $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynvideo_quick');
    }

}
