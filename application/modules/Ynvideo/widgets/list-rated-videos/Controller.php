<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListRatedVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Get paginator
        $table = Engine_Api::_()->getItemTable('video');
        $select = $table->select()
            ->where('search = ?', 1)
            ->where('status = ?', 1);
        $select->order('rating DESC');

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