<?php

class Ynbanmem_Widget_ExtraStatisticController extends Engine_Content_Widget_Abstract
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
    
   // $paginator = $conversationsTable->getAllOutboxPaginator($users);
	
	$notices = 0;
	$warnings = 0;
	$infractions = 0;
	$i =0;
	
	$bannedEmailTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
	$bannedEmails = $bannedEmailTable->getAllBannedEmails();
	$bannedIpTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
	$bannedIps = $bannedIpTable->getAddresses();
	$bannedUsernameTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
	$bannedUsernames = $bannedUsernameTable->getAllBannedUsers();
	
	//get all
	$rDb = Engine_Api::_()->getDbtable('recipients', 'messages');
	$rName = $rDb->info('name');
	$cDb = Engine_Api::_()->getDbtable('conversations', 'messages');
    $cName = $cDb->info('name');
    $select = $rDb->select()
      ->from($rName)
      //>joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` In (?)", $users)
      ->where("`{$rName}`.`outbox_deleted` = ?", 0)
      //->order(new Zend_Db_Expr('outbox_updated DESC'))
	   ->query()
	   ->fetchAll();
	// print_r($select);die;
	foreach( $select as $rec )
	{
	//echo $recipient['outbox_message_id'];die;
      //$user = Engine_Api::_()->getItem('user', $rec['user_id']);
       // $message = $rec->getOutboxMessage($user);
        //$recipient = $rec->getRecipientInfo($user);
		$tb = Engine_Api::_()->getDbTable('extramessage','ynbanmem');
               // echo $recipient->outbox_message_id;
		$extra = $tb->getExtraMessage($rec['outbox_message_id']);
		if(count($extra) == 0)
		{
			continue;}
		else
		{
			switch($extra[0]['type'])
			{
				case 1:
					$notices++;
					break;
				case 2:
					$warnings++;
					break;
				case 3:
					$infractions++;
					break;
			}
		}
	
	}
	
	$this->view->notices = $notices;
	$this->view->warnings = $warnings;
	$this->view->infractions = $infractions;
	$this->view->bannedUsernames = count($bannedUsernames);
	$this->view->bannedEmails = count($bannedEmails);
	$this->view->bannedIps = count($bannedIps);
	//$this->view->paginator = $paginator = $conversationsTable->getOutboxPaginator($viewer);
  
    //$this->view->unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    // Render
   // $this->_helper->content
        //->setNoRender()
       // ->setEnabled()
      //  ;
     
  }

}
