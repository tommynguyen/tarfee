<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class YnVideo_Model_DbTable_Categories extends Engine_Db_Table {
    protected $_rowClass = 'Ynvideo_Model_Category';
    protected $_name = 'video_categories';

    public function getAllCategoriesAndSortByLevel($where = null, $order = null, $count = null, $offset = null) {
        $categories = $this->fetchAll(array('category_id != 0'), $order, $count, $offset);
        $cats = array();
        foreach($categories as $cat) {
            $cats[$cat->getIdentity()] = $cat;
        }
        foreach($categories as $cat) {
            if ($cat->parent_id) {
                $cats[$cat->parent_id]->addSubCategory($cat);
            }
        }
        return $cats;
    }
    
    public function getCategories($arrCategoryIds) {
        $select = $this->select();
        $select->where('category_id in (?)', $arrCategoryIds);
        $categories = $this->fetchAll($select);
        $arrCat = array();
        foreach($categories as $category) {
            $arrCat[$category->getIdentity()] = $category;
        }
        return $arrCat;
    }
}