<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_WatchLaterController extends Core_Controller_Action_Standard {
    public function init() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        $this->view->viewer = Engine_Api::_()->user()->getViewer();
    }

    public function indexAction() {
        $this->_helper->content->setNoRender()->setEnabled();
    }

    public function addToAction() {
        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
                null !== ($video = Engine_Api::_()->getItem('ynvideo_video', $video_id)) &&
                $video instanceof Ynvideo_Model_Video) {
            Engine_Api::_()->core()->setSubject($video);
        }

        if (!$this->_helper->requireSubject('video')->isValid()) {
            $data = array(
                'result' => 0,
                'message' => Zend_Registry::get('Zend_Translate')->_('The video doesn\'t exist'),
            );
            return $this->_helper->json($data);
        }
        
        if (!$this->_helper->requireAuth()->setAuthParams($video, null, 'view')->isValid()) {
            $data = array(
                'result' => 0,
                'message' => Zend_Registry::get('Zend_Translate')->_('You do not have the authorization to view this video'),
            );
            return $this->_helper->json($data);
        }
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        
        try {
            if (Engine_Api::_()->ynvideo()->addVideoToWatchLater($video_id, $this->view->viewer->getIdentity())) {
//                $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
//                $action = $actionTable->addActivity($viewer, $video, 'ynvideo_add_watchlater');
//                
//                if ($action != null) {
//                    $actionTable->attachActivity($action, $video);
//                }
//                
//                foreach ($actionTable->getActionsByObject($video) as $action) {
//                    $actionTable->resetActivityBindings($action);
//                }
                
                $data = array(
                    'result' => '1',
                    'message' => $this->view->htmlLink($this->view->url(array(), 'video_watch_later', true), 'Watch later')
                );
                return $this->_helper->json($data);
            }
        } catch(Ynvideo_Model_ExistedException $e) {
           $data = array(
                    'result' => -1,
                    'message' => Zend_Registry::get('Zend_Translate')->_('This video is existed in the watchlater list !!!'),
                );
           return $this->_helper->json($data);
        }
    }

    public function removeAction() {
        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
                null !== ($video = Engine_Api::_()->getItem('ynvideo_video', $video_id)) &&
                $video instanceof Ynvideo_Model_Video) {
            Engine_Api::_()->core()->setSubject($video);
        }

        if (!$this->_helper->requireSubject('video')->isValid()) {
            return;
        }

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = $form = new Ynvideo_Form_Remove(
            array(
                'title' => 'Remove video', 
                'description' => 'Are you sure you want to remove this video from the watch later?'
            )
        );

        if (!$video) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("The video doesn't exist.");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method.');
            return;
        }
        
        if (Engine_Api::_()->ynvideo()->removeVideoFromWatchLater($video->getIdentity(), $this->view->viewer->getIdentity())) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from the watch later.');
        } else {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('There is an error occured, please try again !!!');
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }
}