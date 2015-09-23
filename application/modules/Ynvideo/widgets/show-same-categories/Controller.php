<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ShowSameCategoriesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Check subject
        if (!Engine_Api::_()->core()->hasSubject('video')) {
            return $this->setNoRender();
        }
        
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('video');
		$owner_id = $subject->owner_id;
		$parent_type = $subject->parent_type;
		$category_id = $subject->category_id;
		$resource_type = $subject->getType();
		$resource_id = $subject->getIdentity();

        // Set default title
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle('Related Videos');
        }

        $numberOfVideos = $this->_getParam('numberOfVideos', 5);
        // Get tags for this video
        $itemTable = Engine_Api::_()->getItemTable($subject->getType());
        $tagMapsTable = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $tagsTable = Engine_Api::_()->getDbtable('tags', 'core');

        // Get other with same tags
        $select = $itemTable->select()
				->distinct(true)
                ->from($itemTable)
				->joinLeft($tagMapsTable->info('name'), 'resource_id = video_id', null)
				->where('video_id <> ?', $subject->getIdentity())
				->where('search = ?', 1)
                ->limit($numberOfVideos)
                ->order(new Zend_Db_Expr(('rand()')));
				
		// Get tags
        $tags = $tagMapsTable->select()
                ->from($tagMapsTable, 'tag_id')
                ->where('resource_type = ?', $subject->getType())
                ->where('resource_id = ?', $subject->getIdentity())
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);
		if(!$tags)
		{
			$tags = array('');
		}
        if ($parent_type == 'user_playercard') 
        {
			$parent_id = $subject->parent_id;
			$select->where("owner_id = $owner_id OR (parent_type = 'user_playercard' AND parent_id = $parent_id ) OR category_id = {$category_id} OR (resource_type = '{$resource_type}' AND resource_id = {$resource_id} AND tag_id IN(?))", $tags);
		}
		else 
		{
			$select->where("owner_id = {$owner_id} OR category_id = {$category_id} OR (resource_type = '{$resource_type}' AND resource_id = {$resource_id} AND tag_id IN(?))", $tags);
		}
        $this->view->videos = $itemTable->fetchAll($select);
    }
}