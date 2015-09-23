<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ShowSamePosterController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject('video')) {
            return $this->setNoRender();
        }        
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');
		
        // Set default title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('Suggested Videos');
        }
        $viewer = $subject->getOwner();

        // Get tags for this video
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());
		
		$owner_id = $subject->owner_id;
		$parent_type = $subject->parent_type;
        $select = $itemTable->select()
                ->from($itemTable)
                ->where('video_id != ?', $subject->getIdentity())
                ->where('search = ?', true) // ?
                ->order('rating DESC')
				->order('video_id DESC')
        ;
		
		if ($parent_type == 'user_playercard') {
			$parent_id = $subject->parent_id;
			$select->where("owner_id = $owner_id OR (parent_type = 'user_playercard' AND parent_id = $parent_id )");
		}
		else {
			$select->where("owner_id = ?", $owner_id);
		}
        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Hide if nothing to show
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
    }

}