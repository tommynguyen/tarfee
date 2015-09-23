<?php
class Ynblog_IndexController extends Core_Controller_Action_Standard {
	/* ------ Init Contidition & Needed Resource Function ----- */
	public function init() {
		// Only show to user if view permisssion authorized
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'blog', null, 'view' )->isValid ())
			return;
	}

	/* ------ Blog Home Page Function ----- */
	public function indexAction() {
		// Landing page mode
		$this->_helper->content->setNoRender ()->setEnabled ();
	}

	/* ----- General Blog Listing Function ----- */
	public function listingAction() {
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$params = $this -> _getAllParams();
		$ids = array ();
		// Do the show thingy
		if (isset($params ['by_authors'] ) && in_array('networks', $params['by_authors'])) 
		{
			// Get an array of user ids
			
			$network_table = Engine_Api::_()->getDbtable('membership', 'network');
      		$network_select = $network_table->select()->where('user_id = ?', $viewer -> getIdentity());
      		$networks = $network_table->fetchAll($network_select);
			foreach($networks as $network)
			{
				$network_select = $network_table->select()->where('resource_id = ?', $network -> resource_id) -> where("active = 1");
      			$users = $network_table->fetchAll($network_select);
				foreach ( $users as $user ) {
					$ids [] = $user->user_id;
				}
			}
			
		}
		if (isset($params ['by_authors'] ) && in_array('professional', $params['by_authors'])) 
		{
			$userIds = Engine_Api::_() -> user() -> getProfessionalUsers();
			foreach ($userIds as $id) {
				$ids [] = $id;
			}
		}
		
		$params ['users'] = $ids;
		
		// Get blog paginator
		$paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $params );
		$items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.page', 10 );
		$paginator->setItemCountPerPage ( $items_per_page );

		if (isset ( $params ['page'] )) {
			$paginator->setCurrentPageNumber ( $params ['page'] );
		}
		$this->view->paginator = $paginator;
		// Render
		$this->_helper->content->setEnabled ();
	}

	/* ------ A User Blogs List Function ----- */
	public function listAction() {
		// Preload info
		$this->view->viewer = $viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->view->owner = $owner = Engine_Api::_ ()->getItem ( 'user', $this->_getParam ( 'user_id' ) );
		Engine_Api::_ ()->core ()->setSubject ( $owner );

		if (! $this->_helper->requireSubject ()->isValid ()) {
			return;
		}

		// Search Params
		$form = new Ynblog_Form_Search ();
		$form->isValid ( $this->_getAllParams () );
		$params = $form->getValues ();
		$params ['date'] = $this->_getParam ( 'date' );
		$this->view->formValues = $params;

		$params ['user_id'] = $owner->getIdentity ();
		$params ['draft'] = 0;
		$params ['is_approved'] = 1;
		$params ['visible'] = 1;

		// Get paginator
		$this->view->paginator = $paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $params );
		$items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.page', 10 );
		$paginator->setItemCountPerPage ( $items_per_page );
		if (isset ( $params ['page'] )) {
			$this->view->paginator = $paginator->setCurrentPageNumber ( $params ['page'] );
		}
		// Render
		$this->_helper->content->setEnabled ();
	}

	/* ----- Blog Creation Function ----- */
	public function createAction() {
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'blog', null, 'create' )->isValid ())
			return;

		// Render
		$this->_helper->content->setEnabled ();

		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		// Checking maximum blog allowed
		$this->view->maximum_blogs = $maximum_blogs = Engine_Api::_ ()->getItemTable ( 'blog' )->checkMaxBlogs ();
		$blog_number = Engine_Api::_ ()->getItemTable ( 'blog' )->getCountBlog ( $viewer );
		if ($maximum_blogs == 0 || $blog_number < $maximum_blogs) {
			$this->view->maximum_reach = false;
		} else {
			$this->view->maximum_reach = true;
		}

		// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_main' );

		// Prepare form
		$this->view->form = $form = new Ynblog_Form_Create ();

		// Post request checking
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Process
		$table = Engine_Api::_ ()->getDbTable ( 'blogs', 'ynblog' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();
		try {
			// Create blog
			$values = array_merge ( $form->getValues (), array (
					'owner_type' => $viewer->getType (),
					'owner_id' => $viewer->getIdentity ()
			) );

			// Moderation mode
			$blog_moderation = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 );
			if ($blog_moderation) {
				$values ['is_approved'] = 0;
			} else {
				$values ['is_approved'] = 1;
			}
			$blog = $table->createRow ();
			$blog->setFromArray ( $values );

			$blog->save ();
			
			 // Set photo
		      if( !empty($values['photo']) ) {
		        $blog->setPhoto($form->photo);
		      }

			// Authorization set up
			$auth = Engine_Api::_ ()->authorization ()->context;
			$roles = array (
					'owner',
					'owner_member',
					'owner_member_member',
					'owner_network',
					'everyone'
			);

			if (empty ( $values ['auth_view'] )) {
				$values ['auth_view'] = 'everyone';
			}

			if (empty ( $values ['auth_comment'] )) {
				$values ['auth_comment'] = 'everyone';
			}

			$viewMax = array_search ( $values ['auth_view'], $roles );
			$commentMax = array_search ( $values ['auth_comment'], $roles );

			foreach ( $roles as $i => $role ) {
				$auth->setAllowed ( $blog, $role, 'view', ($i <= $viewMax) );
				$auth->setAllowed ( $blog, $role, 'comment', ($i <= $commentMax) );
			}

			// Add tags
			$tags = preg_split ( '/[,]+/', $values ['tags'] );
			$blog->tags ()->addTagMaps ( $viewer, $tags );

			// Add activity only if blog is published
			if ($values ['draft'] == 0 && $values ['is_approved'] == 1) {
				$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $viewer, $blog, 'ynblog_new' );

				// Make sure action exists before attaching the blog to the
				// activity
				if ($action) {
					Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $blog );
				}

				// Send notifications for subscribers
				Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $blog );

				$blog->add_activity = 1;
				$blog->save ();
			}
			// Commit
			$db->commit ();
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		return $this->_helper->redirector->gotoRoute ( array (
				'action' => 'manage'
		) );
	}

	/* ----- User Blogs Manage Page Function ----- */
	public function manageAction() {
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ())
			return;

		// Render
		$this->_helper->content->setEnabled ();

			// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_main' );

		// Get quick navigation
		$this->view->quickNavigation = $quickNavigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_quick' );

		// Prepare data
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$this->view->form = $form = new Ynblog_Form_Search ();
		$this->view->canCreate = $this->_helper->requireAuth ()->setAuthParams ( 'blog', null, 'create' )->checkRequire ();
		$form->removeElement ( 'show' );

		// Populate form
		$categories = Engine_Api::_ ()->getDbtable ( 'categories', 'ynblog' )->getCategoriesAssoc ();
		if (! empty ( $categories ) && is_array ( $categories ) && $form->getElement ( 'category' )) {
			$form->getElement ( 'category' )->addMultiOptions ( $categories );
		}

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );
		$values ['user_id'] = $viewer->getIdentity ();
		$mode = $values ['mode'];

		if (isset ( $mode )) {
			if ($mode == '0') {
				$values ['draft'] = 1;
			} else if ($mode == '1') {
				$values ['draft'] = 0;
				$values ['is_approved'] = 0;
			} else if ($mode == '2') {
				$values ['draft'] = 0;
				$values ['is_approved'] = 1;
			}
		}
		// Get blog paginator
		$this->view->paginator = $paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $values );
		$items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.page', 10 );
		$paginator->setItemCountPerPage ( $items_per_page );
		$this->view->paginator = $paginator->setCurrentPageNumber ( $values ['page'] );
	}

	/* ----- Blog Edit Function ----- */
	public function editAction() {
		// User checking
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		// Get chosen blog to edit
		$blog = Engine_Api::_ ()->getItem ( 'blog', $this->_getParam ( 'blog_id' ) );
		if (! Engine_Api::_ ()->core ()->hasSubject ( 'blog' )) {
			Engine_Api::_ ()->core ()->setSubject ( $blog );
		}
		if (! $this->_helper->requireSubject ()->isValid ())
			return;
		if (! $this->_helper->requireAuth ()->setAuthParams ( $blog, $viewer, 'edit' )->isValid ())
			return;

			// Get navigation
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_main' );

		// Prepare form
		$this->view->form = $form = new Ynblog_Form_Edit ();

		// Populate form
		$form->populate ( $blog->toArray () );

		$tagStr = '';
		foreach ( $blog->tags ()->getTagMaps () as $tagMap ) {
			$tag = $tagMap->getTag ();
			if (! isset ( $tag->text ))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag->text;
		}
		$form->populate ( array (
				'tags' => $tagStr
		) );
		$this->view->tagNamePrepared = $tagStr;

		$auth = Engine_Api::_ ()->authorization ()->context;
		$roles = array (
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
		);

		foreach ( $roles as $role ) {
			if ($form->auth_view) {
				if ($auth->isAllowed ( $blog, $role, 'view' )) {
					$form->auth_view->setValue ( $role );
				}
			}

			if ($form->auth_comment) {
				if ($auth->isAllowed ( $blog, $role, 'comment' )) {
					$form->auth_comment->setValue ( $role );
				}
			}
		}

		// hide status change if it has been already published
		if ($blog->draft == "0") {
			$form->removeElement ( 'draft' );
		}

		// Check post/form
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Process
		$db = Engine_Db_Table::getDefaultAdapter ();
		$db->beginTransaction ();

		try {
			$values = $form->getValues ();

			$blog->setFromArray ( $values );
			$blog->modified_date = date ( 'Y-m-d H:i:s' );
			$blog->save ();
			
			// Set photo
		      if( !empty($values['photo']) ) {
		        $blog->setPhoto($form->photo);
		      }

			// Authorization
			if (empty ( $values ['auth_view'] )) {
				$values ['auth_view'] = 'everyone';
			}

			if (empty ( $values ['auth_comment'] )) {
				$values ['auth_comment'] = 'everyone';
			}

			$viewMax = array_search ( $values ['auth_view'], $roles );
			$commentMax = array_search ( $values ['auth_comment'], $roles );

			foreach ( $roles as $i => $role ) {
				$auth->setAllowed ( $blog, $role, 'view', ($i <= $viewMax) );
				$auth->setAllowed ( $blog, $role, 'comment', ($i <= $commentMax) );
			}

			// Handle tags
			$tags = preg_split ( '/[,]+/', $values ['tags'] );
			$blog->tags ()->setTagMaps ( $viewer, $tags );

			// Insert new activity if blog is just getting published and
			// approved
			if (! $blog->add_activity && ! $blog->draft && $blog->is_approved) {
				$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $viewer, $blog, 'ynblog_new' );
				// make sure action exists before attaching the blog to the
				// activity
				if ($action) {
					Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $blog );
				}

				$blog->add_activity = 1;
				$blog->save ();
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' );
			foreach ( $actionTable->getActionsByObject ( $blog ) as $action ) {
				$actionTable->resetActivityBindings ( $action );
			}

			// Send notifications for subscribers
			Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $blog );

			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		if($this -> _getParam('browse', false))
		{
			return $this->_helper->redirector->gotoRoute (array(),'blog_general', true);
		}
		else {
			return $this->_helper->redirector->gotoRoute (array(
				'action' => 'manage'
			) );
		}
	}

	/* ----- Blog Delete Action ----- */
	public function deleteAction() {
		// User checking
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();

		$blog = Engine_Api::_ ()->getItem ( 'blog', $this->getRequest ()->getParam ( 'blog_id' ) );
		if (! $this->_helper->requireAuth ()->setAuthParams ( $blog, null, 'delete' )->isValid ())
			return;

			// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );

		$this->view->form = $form = new Ynblog_Form_Delete ();

		if (! $blog) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get ( 'Zend_Translate' )->_ ( "Blog entry doesn't exist or not authorized to delete." );
			return;
		}

		if (! $this->getRequest ()->isPost ()) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Invalid request method.' );
			return;
		}

		$db = $blog->getTable ()->getAdapter ();
		$db->beginTransaction ();

		try {
			$blog->delete ();

			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$this->view->status = true;
		$this->view->message = Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Your blog entry has been deleted.' );
		return $this->_forward ( 'success', 'utility', 'core', array (
				'parentRedirect' => Zend_Controller_Front::getInstance ()->getRouter ()->assemble ( array (
						'action' => 'manage'
				), 'blog_general', true ),
				'messages' => Array (
						$this->view->message
				)
		) );
	}
	public function styleAction() {
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'blog', null, 'style' )->isValid ())
			return;

			// In smoothbox
		$this->_helper->layout->setLayout ( 'default-simple' );

		// Require user
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$user = Engine_Api::_ ()->user ()->getViewer ();

		// Make form
		$this->view->form = $form = new Ynblog_Form_Style ();

		// Get current row
		$table = Engine_Api::_ ()->getDbtable ( 'styles', 'core' );
		$select = $table->select ()->where ( 'type = ?', 'user_blog' )->		// @todo this is not a real type
		where ( 'id = ?', $user->getIdentity () )->limit ( 1 );

		$row = $table->fetchRow ( $select );

		// Check post
		if (! $this->getRequest ()->isPost ()) {
			$form->populate ( array (
					'style' => (null === $row ? '' : $row->style)
			) );
			return;
		}

		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Cool! Process
		$style = $form->getValue ( 'style' );

		// Save
		if (null == $row) {
			$row = $table->createRow ();
			$row->type = 'user_blog'; // @todo this is not a real type
			$row->id = $user->getIdentity ();
		}

		$row->style = $style;
		$row->save ();

		$this->view->draft = true;
		$this->view->message = Zend_Registry::get ( 'Zend_Translate' )->_ ( "Your changes have been saved." );
		$this->_forward ( 'success', 'utility', 'core', array (
				'smoothboxClose' => true,
				'parentRefresh' => false,
				'messages' => array (
						$this->view->message
				)
		) );
	}
	public function viewAction() {
		// Check permission
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$blog = Engine_Api::_ ()->getItem ( 'blog', $this->_getParam ( 'blog_id' ) );
		if ($blog) {
			Engine_Api::_ ()->core ()->setSubject ( $blog );
		}

		if (! $this->_helper->requireSubject ()->isValid ()) {
			return;
		}
		
		if (Engine_Api::_()->user()->itemOfDeactiveUsers($blog)) {
			return $this->_helper->requireSubject()->forward();
		}
		
		if (! $this->_helper->requireAuth ()->setAuthParams ( $blog, $viewer, 'view' )->isValid ()) {
			return;
		}
		if (! $blog || ! $blog->getIdentity () || ($blog->draft && ! $blog->isOwner ( $viewer )) || (! $blog->is_approved && ! $blog->isOwner ( $viewer ) && ! $viewer->isAdmin ())) {
			return $this->_helper->requireSubject->forward ();
		}

		// Prepare data
		$blogTable = Engine_Api::_ ()->getItemTable ( 'blog' );

		$this->view->blog = $blog;
		$this->view->owner = $owner = $blog->getOwner ();
		$this->view->viewer = $viewer;

		if (! $blog->isOwner ( $viewer )) {
			$blogTable->update ( array (
					'view_count' => new Zend_Db_Expr ( 'view_count + 1' )
			), array (
					'blog_id = ?' => $blog->getIdentity ()
			) );
		}

		// Get tags
		$this->view->blogTags = $blog->tags ()->getTagMaps ();

		// Get category
		if (! empty ( $blog->category_id )) {
			$this->view->category = Engine_Api::_ ()->getItemTable ( 'blog_category' )->find ( $blog->category_id )->current ();
		}

		// Get styles
		$table = Engine_Api::_ ()->getDbtable ( 'styles', 'core' );
		$style = $table->select ()->from ( $table, 'style' )->where ( 'type = ?', 'user_blog' )->where ( 'id = ?', $owner->getIdentity () )->limit ( 1 );

		$row = $table->fetchRow ( $style );
		if (! empty ( $row )) {
			$this->view->headStyle ()->appendStyle ( $row->style );
		}
        if($blog -> link_detail)
        {
            $view = Zend_Registry::get('Zend_View');
            $view->headLink(array(
              'rel' => 'canonical',
              'href' => $blog -> link_detail),
              'PREPEND');
        }

		// Render
		$this->_helper->content
        //->setNoRender()
        ->setEnabled();
	}
	public function becomeAction() {
		// Disable layout
		$this->_helper->layout->disableLayout ();
		// Don't use view
		$this->_helper->viewRenderer->setNoRender ( TRUE );
		// Check permission
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$blog = Engine_Api::_ ()->getItem ( 'blog', $this->_getParam ( 'blog_id' ) );
		if (! $this->_helper->requireAuth ()->setAuthParams ( $blog, $viewer, 'view' )->isValid ())
			return;
			// Process
		$table = Engine_Api::_ ()->getDbtable ( 'becomes', 'ynblog' );
		$db = $table->getAdapter ();
		$db->beginTransaction ();
		try {
			// Create become_member
			$become = $table->createRow ();
			$become->blog_id = $blog->blog_id;
			$become->user_id = $viewer->getIdentity ();
			$become->save ();

			$blog->become_count = $blog->become_count + 1;
			$blog->save ();
			// Commit
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
	}
	public function uploadPhotoAction() {
		// Disable layout
		$this->_helper->layout->disableLayout ();

		$user_id = Engine_Api::_ ()->user ()->getViewer ()->getIdentity ();
		$destination = "public/ynblog/";
		if (! is_dir ( $destination )) {
			mkdir ( $destination );
		}
		$destination = "public/ynblog/" . $user_id . "/";
		if (! is_dir ( $destination )) {
			mkdir ( $destination );
		}
		$upload = new Zend_File_Transfer_Adapter_Http ();
		$upload->setDestination ( $destination );
		$file_info = pathinfo($upload -> getFileName('userfile', false));

        $fullFilePath = $destination . time() . '.' . $file_info['extension'];

		$image = Engine_Image::factory ();
		$image->open ( $_FILES ['userfile'] ['tmp_name'] )->resize ( 720, 720 )->write ( $fullFilePath );

		$this->view->status = true;
		$this->view->name = $_FILES ['userfile'] ['name'];
		$this->view->photo_url = Zend_Registry::get ( 'StaticBaseUrl' ) . $fullFilePath;
		$this->view->photo_width = $image->getWidth ();
		$this->view->photo_height = $image->getHeight ();
	}
	public function rssAction() {
		// Disable layout
		$this->_helper->layout->disableLayout ();

		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		// Must be able to view blogs
		if (! Engine_Api::_ ()->authorization ()->isAllowed ( 'blog', $viewer, 'view' )) {
			return;
		}
		$cat = $this->_getParam ( 'category' );
		$blog_id = $this->_getParam ( 'rss_id' );
		$owner_id = $this->_getParam ( 'owner' ); //
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_main' );
		if ($cat && $blog_id <= 0) {
			// Get navigation
			$params = array ();
			if ($cat > 0) {
				$params ['category'] = $cat;
				if ($owner_id) {
					$params ['user_id'] = $owner_id; //
				}
				$categories = Engine_Api::_ ()->getItemTable ( 'blog_category' )->getCategories ();
				foreach ( $categories as $category ) {
					if ($category->category_id == $cat) {
						$pro_type_name = $category->category_name;
					}
				}
			} else
				$pro_type_name = "All Blogs";
		} else {
			$pro_type_name = 'Blog';
			$params ['blogRss'] = $blog_id;
		}
		$table = Engine_Api::_ ()->getItemTable ( 'blog' );
		$blogs = $table->fetchAll ( Ynblog_Api_Core::getBlogsSelect ( $params ) );
		$this->view->blogs = $blogs;
		$this->view->pro_type_name = str_replace ( '&', '-', $pro_type_name );
		$this->getResponse()->setHeader('Content-type', 'text/xml');
	}
	public function favoriteAjaxAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$blogId = $this -> _getParam('blog_id', 0);
		if(!$blog = Engine_Api::_() -> getItem('blog', $blogId))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable('favorites', 'ynblog');
		$row = $table -> createRow();
		$row -> user_id = $viewer -> getIdentity();
		$row -> blog_id = $blog -> getIdentity();
		$row -> save();
		exit();
	}
	
	public function unFavoriteAjaxAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$blogId = $this -> _getParam('blog_id', 0);
		if(!$blog = Engine_Api::_() -> getItem('blog', $blogId))
		{
			return;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbTable('favorites', 'ynblog');
		$select = $table -> select() -> where('blog_id = ?', $blogId) -> where('user_id = ?', $viewer -> getIdentity()) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
		{
			$row -> delete();
		}
		exit();
	}
	public function favoriteAction()
	{
		// Check authoraiztion permisstion
		if (! $this->_helper->requireUser ()->isValid ())
			return;

		// Render
		$this->_helper->content->setEnabled ();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Get navigation
		$this->view->form = $form = new Ynblog_Form_Search ();
		// Populate form
		$categories = Engine_Api::_ ()->getDbtable ( 'categories', 'ynblog' )->getCategoriesAssoc ();
		if (! empty ( $categories ) && is_array ( $categories ) && $form->getElement ( 'category' )) {
			$form->getElement ( 'category' )->addMultiOptions ( $categories );
		}
		$form -> removeElement('mode');
		$form -> removeElement('show');

		// Process form
		$form->isValid ( $this->_getAllParams () );
		$values = $form->getValues ();
		$this->view->formValues = array_filter ( $values );
		$values['favorite_owner_id'] = $viewer -> getIdentity();
		// Get blog paginator
		$this->view->paginator = $paginator = Engine_Api::_ ()->ynblog ()->getBlogsPaginator ( $values );
		$items_per_page = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.page', 10 );
		$paginator->setItemCountPerPage ( $items_per_page );
		$this->view->paginator = $paginator->setCurrentPageNumber ( $values ['page'] );
	}
}
