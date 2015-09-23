<?php
class Ynevent_Widget_ProfileAnnouncementsController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject('event')) 
        {
            return $this->setNoRender();
        }
		
        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
		$table = Engine_Api::_() -> getItemTable('ynevent_announcement');
		$select = $table -> select() -> where("event_id = ?", $subject -> getIdentity()) -> where("highlight = 1")-> limit(1);
		$announcement = $table -> fetchRow($select);
		$this -> view -> announcement = $announcement;
		$this -> view -> event = $subject;
		$this -> view -> viewer = $viewer;
		if(!$announcement)
		{
			return $this->setNoRender();
		}
    }
}