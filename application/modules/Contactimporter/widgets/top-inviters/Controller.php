<?php

class Contactimporter_Widget_TopInvitersController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  	{
  		$invitationTbl = Engine_Api::_()->getDbTable("invitations", "contactimporter");
		$invitationTblName = $invitationTbl->info("name");
		$select = $invitationTbl->select()
			->from ($invitationTblName, array('inviter_id' => 'inviter_id', 'amount' => "COUNT(`invitation_id`)"))
			->group ("inviter_id")
			->order ("amount DESC")
			;
		$this->view->inviters = $inviters = $invitationTbl->fetchAll($select);
		if(!count($inviters))
		{
			$this -> setNoRender();
		}
  	}
}