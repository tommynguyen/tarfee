<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Category extends Core_Model_Item_Abstract {
    protected $_type = 'video_category';
    private $_arrSubCategories;
    private $_arrVideos;

    // Properties
    // General

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('categories', 'ynvideo');
        }

        return $this->_table;
    }

    public function getUsedCount() {
        $table = Engine_Api::_()->getDbTable('videos', 'ynvideo');
        $rName = $table->info('name');
        $select = $table->select()
                ->from($rName)
                ->where($rName . '.category_id = ?', $this->category_id)
                ->orWhere($rName . '.subcategory_id = ?', $this->category_id);
        $row = $table->fetchAll($select);
        $total = count($row);
        return $total;
    }

    /**
     * add a sub category for a category
     * @param type $category
     */
    public function addSubCategory($category) {
        if (!isset($this->_arrSubCategories)) {
            $this->_arrSubCategories = array();
        }
        array_push($this->_arrSubCategories, $category);
    }

    public function fetchSubCategories() {
        $categoryTbl = Engine_Api::_()->getDbTable('categories', 'ynvideo');
        $select = $categoryTbl->select()->where('parent_id = ?', $this->getIdentity());
        return $categoryTbl->fetchAll($select);
    }
    
    public function getSubCategories() {
        if (!isset($this->_arrSubCategories)) {
            $this->_arrSubCategories = array();
        }
        return $this->_arrSubCategories;
    }

    public static function defaultIconUrl() {
        return 'application/modules/Ynvideo/externals/images/default_category.png';
    }
    
    public function getIconUrl() {
        if ($this->photo_url) {
            $url = $this->photo_url;
        } else {
            $url = $this->defaultIconUrl();
        }
        return $url;
    }

    public function addVideo($video) {
        if (!$this->_arrVideos) {
            $this->_arrVideos = array();
        }
        $this->_arrVideos[$video->getIdentity()] = $video;
    }

    public function getVideos() {
        if (!$this->_arrVideos) {
            $this->_arrVideos = array();
        }
        return $this->_arrVideos;
    }

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'video_general',
            'reset' => true,
            'action' => 'list',
            'category' => $this->getIdentity()
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, $reset);
    }
    
    protected function _postDelete() {
        parent::_postDelete();
        if (file_exists($this->photo_url)) {
            @unlink($this->photo_url);
        }
    }
    
    protected function _postUpdate() {
        parent::_postUpdate();
        if (array_key_exists('photo_url', $this->_modifiedFields)) {
            $previousPhotoUrl = $this->_cleanData['photo_url'];
            if (!empty($previousPhotoUrl) && file_exists($previousPhotoUrl)) {
                @unlink($previousPhotoUrl);
            }
        }
    }
    
    protected function _delete() {
        parent::_delete();
        
        $categories = $this->fetchSubCategories();
        foreach($categories as $category) {
            $category->delete();
        }
    }
}