<?php
class Ynmember_Widget_ProfileStatisticsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		
		$this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}

		// Get subject and check auth
		$this ->view -> subject = $subject = Engine_Api::_()->core()->getSubject('user');
		$this ->view -> totalLikes = $subject->likes()->getLikePaginator()->getTotalItemCount();
		$notiTbl = Engine_Api::_()->getDbTable('notifications', 'ynmember');
		$this ->view -> totalNotifications = $notiTbl -> getNotificationCount($subject);
	}
}