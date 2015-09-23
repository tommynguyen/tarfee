<?php

class Contactimporter_Widget_StatisticsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  	{
  		// Don't render this if not authorized
	    $viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer->getIdentity())
		{
			return $this->setNoRender();
		}
		
		// GET AMOUNT OF EMAILS
		$mailTbl = Engine_Api::_()->getDbTable("mail", "core");
		$mailTblName = $mailTbl->info("name");
		$invitationsTbl = Engine_Api::_() -> getDbTable('invitations', 'contactimporter');
		$invitationsTblName = $invitationsTbl->info("name"); 
		
		$select = $invitationsTbl -> select()
			-> from($invitationsTblName, "COUNT($invitationsTblName.inviter_id)") 
			-> join($mailTblName, $invitationsTblName . '.mail_id=' . $mailTblName . '.mail_id', null)
			-> where("$invitationsTblName.inviter_id = ? ", $viewer -> getIdentity()) 
			-> where("$invitationsTblName.type = ? ", 'email')
			-> where("$invitationsTblName.mail_id > 0");
		$numOfQueueEmails = $select->query()->fetchColumn(0);

		// GET AMOUNT OF MESSAGES 
		$queuesTable = Engine_Api::_() -> getDbtable('queues', 'socialbridge');
		$select = $queuesTable -> select() 
			-> where('user_id = ? ', $viewer -> getIdentity())
			-> where('type = ?', "sendInvite");
		
		$queues = $queuesTable->fetchAll($select);
		$facebook = array(); $twitter = array();
		$numOfQueueMessages = 0;
		foreach($queues as $queue)
		{
			$exParams = $queue->extra_params;
			$exParams = unserialize($exParams);
			if (count($exParams['list']))
			{
				$numOfQueueMessages += count($exParams['list']);
			}
		}
		
		//Remaining invitations
		$this->view->remainingAmount = $numOfQueueEmails + $numOfQueueMessages;
		
		$invitationTbl = Engine_Api::_()->getDbTable("invitations", "contactimporter");
		$invitationTblName = $invitationTbl->info("name");
		$select = $invitationTbl->select()
			->from ($invitationTblName, "COUNT(`invitation_id`)")
			->where ("inviter_id = ?", $viewer->getIdentity())
			->group ("inviter_id");

		//Total sent invitations
		$totalSentAmount = $select->query()->fetchColumn(0);
		$this->view->totalSentAmount = ($totalSentAmount) ? $totalSentAmount : 0;
		
		$joinedTbl = Engine_Api::_() -> getDbTable('joined', 'contactimporter');
		$joinedTblName = $joinedTbl->info("name");
		$select = $joinedTbl -> select()
			-> from($joinedTblName, 'COUNT(`joined_id`)') 
			-> where('inviter_id = ? ', $viewer -> getIdentity());
		
		//Total jointd friends of this user
		$this->view->totalFriendAmount = $select->query()->fetchColumn(0); 
		
  	}
}