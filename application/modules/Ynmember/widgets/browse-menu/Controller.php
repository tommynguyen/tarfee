<?php
class Ynmember_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get navigation
    $this->view->navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('ynmember_main', array());
      
    if( count($this->view->navigation) == 1 ) {
      //$this->view->navigation = null;
    }
  }
}
