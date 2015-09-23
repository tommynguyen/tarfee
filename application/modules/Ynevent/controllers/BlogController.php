<?php
/**
 * Company:	Younetco
 * Author: 	LuanND
 */
class Ynevent_BlogController extends Core_Controller_Action_Standard {
	public function init() {
		// only show to member_level if authorized
		if (!$this -> _helper -> requireAuth() -> setAuthParams('blog', null, 'view') -> isValid())
			return;
		
	}

	public function removeAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$blog = Engine_Api::_() -> getItem('blog', $this ->_getParam('blog_id'));
		$event_id = $this ->_getParam('event_id');
		$event = Engine_Api::_()->getItem('event', $event_id);
 
		if(!$event->isOwner($viewer))
		{
			return $this->_forward('requireauth', 'error', 'core');
		}
		
		$tab = $this ->_getParam('tab');
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynevent_Form_Blog_Remove();

		if (!$blog) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Blog entry doesn't exist or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $blog -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$this->deleteEventBlog();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your blog entry has been remove.');
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id'=> $event_id, 'tab' => $tab), 'event_profile', true), 'messages' => Array($this -> view -> message)));
	}

	public function deleteAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$blog = Engine_Api::_() -> getItem('blog', $this ->_getParam('blog_id'));
		if(!$blog->isOwner($viewer))
		{
			return $this->_forward('requireauth', 'error', 'core');
		}
		$event_id = $this ->_getParam('event_id');
		$tab = $this ->_getParam('tab');
		if (!$this -> _helper -> requireAuth() -> setAuthParams($blog, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		$this -> view -> form = $form = new Ynevent_Form_Blog_Delete();

		if (!$blog) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Blog entry doesn't exist or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $blog -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {
			$this->deleteEventBlog();
			$blog -> delete();

			$db -> commit();
		} catch( Exception $e ) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your blog entry has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id'=> $event_id, 'tab' => $tab), 'event_profile', true), 'messages' => Array($this -> view -> message)));
	}

	private function deleteEventBlog() {
		$event_id = $this ->_getParam('event_id');
		$blog_id = $this ->_getParam('blog_id');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		$h_tbl = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
		$select = $h_tbl -> select() -> where("event_id = ?", $event_id) -> where('item_id = ?', $blog_id) -> where("user_id = ?", $viewer->getIdentity()) -> where("type = 'blog'") -> limit(1);
		$row = $h_tbl -> fetchRow($select);
		
		if ($row) {
			$row -> delete();
		} 
	}
	public function importBlogsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this->view -> event_id = $event_id = $this ->_getParam('event_id');
		$event = Engine_Api::_()->getItem('event', $event_id);
 		
		$tab = $this ->_getParam('tab');
		if(!$event->isOwner($viewer))
		{
			return $this->_forward('requireauth', 'error', 'core');
		}
		
		$this -> view -> form = $form = new Ynevent_Form_Blog_Import();
		
		$blog_tbl = Engine_Api::_() -> getItemTable('blog');
		$rName = $blog_tbl -> info('name');
		$select = $blog_tbl -> select() -> setIntegrityCheck(false) 			
			-> where('draft = 0')
			-> order('creation_date DESC')
			;
		if(Engine_Api::_()->hasModuleBootstrap('ynblog'))
		{
			$select->where("is_approved = 1");
			$this->view ->css = 'yn';
		}
		$this->view ->css = '';
		$blogs = $blog_tbl -> fetchAll($select);
		$data = array();
		if($blogs)
		{
			foreach($blogs as $blog)
			{
				$owner = Engine_Api::_() -> getItem('user', $blog->owner_id);
				
				$data[$blog -> getIdentity()] = array(						
						'blog_href' => $blog -> getHref(),
						'blog_title' => $blog -> title,
						'blog_body' =>$this->view->string()->truncate(strip_tags($blog->body),100),
						'blog_creation_date' => $this->view->timestamp(strtotime($blog->creation_date)),
						'owner_photo' => $this -> view -> itemPhoto($blog->getOwner(), 'thumb.icon'),
						'owner_title' => $blog->getOwner()->getTitle(),
					);
			}
		}
		$this->view->json_blog = Zend_Json::encode($data);
		
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		if($_POST['to'])
		{
			$table = Engine_Api::_() -> getDbTable('highlights', 'ynevent');
			try {
				$blog = Engine_Api::_()->getItem('blog', $_POST['to']);
				if($blog)
				{
					$row = $table -> createRow();
				    $row -> setFromArray(array(
				       'event_id' => $event_id,
				       'item_id' => $_POST['to'],
				       'user_id' => $viewer->getIdentity(),				       
				       'type' => 'blog',
				       'creation_date' => date('Y-m-d H:i:s'),
				       'modified_date' => date('Y-m-d H:i:s'),
				       ));
				    $row -> save();
					//send notification
					if($viewer->getIdentity() != $blog -> owner_id)
					{
						//send notification						
						$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
						$notifyApi -> addNotification($blog->getOwner(), $blog, $event, 'event_import_blog');
					}
				}
				else {
					return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id'=> $event_id, 'tab' => $tab), 'event_profile', true), 'messages' => Zend_Registry::get('Zend_Translate')->_('Import Fail.')));
				}
			}
			catch (Exception $e) {
			}		
		}
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('id'=> $event_id, 'tab' => $tab), 'event_profile', true), 'messages' => Zend_Registry::get('Zend_Translate')->_('Import Success.')));
		
	}
	public function importAction()
	{
		$text = $this -> _getParam('value','');
		
		$blog_tbl = Engine_Api::_() -> getItemTable('blog');
		$rName = $blog_tbl -> info('name');
		$select = $blog_tbl -> select() -> setIntegrityCheck(false) 
			-> where('title LIKE ?', '%'.$text.'%') 
			-> where('draft = 0')
			-> order('creation_date DESC')
			;
		if(Engine_Api::_()->hasModuleBootstrap('ynblog'))
		{
			$select->where("is_approved = 1");
		}
		
		$blogs = $blog_tbl -> fetchAll($select);
		$data = array();
		if($blogs)
		{
			foreach($blogs as $blog)
			{
				$data[] = array(
						'type' => 'user',
						'id' => $blog -> getIdentity(),
						'guid' => $blog -> getGuid(),
						'label' => $blog -> getTitle(),
						'photo' => $this -> view -> itemPhoto($blog->getOwner(), 'thumb.icon'),
						'url' => $blog -> getHref(),
					);
			}
		}
		return $this -> _helper -> json($data);
	}
}
