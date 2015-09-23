<?php
class Ynfeedback_Widget_MiddleCategoriesController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $items_per_page = $this -> _getParam('itemCountPerPage', 9);
    	$this -> view -> categories = $categories = Engine_Api::_() -> getItemTable('ynfeedback_category') -> getTopParentCategories($items_per_page);
    	if (count($categories) == 0)
    	{
    		return $this -> setNoRender();
    	}
    }
}
