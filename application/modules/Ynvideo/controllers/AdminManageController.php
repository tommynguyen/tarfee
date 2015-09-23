<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_AdminManageController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynvideo_admin_main', array(), 'ynvideo_admin_main_manage');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $video = Engine_Api::_()->getItem('video', $value);
                    $video->delete();
                }
            }
        }

        $params = $this->_getAllParams();
        $this->view->paginator = Engine_Api::_()->ynvideo()->getVideosPaginator($params, false);
        
        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);
        
        // Video Search Form
        $this->view->form = $form = new Ynvideo_Form_Admin_Search();
        $form->populate($params);
        $formValues = $form->getValues();
        if (isset($params['fieldOrder'])) {
            $formValues['fieldOrder'] = $params['fieldOrder'];
        }
        if (isset($params['order'])) {
            $formValues['order'] = $params['order'];
        }
        $this->view->params = $formValues;
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->video_id = $id;
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $video = Engine_Api::_()->getItem('video', $id);
                Engine_Api::_()->getApi('core', 'ynvideo')->deleteVideo($video);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The video is deleted successfully.'))
            ));
        }
        
        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage/delete.tpl');
    }

    public function killAction() {
        $id = $this->_getParam('video_id', null);
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $video = Engine_Api::_()->getItem('video', $id);
                $video->status = 3;
                $video->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    public function setFeatureAction() {
        $id = $this->_getParam('video_id', null);
        if ($id) {
            $video = Engine_Api::_()->getItem('video', $id);
            if ($video) {
                $video->featured = !($video->featured);
                if ($video->type != '6')
                {
                    Engine_Api::_()->ynvideo()->fetchVideoLargeThumbnail($video);
                }
                $video->save();
                $this->view->status = 1;
                $this->view->featured = $video->featured;
            } else {
                $this->view->status = 0;
            }
        } else {
            $this->view->status = 0;
        }
    }
}