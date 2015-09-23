<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListRecentVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $marginLeft = $this->_getParam('marginLeft', '');
        if (!empty($marginLeft)) {
            $this->view->marginLeft = $marginLeft;
        }
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        if ($request->isPost()) {
//            $this->getElement()->setTitle('');                    
            $element = $this->getElement();
            $element->clearDecorators();            
//            $decorators = $element->getDecorators();
//            unset($decorators['Engine_Content_Decorator_Title']);
//            $element->setDecorators($decorators);
        }
        
        $numberOfVideos = $this->_getParam('numberOfVideos', 48);
        
        // Get paginator
        $recentType = $this->_getParam('recentType', 'creation');
        if (!in_array($recentType, array('creation', 'modified'))) {
            $recentType = 'creation';
        }
        $this->view->recentType = $recentType;
        $this->view->recentCol = $recentCol = $recentType . '_date';
        
        $table = Engine_Api::_()->getItemTable('video');
        $select = $table->select()
                ->where('search = ?', 1)
                ->where('status = ?', 1);
        if ($recentType == 'creation') {
            // using primary should be much faster, so use that for creation
            $select->order('video_id DESC');
        } else {
            $select->order($recentCol . ' DESC');
        }
        $select->limit($numberOfVideos);
        
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        
        $this->_childCount = $paginator->getTotalItemCount();
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->can_create = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')
                ->setAuthParams('video', null, 'create')->checkRequire();                        
    }
}