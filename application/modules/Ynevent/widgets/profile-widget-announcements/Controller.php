<?php
class Ynevent_Widget_ProfileWidgetAnnouncementsController extends Engine_Content_Widget_Abstract 
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
		
		if(!$subject->isOwner($viewer))
		{
			return $this->setNoRender();
		}
		
		// Get subject and check auth
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }
		
		$itemCount = $this->_getParam('itemCountPerPage', 10);	
		
		$table = Engine_Api::_() -> getItemTable('ynevent_announcement');
		$select = $table -> select() -> where("event_id = ?", $subject -> getIdentity())->order('highlight')->limit($itemCount);
		$this -> view -> announcements = $table->fetchAll($select);
		$this -> view -> event = $subject;
		$this -> view -> viewer = $viewer;
		
		
    }
}