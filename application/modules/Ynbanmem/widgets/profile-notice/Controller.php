<?php

class Ynbanmem_Widget_ProfileNoticeController extends Engine_Content_Widget_Abstract
{
   public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
	$subject = Engine_Api::_()->core()->getSubject('user');
	 if ($subject->user_id != $viewer->user_id || !Engine_Api::_()->authorization()->isAllowed('ynbanmem', $viewer, 'action')) {
            return $this->setNoRender();
        }
		
	
 
	// Get level are allowed to view notice message
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	$usersTable = Engine_Api::_()->getDbtable('users', 'user');
	$select = $permissionsTable->select()
                ->from($permissionsTable,'level_id')
                ->where('type = ?', 'ynbanmem')
                ->where('name = ?', 'action')
                ->query()
                ->fetchAll();
		
         $users = $usersTable->select()
                ->from($usersTable,'user_id')
                ->where('level_id IN (?)', $select)
                ->query()
                ->fetchAll();
         //print_r($users);die;
	$conversationsTable  =	Engine_Api::_()->getDbtable('conversations', 'ynbanmem');
        
    $this->view->paginator = $paginator = $conversationsTable->getAllOutboxPaginator($users);
	
	//$this->view->paginator = $paginator = $conversationsTable->getOutboxPaginator($viewer);
   $paginator->setCurrentPageNumber($this->_getParam('page'));
   if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

   
    //$this->view->unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    // Render
   // $this->_helper->content
        //->setNoRender()
       // ->setEnabled()
      //  ;
     
  }
  

}
