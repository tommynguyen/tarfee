<?php
class Tfcampaign_ProfileController extends Core_Controller_Action_Standard
{
  	public function init()
  	{
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$subject = null;
  		if( !Engine_Api::_()->core()->hasSubject() )
  		{
  			$id = $this->_getParam('id');
  			if( null !== $id )
  			{
  				$subject = Engine_Api::_()->getItem('tfcampaign_campaign', $id);
				
  				if( $subject && $subject->getIdentity() )
  				{
  					Engine_Api::_()->core()->setSubject($subject);
  				}
  			}
  		}
  		$this->_helper->requireSubject('tfcampaign_campaign');
  	}
  
	public function indexAction()
	{
	    if(!Engine_Api::_()->core()->hasSubject()) 
		{
	    	return $this->_helper->requireSubject()->forward();
		}	
	    $this -> view -> campaign = $campaign = Engine_Api::_()->core()->getSubject();
		
		if (Engine_Api::_()->user()->itemOfDeactiveUsers($campaign)) {
			return $this->_helper->requireSubject()->forward();
		}
		
        // Check authorization to view campaign.
        if (!$campaign->isViewable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$campaign->isOwner($viewer))
		{
			$campaign -> view_count++;
			$campaign -> save();
		}
		
		$this -> view -> submissionPlayers =  $submissionPlayers = $campaign -> getSubmissionPlayers();
		$this -> _helper -> content	-> setEnabled();
  	}
}
