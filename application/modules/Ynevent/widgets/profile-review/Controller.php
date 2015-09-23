<?php

class Ynevent_Widget_ProfileReviewController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		
		$this->view->subject = $subject;
		$this->view->event=$subject;
		$this->view->viewer_id = $viewer->getIdentity();
		$this->view->rating_count = Engine_Api::_()->ynevent()->ratingCount($subject->getIdentity());
		$this->view->rated = Engine_Api::_()->ynevent()->checkRated($subject->getIdentity(), $viewer->getIdentity());
		$this->view->maxReport = $maxReport = Engine_Api::_() -> getApi('settings', 'core')->getSetting('ynevent.max.review.report', 10);
		
		$table = Engine_Api::_()->getItemTable('ynevent_review');
		$tableName = $table->info('name');
		
		$ratingTable = Engine_Api::_()->getDbTable('ratings','ynevent');
		$ratingTableName = $ratingTable->info('name');
		
		$select = $table->select()->from($tableName)-> setIntegrityCheck(false);
		$select->joinLeft($ratingTableName, "$tableName.user_id = $ratingTableName.user_id" . " AND " . "$tableName.event_id = $ratingTableName.event_id", "$ratingTableName.rating" )
		->where("$tableName.event_id = ?", $subject->getIdentity());
		
		$this->view->reviews = $reviews = $table->fetchAll($select);

		$isPostedReview = false;
		$myReview = null;
		$aReviewIds = array();
		
		// GETTING DATA: LIST OF REVIEW
		foreach($reviews as $rev)
		{
			if ( $rev->user_id == $viewer->getIdentity() )
			{
				$isPostedReview = true;
				$myReview = $rev;
				//break;
			}
			$aReviewIds[] = $rev->getIdentity();
		}

		// GETTING DATA: NUMBER OF EVERY REVIEW
		$reviewReportTbl = Engine_Api::_()->getDbTable('reviewreports','ynevent');
		$reviewReportSelect = $reviewReportTbl->select();
		$reviewReportSelect
		->from($reviewReportTbl->info('name'), array('total_report' => 'COUNT(report_id)', 'review_id' => 'review_id'))
		->group("review_id");
		
		if (count($aReviewIds))
		{
			$reviewReportSelect->where("review_id IN (?)", $aReviewIds);
		}
		$reviewReports = $reviewReportTbl->fetchAll($reviewReportSelect);

		// GETTING NUMBER OF EVERY REVIEW
		$reportCount = array();
		foreach ($reviewReports as $reviewReport)
		{
			$reportCount[$reviewReport->review_id] = $reviewReport->total_report;
		}
		$this->view->reportCount = $reportCount;
		$this->view->isPostedReview = $isPostedReview;
		$this->view->myReview = $myReview;
		
		if (!$isPostedReview) //POSTED REVIEW
		{
			$this->view->form = $form = new Ynevent_Form_Review_Create(array('tab'=> $this->view->identity, 'event' => $subject));
			$request = Zend_Controller_Front::getInstance()->getRequest();
			if ($request->isPost() &&  $form->isValid($request->getPost()))
			{
				$table = Engine_Api::_()->getItemTable('ynevent_review');
				$viewer = Engine_Api::_()->user()->getViewer();
				$values = array_merge($form->getValues(), array(
						'event_id' => $subject->getIdentity(),
						'user_id' => $viewer->getIdentity(),
				));
					
				$review = $table->createRow();
				$review->setFromArray($values);
				$review->creation_date = date('Y-m-d H:i:s');
				$review->save();
				
				Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoRoute(array('id'=>$subject->getIdentity(), 'tab'=> $this->view->identity), 'event_profile');
			}
		}
		else //NOT POSTED YET
		{
			
		}
	}
	
	public function getChildCount()
	{
		return $this->_childCount;
	}
}