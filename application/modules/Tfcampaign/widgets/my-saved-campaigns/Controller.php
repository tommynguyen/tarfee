<?php
class Tfcampaign_Widget_MySavedCampaignsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		
		$saveTable = Engine_Api::_() -> getDbTable('saves', 'tfcampaign');
				
		//get saved campaigns
		$this -> view -> saveRows = $saveRows = $saveTable -> getSavedCampaigns($viewer -> getIdentity());
		/*
		if(!count($saveRows)) {
			return $this -> setNoRender();
		}*/
	}
}
