<?php
class Tfcampaign_Widget_ProfileSubmissionController extends Engine_Content_Widget_Abstract
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
		$params  = Zend_Controller_Front::getInstance()->getRequest() -> getParams();
		$params['campaign_id'] = $campaign -> getIdentity();
		//for filter
		$filterType = (isset($params['sort']))? $params['sort'] : 'matching';
		switch ($filterType) {
			case 'location':
				$params['order'] = "location.title";
				$params['direction'] = "ASC";
				break;
			case 'age':
				$params['order'] = "player.birth_date";
				$params['direction'] = "DESC";
				break;
			case 'gender':
				$params['order'] = "player.gender";
				$params['direction'] = "DESC";
				break;
			case 'rating': 
				$params['order'] = "player.rating";
				$params['direction'] = "DESC";
				break;
		}
		
		$submissionTable = Engine_Api::_() -> getItemTable('tfcampaign_submission');
		$submissionPlayers = $submissionTable -> fetchAll($submissionTable -> getSubmissionsSelect($params));
		
		switch ($filterType) {
				case 'matching':
					$arrMatch = array();
					foreach($submissionPlayers as $submissionPlayer) {
						$arrMatch[$submissionPlayer -> getIdentity()] = $submissionPlayer -> countPercentMatching();
					}
					arsort($arrMatch);
					$submissionPlayers = null;
					foreach($arrMatch as $key => $value) {
						$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $key);
						if($submission)
							$submissionPlayers[] = $submission;
					}
					break;
		}
		$this -> view -> filterType = $filterType;
		$this -> view -> submissionPlayers = $submissionPlayers;
		if(count($submissionPlayers) <= 0 || !$campaign -> isOwner($viewer))
		{
			return $this -> setNoRender();
		}
	}
}
