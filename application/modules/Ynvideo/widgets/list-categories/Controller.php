<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListCategoriesController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') . 
                'application/modules/Ynvideo/externals/scripts/collapsible.js');
        
        $categoryTable = Engine_Api::_()->getDbTable('categories', 'ynvideo');
        $this->view->categories = $categoryTable->
                getAllCategoriesAndSortByLevel(null, array('category_name'));
    }

}