<?php
class Ynblog_Widget_BlogsListingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
     $viewer = Engine_Api::_()->user()->getViewer();

    //Search Params
    $params = array();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $params['draft']       = 0;
    $params['is_approved'] = 1;
    $params['visible']     = 1;

    // Do the show thingy
    if(isset($params['show']) && $params['show'] == 2 ) {
      // Get an array of friend ids
      $table = Engine_Api::_()->getItemTable('user');
      $select = $viewer->membership()->getMembersSelect('user_id');
      $friends = $table->fetchAll($select);
      // Get stuff
      $ids = array();
      foreach( $friends as $friend )
      {
        $ids[] = $friend->user_id;
      }
      //unset($values['show']);
      $params['users'] = $ids;
    }

    $this->view->formValues = $params;
    //Get blog paginator
    $paginator = Engine_Api::_()->ynblog()->getBlogsPaginator($params);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.page',10);
    $paginator->setItemCountPerPage($items_per_page);

    if(isset($params['page'])){
      $paginator->setCurrentPageNumber($params['page']);
    }
    $this->view->paginator = $paginator;
  }
}
