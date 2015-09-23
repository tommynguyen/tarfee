<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Edit extends Ynvideo_Form_Video {
    private $_video;  

    public function setVideo($value)
    {
      $this->_video = $value;
    }

    protected function initValueForElements() {
        
        $this->populate($this->_video->toArray());
        
		/*
        // prepare tags
        $videoTags = $this->_video->tags()->getTagMaps();

        $tagString = '';
        foreach ($videoTags as $tagmap) {
            if ($tagString !== '')
                $tagString .= ', ';
            $tagString .= $tagmap->getTag()->getTitle();
        }

        $this->tags->setValue($tagString);
		 */
        
        // set view authentication and comment authentication for the two dropdownlists
        $authViewElement = $this->getElement('auth_view');
        $authCommentElement = $this->getElement('auth_comment');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_video, $key, 'view')) {
                    $authViewElement->setValue($key);
                    break;
                }
            }
        }

        if ($authCommentElement) {
            foreach ($this->_roles as $key => $role) {
                if ($auth->isAllowed($this->_video, $key, 'comment')) {
                    $authCommentElement->setValue($key);
                    break;
                }
            }
        }
    }
    
    protected function addAdditionalElements() {
    }
}