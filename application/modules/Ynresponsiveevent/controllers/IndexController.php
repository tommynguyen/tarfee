<?php
class Ynresponsiveevent_IndexController extends Core_Controller_Action_Standard
{
  public function eventAction()
  {
    // Render
    $this->_helper->content
        ->setNoRender()
        ->setEnabled()
        ;
  }
  public function attendingAction()
  {
    $id = $this -> _getParam('id');
    $this -> view -> event = $event = Engine_Api::_() -> getItem('event', $id);
    $select = $event->membership()->getMembersObjectSelect();
    $this->view->members = $members = Zend_Paginator::factory($select);
    $members->setItemCountPerPage(1000);
  }
  public function eventFollowAction() 
  {
    $id = $this -> _getParam('event_id');
    $event = Engine_Api::_() -> getItem('event', $id);
    $viewer = Engine_Api::_()->user()->getViewer();
    $followTable = Engine_Api::_()->getDbTable('follow', 'ynevent');
    $row = $followTable->getFollowEvent($event->getIdentity(), $viewer->getIdentity());
    $this->view->viewer_id = $viewer->getIdentity();
    if ($row) 
    {
        $this->view->follow = $row->follow;
    } 
    else if($this->getRequest()->isPost())
    {
        $row = $followTable->createRow();
        $row->resource_id = $event->getIdentity();
        $row->user_id = $viewer->getIdentity();
    }
    else
    {
        return $this->_helper->viewRenderer->setNoRender(true);
    }
    $option_id = $this->getRequest()->getParam('option_id');
    $row->follow = $option_id;
    $row->save();
  }
}