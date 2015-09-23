<?php
class SocialConnect_AdminSettingsController extends Core_Controller_Action_Admin {

	public function init() {
	}

	public function listingAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialconnect_admin', array(), 'socialconnect_admin_providers');

		$talbe = Engine_Api::_() -> getDbTable('Services', 'SocialConnect');
		$select = $talbe -> select() -> order('service_id');
		$this -> view -> paginator = $paginator = $talbe -> fetchAll($select);
	}

	/**
	 * main settings.
	 * @return unknown_type
	 */

	/**
	 * show list of consumer for edit.
	 * @return unknown_type
	 */

	public function indexAction() {

		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialconnect_admin', array(), 'socialconnect_admin_settings');

		$form = $this -> view -> form = new SocialConnect_Form_General();

		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			$values = $form -> getValues();
			$p_post = $this -> getRequest() -> getPost();
			$values['tffooter_color'] = $p_post['color'];
			foreach ($values as $key => $value) {
				if ($key == 'quick_signup_heading') {
					continue;
				}
				Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
			}
			$form -> addNotice('Your changes have been saved.');
			$form -> tffooter_color -> setValue('<input value="'. $values['tffooter_color'] .'" type="color" id="color" name="color"/>');
		}

	}

	/**
	 * fields settings
	 *
	 */
	public function fieldsAction() {
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('openid_admin', array(), 'openid_admin_fields');
	}

	/**
	 * @return unknown_type
	 */
	public function mapAction() {
		$service = $this -> _request -> getParam('service');
		// In smoothbox
		$form = $this -> view -> form = new SocialConnect_Form_Map();
		$check = SocialConnect_Api_Core::getMapService($service);

		$title = sprintf('%s to Social Engine Profile ', ucfirst($service));

		$desc = 'Set "None" value if you do not need to map %s fields.';
		$desc = sprintf($desc, ucfirst($service));
		if ($check == 'openid') {
			$desc .= ' This configuration affects to all OpenID service providers as myopenid, yiid.com, etc.';
		}
		$form -> setTitle($title);
		$form -> setDescription($desc);
		$form -> populateData($service);
		if ($this -> _request -> isPost() && $form -> isValid($_POST)) {
			$result = $form -> commitSave($service);
			if ($result) {
				return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.')), 'layout' => 'default-simple', 'smoothboxClose' => true, 'parentRefresh' => false));
			}
		}
	}

	public function changeEnableAction() {
		$service_id = $this -> _request -> getParam('service_id');
		$talbe = Engine_Api::_() -> getDbTable('Services', 'SocialConnect') -> switchEnable($service_id);
	}

	public function categoriesAction() 
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialconnect_admin', array(), 'socialconnect_admin_categories');
		$this -> view -> categories = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> getAllCategories();
	}

	public function addCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');

		// Generate and assign form
		$form = $this -> view -> form = new SocialConnect_Form_Admin_Category();
		$form -> setAction($this -> getFrontController() -> getRouter() -> assemble(array()));
		// Check post
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost())) {
			// we will add the category
			$values = $form -> getValues();

			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {
				// add category to the database
				// Transaction
				$table = Engine_Api::_() -> getDbtable('categories', 'socialConnect');
				$user = Engine_Api::_() -> user() -> getViewer();

				// insert the category into the database
				$row = $table -> createRow();
				$row -> user_id = $user -> getIdentity();
				$row -> category_name = $values["label"];
				$row -> save();

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-settings/form.tpl');
	}

	public function deleteCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$id = $this -> _getParam('id');
		$this -> view -> video_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost()) {
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try {

				$row = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> findRow($id);
				// delete the video category into the database
				if ($row) {
					$row -> delete();
				}

				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Output
		$this -> renderScript('admin-settings/delete.tpl');
	}

	public function editCategoryAction() {
		// In smoothbox
		$this -> _helper -> layout -> setLayout('admin-simple');
		$form = $this -> view -> form = new SocialConnect_Form_Admin_Category();
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
				$row = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> findRow($this -> _getParam('id'));
				$row -> category_name = $values["label"];
				$row -> save();
				$db -> commit();
			} catch( Exception $e ) {
				$db -> rollBack();
				throw $e;
			}
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
		}

		// Must have an id
		if (!($id = $this -> _getParam('id'))) {
			die('No identifier specified');
		}

		// Generate and assign form
		$category = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> findRow($id);
		$form -> setField($category);

		// Output
		$this -> renderScript('admin-settings/form.tpl');
	}

	public function pagesAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('socialconnect_admin', array(), 'socialconnect_admin_pages');
        //make paginator for contain list of books
        $page = $this->_getParam('page',1);
		$category_id = $this -> _getParam('id', 0);
        $table = Engine_Api::_()->getDbTable('pages', 'socialConnect');
		$select = $table -> select();
		if($category_id)
		{
			$this -> view -> category = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> findRow($category_id);
			$select -> where('category_id = ?', $category_id);
		}
        $pages = $table->fetchAll($select -> order('order'));
        $this->view->paginator = Zend_Paginator::factory($pages);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
	}
	public function sortCategoryAction()
  	{
		$categories = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> getAllCategories();
	    $order = explode(',', $this->getRequest()->getParam('order'));
	    foreach( $order as $i => $item ) {
	      $category_id = substr($item, strrpos($item, '_')+1);
	      foreach( $categories as $category ) {
	        if( $category->category_id == $category_id ) {
	          $category->order = $i;
	          $category->save();
	        }
	    	}
    	}
	}
	public function sortPageAction()
  	{
		$pages = Engine_Api::_() -> getDbTable('pages', 'socialConnect') -> getAllPages();
	    $order = explode(',', $this->getRequest()->getParam('order'));
	    foreach( $order as $i => $item ) {
	      $page_id = substr($item, strrpos($item, '_')+1);
	      foreach( $pages as $page ) {
	        if( $page->page_id == $page_id ) {
	          $page->order = $i;
	          $page->save();
	        }
	    	}
    	}
	}
	 public function deletePageAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->page_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $row = Engine_Api::_() -> getDbTable('pages', 'socialConnect') -> findRow($id);
				if($row)
                	$row->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('')
            ));
        }
    }
    
    public function editPageAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new SocialConnect_Form_Admin_EditPage();
		
        $row = Engine_Api::_() -> getDbTable('pages', 'socialConnect') -> findRow($id);
        $form->populate($row->toArray());
        // Output
		$this -> renderScript('admin-settings/form.tpl');     
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if(!$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $db = Engine_Api::_()->getDbtable('pages', 'socialConnect')->getAdapter();
        $db->beginTransaction();
        try {
            $row->setFromArray($form->getValues());
            $row->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
        $this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
    
    public function addPageAction() {
        $this->view->form = $form = new SocialConnect_Form_Admin_CreatePage();
		// Output
		$this -> renderScript('admin-settings/form.tpl');
		
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        $success = FALSE;
        
        $values = array_merge($form->getValues(), array(
                'created_date' => date('Y-m-d H:i:s')
            ));
        
        $db = Engine_Api::_() -> getDbTable('pages', 'socialConnect') ->getAdapter();
        $db->beginTransaction();
        try {
            $table = Engine_Api::_()->getDbtable('pages', 'socialConnect');
            $page = $table->createRow();
            $page->setFromArray($values);
            $page->save();
            $success = TRUE;
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       

        $db->commit();
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => 10, 'parentRefresh' => 10, 'messages' => array('')));
    }
}
