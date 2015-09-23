<?php

/**
 * @category ynvideo
 * @package widget
 * @subpackage search-manage-videos
 * @author dang tran
 */
class Ynvideo_Widget_ListManageVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // Process form
        $values = $request->getParams();
        $values['user_id'] = $viewer->getIdentity();
        if (isset($values['category'])) {
            $this->view->category = $values['category'];
        }

        if (!empty($values['parent_type']) && !empty($values['subject_id'])) {
            $item = Engine_Api::_()->getItem($values['parent_type'], $values['subject_id']);
            if ($item && $item instanceof Core_Model_Item_Abstract) {
                $this->view->item = $item;
            }
        }    
        
        if (isset($values['parent_type'])) {
            unset($values['parent_type']);
        }
        if (isset($values['subject_type'])) {
            unset($values['subject_type']);    
        }        
        
        $this->view->paginator = $paginator =
            Engine_Api::_()->getApi('core', 'ynvideo')->getVideosPaginator($values);

        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('ynvideo.page', 10);
        $this->view->paginator->setItemCountPerPage($items_count);

        $this->view->paginator->setCurrentPageNumber($request->getParam('page', 1));

        // maximum allowed videos
        $this->view->quota = $quota = (int) Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'video', 'max');
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->params = $values;
    }

}