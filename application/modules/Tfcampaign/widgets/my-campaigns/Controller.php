<?php
class Tfcampaign_Widget_MyCampaignsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		
		$campaignTable = Engine_Api::_() -> getItemTable('tfcampaign_campaign');
				
		//get all campaigns user own
		$this -> view -> ownCampaigns = $ownCampaigns = $campaignTable -> getCampaignsByUser($viewer);
		/*
		if(!count($ownCampaigns)) {
			return $this -> setNoRender();
		}
		*/
	}
}
