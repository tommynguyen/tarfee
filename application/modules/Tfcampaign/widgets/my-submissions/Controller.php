<?php
class Tfcampaign_Widget_MySubmissionsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		
		$submissionTable = Engine_Api::_() -> getItemTable('tfcampaign_submission');
				
		//get campaigns that user has submited his players
		$this -> view -> submitCampaignIds = $submitCampaignIds = $submissionTable -> getCampaignIdsSubmitted($viewer);
		/*
		if(!count($submitCampaignIds)) {
			return $this -> setNoRender();
		}*/
	}
}
