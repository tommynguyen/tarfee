<?php
class Yncomment_Widget_CommentsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //GET SUBJECT
        $subject = null;
        if (Engine_Api::_()->core()->hasSubject()) {
            $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
        } else if (($subject = $this->_getParam('subject'))) {
            list($type, $id) = explode('_', $subject);
            $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
        } else if (($type = $this->_getParam('type')) &&
                ($id = $this->_getParam('id'))) {
            $this->view->subject = $subject = Engine_Api::_()->getItem($type, $id);
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->subjectSet = 0;

        if (empty($subject)) 
        {
            if (!$viewer->getIdentity()) 
            {
                return $this->setNoRender();
            }

            if ((isset($viewer->level_id) && $viewer->level_id != 1)) 
            {
                return $this->setNoRender();
            }
        }

        if ($subject) 
        {
            $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Yncomemnt/View/Helper', 'Yncomment_View_Helper');
            $this->view->subjectSet = 1;
            $params = $this->_getAllParams();
            $this->view->params = $params;
            if ($this->_getParam('loaded_by_ajax', false)) 
            {
                $this->view->loaded_by_ajax = true;
                if ($this->_getParam('is_ajax_load', false)) 
                {
                    $this->view->is_ajax_load = true;
                    $this->view->loaded_by_ajax = false;
                    if (!$this->_getParam('onloadAdd', false))
                        $this->getElement()->removeDecorator('Title');
                    $this->getElement()->removeDecorator('Container');
                    $this->view->showContent = true;
                }
            } else {
                $this->view->showContent = true;
            }

            if ($this->_getParam('taggingContent')) 
            {
                $this->view->taggingContent = implode($this->_getParam('taggingContent'), ",");
            }

            $showComposerOptions = $this->_getParam('showComposerOptions', array('addLink', 'addPhoto', 'addSmilies'));
            if ($showComposerOptions) 
            {
                $this->view->showComposerOptions = implode($showComposerOptions, ",");
            }
            
            $this->view->showAsNested = $this->_getParam('showAsNested', 1);
            $this->view->showAsLike = $this->_getParam('showAsLike', 1);
            $this->view->commentsorder = $this->_getParam('commentsorder', 1);
            $this->view->showDislikeUsers = $this->_getParam('showDislikeUsers', 1);
            $this->view->showLikeWithoutIcon = $this->_getParam('showLikeWithoutIcon', 1);
            $this->view->showLikeWithoutIconInReplies = $this->_getParam('showLikeWithoutIconInReplies', 1);
            $this->view->showAddLink = 0;
            $this->view->showAddPhoto = 0;
            $this->view->showSmilies = 0;
            $this->view->photoLightboxComment =$this->_getParam('photoLightboxComment', 0);
            
            if (!empty($showComposerOptions)) 
            {
                if (in_array('addLink', $showComposerOptions)) {
                    $this->view->showAddLink = 1;
                }

                if (in_array('addPhoto', $showComposerOptions)) {
                    $this->view->showAddPhoto = 1;
                }
                if (in_array('addSmilies', $showComposerOptions)) {
                    $this->view->showSmilies = 1;
                }
            }
            $this->view->nestedCommentPressEnter = Engine_Api::_()->getApi('settings', 'core')->getSetting('yncomment.comment.pressenter', 1);
        }
    }

}