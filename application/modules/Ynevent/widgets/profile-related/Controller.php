<?php

class Ynevent_Widget_ProfileRelatedController extends Engine_Content_Widget_Abstract
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
		$subject = Engine_Api::_() -> core() -> getSubject('event');
		if (!$subject -> authorization() -> isAllowed($viewer, 'view'))
		{
			return $this -> setNoRender();
		}

		// Prepare data
		$this -> view -> event = $event = $subject;
		$limit = $this -> _getParam('max', 5);
		$currentDay = date('Y') . '-' . date('m') . '-' . date('d');

		$table = Engine_Api::_() -> getItemTable('event');
		$select = $table -> select() -> where('category_id = ?', $event -> category_id) -> where('event_id != ?', $event -> getIdentity()) -> order("DATEDIFF('{$currentDay}', starttime) DESC") -> limit($limit);
		$showedEvents = $table -> fetchAll($select);
		$this -> view -> showedEvents = $showedEvents;
		// Hide if nothing to show
		if (count($showedEvents) <= 0)
		{
			return $this -> setNoRender();
		}
	}

}
