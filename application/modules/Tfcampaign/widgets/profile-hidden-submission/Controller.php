<?php
class Tfcampaign_Widget_ProfileHiddenSubmissionController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> setNoRender();
		}
		// Get subject and check auth
		$this -> view -> campaign = $campaign = Engine_Api::_() -> core() -> getSubject('tfcampaign_campaign');
		if(!$viewer -> isSelf($campaign -> getOwner())) {
			return $this -> setNoRender();
		}
		$params  = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
		$params['campaign_id'] = $campaign -> getIdentity();
		$params['hided'] = 1;
		
		$submissionTable = Engine_Api::_() -> getItemTable('tfcampaign_submission');
		$submissionPlayers = $submissionTable -> fetchAll($submissionTable -> getSubmissionsSelect($params));
		$this -> view -> submissionPlayers = $submissionPlayers;
		if(!count($submissionPlayers) || !$campaign -> isOwner($viewer)) {
			return $this -> setNoRender();
		}
	}
}
