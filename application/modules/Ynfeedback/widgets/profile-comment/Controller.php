<?php
class Ynfeedback_Widget_ProfileCommentController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		// Get subject
		$subject = null;
		if( Engine_Api::_()->core()->hasSubject() ) 
		{
			$subject = Engine_Api::_()->core()->getSubject();
		} 
		else if( ($subject = $this->_getParam('subject')) ) 
		{
			list($type, $id) = explode('_', $subject);
			$subject = Engine_Api::_()->getItem($type, $id);
		} 
		else if( ($type = $this->_getParam('type')) && ($id = $this->_getParam('id')) ) 
		{
			$subject = Engine_Api::_()->getItem($type, $id);
		}
		
		// Perms
		$viewer = Engine_Api::_()->user()->getViewer();
		$this->view->canComment = $canComment = $subject->isCommentable();
		$this->view->canDelete = $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
		
		// Likes
		$this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
		$this->view->likes = $likes = $subject->likes()->getLikePaginator();

		// If has a page, display oldest to newest
		if( null !== ( $page = $this->_getParam('page')) ) 
		{
			$commentSelect = $subject->comments()->getCommentSelect();
			$commentSelect->order('comment_id ASC');
			$comments = Zend_Paginator::factory($commentSelect);
			$comments->setCurrentPageNumber($page);
			$comments->setItemCountPerPage(10);
			$this->view->comments = $comments;
			$this->view->page = $page;
		} 
		else 
		{
			// If not has a page, show the
			$commentSelect = $subject->comments()->getCommentSelect();
			$commentSelect->order('comment_id DESC');
			$comments = Zend_Paginator::factory($commentSelect);
			$comments->setCurrentPageNumber(1);
			$comments->setItemCountPerPage(4);
			$this->view->comments = $comments;
			$this->view->page = $page;
		}
		
		if( $canComment ) 
		{
			$this->view->form = $form = new Ynfeedback_Form_Comment_Create();
			$form->populate(array(
		        'identity' => $subject->getIdentity(),
		        'type' => $subject->getType(),
			));
		}
		
		//check follow
		$followTable = Engine_Api::_() -> getDbTable('follows', 'ynfeedback');
		$followRow = $followTable -> getFollowIdea($subject->getIdentity(), $viewer -> getIdentity());
		if(!$followRow)
		{
			$this -> view -> follow = false;
		}
		else {
			$this -> view -> follow = true;
		}
	}
}
