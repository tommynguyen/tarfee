<?php
class Tfcampaign_Widget_RecentCampaignController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$campaignTable = Engine_Api::_() -> getItemTable('tfcampaign_campaign');
		$this -> view -> campaigns = $campaigns = $campaignTable -> getCampaignsPaginator();
    	$campaigns->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
		if($campaigns -> getTotalItemCount() <= 0)
		{
			return $this -> setNoRender();
		}
	}
}
