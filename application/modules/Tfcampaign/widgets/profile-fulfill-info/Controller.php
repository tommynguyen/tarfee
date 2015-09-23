<?php
class Tfcampaign_Widget_ProfileFulfillInfoController extends Engine_Content_Widget_Abstract
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
		
		//age
		if($campaign -> from_age == 0 && $campaign -> from_age == 0) {
			$this -> view -> type = 'age';
		}
		else if(!$campaign -> gender) {
			//gender 
			$this -> view -> type = 'gender';
		} 
		else if(!$campaign -> country_id) {
			//country 
			$this -> view -> type = 'country';
		} else {
			return $this -> setNoRender();
		}
	}
}
