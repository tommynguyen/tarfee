<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Plugin_Composer extends Core_Plugin_Abstract {

    public function onAttachVideo($data) {
        if (!is_array($data) || empty($data['video_id'])) {
            return;
        }

        $video = Engine_Api::_()->getItem('video', $data['video_id']);
        // update $video with new title and description
        $video->title = $data['title'];
        $video->description = $data['description'];

        // Set parents of the video
        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $subject_type = $subject->getType();
            $subject_id = $subject->getIdentity();

            $video->parent_type = $subject_type;
            $video->parent_id = $subject_id;
        }
        $video->search = 1;
        $video->save();

        if (!($video instanceof Core_Model_Item_Abstract) || !$video->getIdentity()) {
            return;
        }

        return $video;
    }

}