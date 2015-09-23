<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListPopularVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }
        
        // Should we consider views or comments popular?
        $popularType = $this->_getParam('popularType', 'view');
        if (!in_array($popularType, array('view', 'comment', 'rating'))) {
            $popularType = 'view';
        }
        $this->view->viewType = $this->_getParam('viewType', 'small');
        
        $this->view->infoCol = 'view';
        if ($popularType == 'comment') {
            $this->view->infoCol = 'comment';
        }
        $this->view->popularType = $popularType;
        if ($popularType == 'rating') {
            $this->view->popularCol = $popularCol = 'rating';
        } else {
            $this->view->popularCol = $popularCol = $popularType . '_count';
        }

        // Get paginator
        $table = Engine_Api::_()->getItemTable('video');
        $select = $table->select()
                ->where('search = ?', 1)
                ->order($popularCol . ' DESC');
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