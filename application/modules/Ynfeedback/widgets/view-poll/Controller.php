<?php
class Ynfeedback_Widget_ViewPollController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $viewer = Engine_Api::_()->user()->getViewer();
		if(!$viewer -> getIdentity())
		{
			return $this -> setNoRender();
		}
		$tablePoll = Engine_Api::_() -> getItemTable('ynfeedback_poll');
		$select = $tablePoll -> select() -> where("`show` = 1") -> limit(1);
		$poll = $tablePoll -> fetchRow($select);
		if(empty($poll))
		{
			return $this -> setNoRender();
		}
	    $this->view->poll = $poll;
	    $this->view->owner = $owner = $poll->getOwner();
	    $this->view->viewer = $viewer;
	    $this->view->pollOptions = $poll->getOptions();
	    $this->view->hasVoted = $poll->viewerVoted();
	    $this->view->showPieChart = true;
	    $this->view->canVote = true;
	    $this->view->canChangeVote = true;
    }
}
