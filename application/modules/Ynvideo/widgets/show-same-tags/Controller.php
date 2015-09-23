<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ShowSameTagsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');

        // Set default title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('Similar Videos');
        }

        // Get tags for this video
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());
        $tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');

        // Get tags
        $tags = $tagMapsTable->select()
                ->from($tagMapsTable, 'tag_id')
                ->where('resource_type = ?', $subject->getType())
                ->where('resource_id = ?', $subject->getIdentity())
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        // Get other with same tags
        $select = $itemTable->select()
                ->distinct(true)
                ->from($itemTable)
                ->joinLeft($tagMapsTable->info('name'), 'resource_id=video_id', null)
                ->where('resource_type = ?', $subject->getType())
                ->where('resource_id != ?', $subject->getIdentity())
                ->where('tag_id IN(?)', $tags)
                ->where('search = ?', true) // ?
        ;

        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($select);

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    }

}