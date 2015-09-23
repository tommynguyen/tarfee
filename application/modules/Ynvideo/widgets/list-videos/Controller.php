<?php

/**
 * @category ynvideo
 * @package widget
 * @subpackage search-manage-videos
 * @author dang tran
 */
class Ynvideo_Widget_ListVideosController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $categoryTbl = Engine_Api::_()->getDbTable('categories', 'ynvideo');
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideo');

        $category_id = $request->getParam('category', null);
        $searchParams = $_GET;
        $params = $request->getParams();
        unset($params['controller']);
        unset($params['name']);
        unset($params['action']);
        unset($params['module']);
        if ($category_id == null || $category_id == '') {
            $categoryTblName = $categoryTbl->info('name');
            $videoTblName = $videoTbl->info('name');
            $videoSelect = Engine_Api::_()->ynvideo()->getVideosSelect($params)
                    ->where("$categoryTblName.category_id = $videoTblName.category_id");
            $categorySelect = $categoryTbl->select();
            $categorySelect->where(new Zend_Db_Expr('exists (' . $videoSelect->__toString() . ')'));
            $categorySelect->where('parent_id = 0');           
            
            $this->view->categoryPaginator = $categoryPaginator = Zend_Paginator::factory($categorySelect);
            $categoryPerPage = $settings->getSetting('ynvideo.number.category.per.page', 5);
            $categoryPaginator->setItemCountPerPage($categoryPerPage);
            $page = $request->getParam('page', 1);
            $categoryPaginator->setCurrentPageNumber($page);

            $videoPerCategory = $settings->getSetting('ynvideo.per.category', 4);
            $videosByCategory = array();
            foreach ($categoryPaginator as $category) {
                $videoSelect = Engine_Api::_()->ynvideo()->getVideosSelect($params);
                $videoSelect->limit($videoPerCategory);
                $videoSelect->where('category_id = ?', $category->getIdentity());
                $videos = $videoTbl->fetchAll($videoSelect);
                $videosByCategory[$category->getIdentity()] = $videos;
            }
            $this->view->videosByCategory = $videosByCategory;
        } else {
            $category = Engine_Api::_()->getItem('video_category', $category_id);
            if ($category_id != 0) {
                if (!$category) {
                    return $this->setNoRender();
                }
            }
            $this->view->category = $category;
            $videoSelect = Engine_Api::_()->ynvideo()->getVideosSelect($params);
            $this->view->videoPaginator = $videoPaginator = Zend_Paginator::factory($videoSelect);
            $videoPerPage = $settings->getSetting('ynvideo.page', 20);
            $videoPaginator->setItemCountPerPage($videoPerPage);
            $videoPaginator->setCurrentPageNumber($request->getParam('page', 1));            
        }
        $this->view->params = $params;
    }
}