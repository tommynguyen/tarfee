<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Form_Playlist_Edit extends Ynvideo_Form_Playlist_Create {
    private $_playlist;

    public function __construct($options = null) {
        if (is_array($options) && array_key_exists('playlist', $options)) {
            $this->_playlist = $options['playlist'];
            unset($options['playlist']);
        }

        parent::__construct($options);
    }

    protected function initValueForElements() {
        $this->getElement('title')->setValue($this->_playlist->title);
        $this->getElement('description')->setValue($this->_playlist->description);

        // fill in data for the authentication view and authentication comment element
        $authViewElement = $this->getElement('auth_view');
        $authCommentElement = $this->getElement('auth_comment');

        $auth = Engine_Api::_()->authorization()->context;
        if ($authViewElement && !$authViewElement instanceof Engine_Form_Element_Hidden) {
          foreach ($this->_roles as $key => $role) {
              if ($auth->isAllowed($this->_playlist, $key, 'view')) {
                  $authViewElement->setValue($key);
                  break;
              }
          }
        }
        if ($authCommentElement && !$authCommentElement instanceof Engine_Form_Element_Hidden) {
          foreach ($this->_roles as $key => $role) {
            if ($auth->isAllowed($this->_playlist, $key, 'comment')) {
                $authCommentElement->setValue($key);
                break;
            }
          }
        }
    }

    public function init() {
        parent::init();

        $this->submit->setLabel('Save changes');
    }

    protected function createImageFileElement() {
        if (!empty($this->_playlist->photo_id)) {
            $photo = new Engine_Form_Element_Image('photo_delete',
                array(
                    'src' => $this->_playlist->getPhotoUrl('thumb.profile'),
                    'class' => 'ynvideo_thumb'
                ));
            $photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
            $this->addElement($photo);
            $this->addDisplayGroup(array('photo_delete'), 'photo_delete_group');
        }

        // Photo
        $file_element = new Engine_Form_Element_File('photo', array(
            'label' => 'Playlist Image (optional)',
            'description' => 'When a new photo is uploaded, the old one will be deleted. The image should have the size (120px * 90px)',
            'size' => '40'
        ));
        $file_element->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement($file_element);
        $this->addDisplayGroup(array('photo'), 'photo_group');

        if (!empty($this->_post->photo_id)) {
            $this->getDisplayGroup('photo_group')->getDecorator('HtmlTag')->setOption('style', 'display:none;');
        }
    }

}