<?php

class Ynfeedback_Model_Category extends Ynfeedback_Model_Node {
	
	protected $_searchTriggers = false;
	protected $_parent_type = 'user';
	protected $_owner_type = 'user';
	protected $_type = 'ynfeedback_category';
    
	public function getParentCategoryLevel1()
	{
		$i = 1;
		$loop_item = $this;
		while($i < 4)
		{
			$item = $loop_item -> getParent($loop_item -> getIdentity());
			if(count($item->themes) > 0)
			{
				return $item;
			}
			$loop_item = $item;
			$i++;
		}
	}
	
	public function getParent($category_id = null)
	{
		$item = Engine_Api::_()->getItem('ynfeedback_category', $category_id);
		$parent_item = Engine_Api::_()->getItem('ynfeedback_category', $item->parent_id);
		return $parent_item;
	}
	
	public function getHref($params = array()) {
	    $params = array_merge(array(
            'route' => 'ynfeedback_general',
            'controller' => 'index',
            'action' => 'listing',
            'category_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
	}
    
	public function getTable() {
		if(is_null($this -> _table)) {
			$this -> _table = Engine_Api::_() -> getDbtable('categories', 'ynfeedback');
		}
		return $this -> _table;
	}
	
	public function checkHasIdea()
	{
		$table = Engine_Api::_() -> getItemTable('ynfeedback_idea');
		$select = $table -> select() -> where('category_id = ?', $this->getIdentity()) -> where('deleted = 0') -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
			return true;
		else {
			return false;
		}
	}
	
	public function getMoveCategoriesByLevel($level)
	{
		$table = Engine_Api::_() -> getDbtable('categories', 'ynfeedback');
		$select = $table -> select() 
				-> where('category_id <>  ?', 1) // not default
				-> where('category_id <>  ?', $this->getIdentity())// not itseft
				-> where('level = ?', $level);
		$result = $table -> fetchAll($select);
		return $result;
	}
	
	public function getTitle()
	{
		$view = Zend_Registry::get('Zend_View');
		return $view -> translate($this -> title);
	}
	
	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}
	
    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('ynfeedback_category');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }
    
    public function getCustomFieldsList() {
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('ynfeedback_idea');
        $option_id = $this->option_id;
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if( !empty($option_id) ) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if( !empty($secondLevelMaps) ) {
                foreach( $secondLevelMaps as $map ) {
                    $secondLevelFields[$map->child_id] = $map->getChild();
                }
            }
        }
        return $secondLevelFields;
    }
    
    public function getFeedbackCount()
    {
    	$feedbackTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
    	$select = $feedbackTbl -> getIdeasSelect(array('category_id' => $this -> category_id));
    	$records = $feedbackTbl -> fetchAll($select);
    	return count($records);
    }
    
}
