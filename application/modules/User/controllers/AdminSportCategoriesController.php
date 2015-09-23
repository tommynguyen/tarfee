<?php
class User_AdminSportCategoriesController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('player_admin_main', array(), 'player_admin_main_sportcategory');
  }
  public function indexAction()
  {
  		$table = Engine_Api::_() -> getDbtable('sportcategories', 'user');
		$node = $table -> getNode($this -> _getParam('parent_id', 0));
		$this -> view -> categories = $node -> getChilren();
		$this -> view -> category = $node;
  }
  public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		
		
		// Generate and assign form
		$form = $this -> view -> form = new User_Form_Admin_Player_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));
		
		$parentId = $this -> _getParam('parent_id', 0);
		if ($parentId != '1') {
			$form -> removeElement('photo');
		}
		
		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();
			$table = Engine_Api::_() -> getDbtable('sportcategories', 'user');
			$node = $table -> getNode($parentId);
			$user = Engine_Api::_() -> user() -> getViewer();
			$data = array('user_id' => $user -> getIdentity(), 'title' => $values["label"]);
			if (!empty($values['photo'])) {
				$data['photo'] = $form -> photo;
			}
			$table -> addChild($node, $data);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-sport-categories/form.tpl');
	}

	public function deleteCategoryAction() {
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');     
		$this -> view -> category_id = $id;
		$table = Engine_Api::_() -> getDbtable('sportcategories', 'user');
		$node = $table -> find($id) -> current();
		
		// Check post
		if($this -> getRequest() -> isPost()) {
			$node_id=  $this->getRequest()->getPost('node_id',0);
			// go through logs and see which classified used this category and set it to ZERO			
			if(is_object($node)) {
				$table -> deleteNode($node, $node_id);
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-sport-categories/delete.tpl');
	}

	public function editCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new User_Form_Admin_Player_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble( array()));

		// Check post
		if($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// Ok, we're good to add field
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// Transaction
				$row = Engine_Api::_() -> getItem('user_sportcategory', $values["id"]);
				$row -> title = $values["label"];
				if (!empty($values['photo'])) {
					$row -> setPhoto($form -> photo);
				}
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Must have an id
		if(!($id = $this -> _getParam('id'))) {
			throw new Zend_Exception('No identifier specified');
		}

		// Generate and assign form
		$category = Engine_Api::_() -> getItem('user_sportcategory', $id);
		$form -> setField($category);

		// Output
		$this -> renderScript('admin-sport-categories/form.tpl');
	}
  
}