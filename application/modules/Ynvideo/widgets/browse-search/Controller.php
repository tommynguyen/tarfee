<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();

        // Make form
        $this->view->form = $form = new Ynvideo_Form_Search();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynvideo') {
            if ($controller == 'favorite' 
                || ($controller == 'playlist' && $action == 'view')
                || $controller == 'watch-later'
                || ($controller == 'index' && $action == 'manage')) {
                $forwardListing = false;
            } 
        }
        if ($forwardListing === true) {
            $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'list'), 'video_general', true));
        }

        // Process form
        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
        if ($form->isValid($p)) {
            $values = $form->getValues();
        } else {
            $values = array();
        }
        $this->view->formValues = $values;

        $values['status'] = 1;
        $values['search'] = 1;

        $this->view->category = $values['category'];


        if (!empty($values['tag'])) {
            $this->view->tag = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
        }

        // check to see if request is for specific user's listings
        $user_id = $this->_getParam('user');
        if ($user_id) {
            $values['user_id'] = $user_id;
        }
    }
}