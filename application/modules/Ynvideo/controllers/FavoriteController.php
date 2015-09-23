<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_FavoriteController extends Core_Controller_Action_Standard {

    public function init() {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    }

    //put your code here
    public function indexAction() {
        // Render
        $this->_helper->content->setNoRender()->setEnabled();        
    }

    public function addAction() {
        if (0 !== ($video_id = (int) $this->_getParam('video_id')) &&
                null !== ($video = Engine_Api::_()->getItem('video', $video_id)) &&
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
                'message' => Zend_Registry::get('Zend_Translate')->_('You do not have the authorization to view this video.'),
            );
            return $this->_helper->json($data);
        }
        
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        if (isset($video)) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $favorite = Engine_Api::_()->ynvideo()->addVideoToFavorite($video->getIdentity(), $this->view->viewer->getIdentity());
                if ($favorite) {
                    // CREATE AUTH STUFF HERE
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($favorite, 'registered', 'view', true);
                    $auth->setAllowed($favorite, 'registered', 'comment', true);
            
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $action = $actionTable->addActivity($viewer, $favorite, 'ynvideo_add_favorite');

                    if ($action != null) {
                        $actionTable->attachActivity($action, $video);
                    }

                    foreach ($actionTable->getActionsByObject($favorite) as $action) {
                        $actionTable->resetActivityBindings($action);
                    }

                    $db->commit();

                    $data = array(
                        'result' => 1,
                        'message' => $this->view->htmlLink($this->view->url(array(), 'video_favorite', true), 'Favorite')
                    );
                    return $this->_helper->json($data);
                }
            } catch (Ynvideo_Model_ExistedException $e) {
                $data = array(
                    'result' => -1,
                    'message' => Zend_Registry::get('Zend_Translate')->_('This video is existed in the favorite list !!!'),
                );
                return $this->_helper->json($data);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        $data = array(
            'result' => 0,
            'message' => Zend_Registry::get('Zend_Translate')->_('There is an error occured. Please try again !!!'),
        );
        return $this->_helper->json($data);
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
                            'remove_title' => 'Remove video',
                            'remove_description' => 'Are you sure you want to remove this video from your favorite videos?'
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

        if (Engine_Api::_()->ynvideo()->removeVideoFromFavorite($video->getIdentity(), $this->view->viewer->getIdentity())) {
            $this->view->status = true;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('The video has been removed from your favorite videos.');
        } else {
            $this->view->status = false;
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('There is an error occured, please try again.');
        }

        return $this->_forward('success', 'utility', 'core', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_($this->view->message)),
            'layout' => 'default-simple',
            'parentRefresh' => true,
        ));
    }

	public function removeFavoriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$video_id = (int) $this->_getParam('video_id');
        $video = Engine_Api::_()->getItem('ynvideo_video', $video_id);
		if ($video) 
		{
			Engine_Api::_()->ynvideo()->removeVideoFromFavorite($video->getIdentity(), $this->view->viewer->getIdentity());
		}
		return $this -> _helper -> json(array('status' => true));
	}
	public function addFavoriteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_()->user()->getViewer();
		$video_id = (int) $this->_getParam('video_id');
        $video = Engine_Api::_()->getItem('ynvideo_video', $video_id);
		if (isset($video)) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $favorite = Engine_Api::_()->ynvideo()->addVideoToFavorite($video->getIdentity(), $this->view->viewer->getIdentity());
                if ($favorite) {
                    // CREATE AUTH STUFF HERE
                    $auth = Engine_Api::_()->authorization()->context;
                    $auth->setAllowed($favorite, 'registered', 'view', true);
                    $auth->setAllowed($favorite, 'registered', 'comment', true);
            
                    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
                    $action = $actionTable->addActivity($viewer, $favorite, 'ynvideo_add_favorite');

                    if ($action != null) {
                        $actionTable->attachActivity($action, $video);
                    }

                    foreach ($actionTable->getActionsByObject($favorite) as $action) {
                        $actionTable->resetActivityBindings($action);
                    }

                    $db->commit();
                }
            } catch (Ynvideo_Model_ExistedException $e) {
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
			return $this -> _helper -> json(array('status' => true));
        }
	}
}

?>