<?php
class Ynfeedback_AdminCategoryController extends Core_Controller_Action_Admin {
	protected $_paginate_params = array();
	public function init() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_main_categories');
	}

	public function getDbTable() {
		return Engine_Api::_() -> getDbTable('categories', 'ynfeedback');
	}

	public function indexAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> categories = $node -> getChilren();
		$this -> view -> category = $node;
	}

	public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$parentId = $this -> _getParam('parent_id', 0);
		$form = $this -> view -> form = new Ynfeedback_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		$table = $this -> getDbTable();
		$node = $table -> getNode($parentId);
		//maximum 3 level category
		if ($node -> level > 2) {
			throw new Zend_Exception('Maximum 3 levels of category.');
		}
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

	public function deleteCategoryAction() {

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> category_id = $id;
		$table = $this -> getDbTable();
		$node = $table -> getNode($id);
		$categories = array();
		$table -> appendChildToTree($node, $categories);
		$level = $node -> level;
		unset($categories[0]);
		
		$hasIdea = $node -> checkHasIdea();
		$optionData = Engine_Api::_() -> fields() -> getFieldsOptions('ynfeedback_idea');
		$tableIdea = Engine_Api::_() -> getItemTable('ynfeedback_idea');
		if ($hasIdea || (count($categories) > 0)) 
		{
			$this -> view -> moveCates = $moveCates = $node -> getMoveCategoriesByLevel($node -> level);
		}
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$move_category_id = $this -> _getParam('move_category');
			$node_id = $this -> getRequest() -> getPost('node_id', 0);
			// go through logs and see which classified used this category and set it to ZERO
			if (is_object($node)) {
				if ($hasIdea || (count($categories) > 0)) {
					if ($move_category_id != 'none') {
						//move businiesses to another category
						if ($hasIdea) 
						{
							//get feedbacks of deleted category
							$ideas = $tableIdea -> getIdeasByCategory($node -> category_id);
							//foreach bussiness in map table
							foreach ($ideas as $idea) 
							{
								$idea -> category_id = $move_category_id;
								$idea -> save();
								
								//TODO clear profile type
								// Remove old data custom fields 
								$old_category = Engine_Api::_()->getItem('ynfeedback_category', $node->category_id);
								$tableMaps = Engine_Api::_() -> getDbTable('maps','ynfeedback');
								$tableValues = Engine_Api::_() -> getDbTable('values','ynfeedback');
								$tableSearch = Engine_Api::_() -> getDbTable('search','ynfeedback');
								$fieldIds = $tableMaps->fetchAll($tableMaps -> select()-> where('option_id = ?',  $old_category->option_id));
								$arr_ids = array();
								if(count($fieldIds) > 0)
								{
									//clear values in search table
									$searchItem  = $tableSearch->fetchRow($tableSearch -> select() -> where('item_id = ?', $idea -> getIdentity()) -> limit(1));
									foreach($fieldIds as $id)
									{
										try{
											$column_name = 'field_'.$id -> child_id;
											if($searchItem)
											{
												$searchItem -> $column_name = NULL;
											}
											$arr_ids[] = $id -> child_id;
										}
										catch(exception $e)
										{
											continue;
										}
									}
									if($searchItem)
										$searchItem -> save();
									//delele in values table
									if(count($arr_ids) > 0)
									{
										$valueItems = $tableValues->fetchAll($tableValues -> select() -> where('item_id = ?', $idea -> getIdentity()) -> where('field_id IN (?)', $arr_ids));
										foreach($valueItems as $item)
										{
											$item -> delete();
										}
									}
								}
							}
						}
						//delete its type + node
						$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
						//move sub category
						$move_node = $table -> getNode($move_category_id);
						foreach ($categories as $item) 
						{
							
							$arr_item = $item -> toArray();
							unset($arr_item['category_id']);
							unset($arr_item['parent_id']);
							unset($arr_item['pleft']);
							unset($arr_item['pright']);
							
							$update_category_id = $item -> category_id;
							$update_option_id = $item -> option_id;
							
							if($item -> level - $move_node -> level == 1)
							{
								$newNode = $table -> addChild($move_node, $arr_item);
								//delete profile type of new node
								$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								$newNode -> option_id = $update_option_id;
								//udpate feedbacks with new category_id
								$list_ideas = $tableIdea -> getIdeasByCategory($update_category_id);
								foreach($list_ideas as $item_ideas)
								{
									$item_ideas -> category_id = $newNode -> category_id;
									$item_ideas -> save();
								}
								$newNode -> save();
								$move_node = $newNode;
								
							}
							else
							{
								while($item -> level - $move_node -> level < 1)
								{
									$move_node = $table -> getNode($move_node -> parent_id);
								}
								$newNode = $table -> addChild($move_node, $arr_item);
								//delete profile type of new node
								$this -> typeDelete($newNode -> option_id);
								//update new node with old option_id
								$newNode -> option_id = $update_option_id;
								//udpate feedbacks with new category_id
								$list_ideas = $tableIdea -> getIdeasByCategory($update_category_id);
								foreach($list_ideas as $item_ideas)
								{
									$item_ideas -> category_id = $newNode -> category_id;
									$item_ideas -> save();
								}
								$newNode -> save();
								$move_node = $newNode;
							}
						}
					} 
					else //delete all
					{
						//delete type + custom field of sub categories
						foreach ($categories as $item) 
						{
							$option = $optionData -> getRowMatching('label', $item -> title);
							if ($option) {
								$this -> typeDelete($option -> option_id);
							}
						}
						//delete its feedbacks
						$ideas = $tableIdea -> getAllChildrenIdeasByCategory($node);
						if (count($ideas) > 0) {
							foreach ($ideas as $item) {
								foreach ($item->toArray() as $idea) {
									$delete_item = Engine_Api::_() -> getItem('ynfeedback_idea', $idea['idea_id']);
									if($delete_item)
										$delete_item -> delete();
								}
							}
						}
						//delete its type + node
						$this -> typeDelete($node -> option_id);
						$table -> deleteNode($node);
					}
				}
				//delete all if category has no sub or no ideas
				else
				{
					//delete type + custom field of sub categories
					foreach ($categories as $item) 
					{
						$option = $optionData -> getRowMatching('label', $item -> title);
						if ($option) {
							$this -> typeDelete($option -> option_id);
						}
					}
					//delete its feedbacks
					$ideas = $tableIdea -> getAllChildrenIdeasByCategory($node);
					if (count($ideas) > 0) {
						foreach ($ideas as $item) {
							foreach ($item->toArray() as $idea) {
								$delete_item = Engine_Api::_() -> getItem('ynfeedback_idea', $idea['idea_id']);
								if($delete_item)
									$delete_item -> delete();
							}
						}
					}
					//delete its type + node
					$this -> typeDelete($node -> option_id);
					$table -> deleteNode($node);
				}
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}
	}

	public function editCategoryAction() {

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}
		// Generate and assign form
		$category = Engine_Api::_() -> getItem('ynfeedback_category', $id);

		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new Ynfeedback_Form_Admin_Category( array('category' => $category));
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));

		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// edit category in the database
				// Transaction
				$row = Engine_Api::_() -> getItem('ynfeedback_category', $values["id"]);
				$row -> title = $values["label"];
				$row -> save();
				$option = Engine_Api::_() -> fields() -> getOption($category -> option_id, 'ynfeedback_idea');
				$option -> label = $values["label"];
				$option -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		$form -> setField($category);

		// Output
		$this -> renderScript('admin-category/form.tpl');
	}

	public function ajaxUseParentCategoryAction() {
		$categoryId = $this -> _getParam('id');
		$check = $this -> _getParam('check');
		$category = Engine_Api::_() -> getItem('ynfeedback_category', $categoryId);
		if ($check) {
			$category -> use_parent_category = 1;
		} else {
			$category -> use_parent_category = 0;
		}
		$category -> save();
	}

	public function typeDelete($option_id) {
		$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynfeedback_idea');
		$field = Engine_Api::_() -> fields() -> getField($option -> field_id, 'ynfeedback_idea');

		// Validate input
		if ($field -> type !== 'profile_type') {
			throw new Exception('invalid input');
		}

		// Do not allow delete if only one type left
		if (count($field -> getOptions()) <= 1) {
			throw new Exception('only one left');
		}

		// Process
		Engine_Api::_() -> fields() -> deleteOption('ynfeedback_idea', $option);

		// @todo reassign stuff
	}

	public function sortAction() {
		$table = $this -> getDbTable();
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$categories = $node -> getChilren();
		$order = explode(',', $this -> getRequest() -> getParam('order'));
		foreach ($order as $i => $item) {
			$category_id = substr($item, strrpos($item, '_') + 1);
			foreach ($categories as $category) {
				if ($category -> category_id == $category_id) {
					$category -> order = $i;
					$category -> save();
				}
			}
		}
	}

}
