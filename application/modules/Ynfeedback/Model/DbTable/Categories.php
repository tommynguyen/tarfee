<?php
class Ynfeedback_Model_DbTable_Categories extends Ynfeedback_Model_DbTable_Nodes {
	protected $_rowClass = 'Ynfeedback_Model_Category';
	
	public function getCategoryByOptionId($option_id)
	{
		$select = $this->select();
		$select -> where('option_id = ?', $option_id);
		$select -> limit(1);
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function getFirstCategory()
	{
		$select = $this->select();
		$select -> order('category_id ASC');
		$select -> limit(2);
		$select -> where('category_id <> 1');
		$item  = $this->fetchRow($select);
		return $item;
	}
	
	public function deleteNode(Ynfeedback_Model_Node $node, $node_id = NULL) {
		parent::deleteNode($node);
	}

    public function getCategories() {
        $table = Engine_Api::_() -> getDbTable('categories', 'ynfeedback');
        $tree = array();
        $node = $table -> getNode(1);
        $this->appendChildToTree($node, $tree);
        return $tree;
    }
    
    public function appendChildToTree($node, &$tree) {
        array_push($tree, $node);
        $children = $node->getChilren();
        foreach ($children as $child_node) {
            $this->appendChildToTree($child_node, $tree);
        }
    }
	
	public function getAllCategories()
	{
		$select = $this -> select() -> order('title') -> where('category_id <> 1');
		return $this -> fetchAll($select);
	}
	
	public function getCategoriesAssoc()
	{
		$categories = $this -> getCategories();
		unset($categories[0]);
		$arr = array(
			'0' => Zend_Registry::get("Zend_Translate")->_('All')
		);
		if(count($categories)) 
		{
			foreach ($categories as $item)
			{
				$arr[$item['category_id']] = str_repeat("-- ", $item['level'] - 1) . Zend_Registry::get("Zend_Translate")->_($item['title']);
			}
		}
		return $arr;
	}
	
	public function getTopParentCategories($limit = 0)
	{
		$select = $this -> select() -> where('level = 1') -> order("title");
        if($limit)
        {
            $select -> limit($limit);
        }
		return $this -> fetchAll($select);
	}
}
