<?php
class User_AdminUpgradeRequestsController extends Core_Controller_Action_Admin {
 	 public function indexAction() 
 	 {
 	 	$table = Engine_Api::_()->getDbTable('membershiprequests', 'user');
		$this -> view -> form = $form = new User_Form_Admin_RequestSearch();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = $params;
		$select = $table->select()
			->where('approved = ?', '0')
			->order('membershiprequest_id DESC');
		if(!empty($params['first_name']))
		{
			$first_name = $params['first_name'];
			$select -> where("first_name like ?", "%{$first_name}%");
		}
		if(!empty($params['last_name']))
		{
			$last_name = $params['last_name'];
			$select -> where("last_name like ?", "%{$last_name}%");
		}
		if(!empty($params['email']))
		{
			$email = $params['email'];
			$select -> where("email like ?", "%{$email}%");
		}
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
		$page = $this->_getParam('page',1);
		$this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
 	 }
	 
	 public function viewDetailAction() {
	 	$this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id', 0);
		$table = Engine_Api::_()->getDbTable('membershiprequests', 'user');
		$request = Engine_Api::_()->getItem('user_membershiprequest', $id);
		if (!request) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array('Request not found.')
            ));
		}
		$this->view->req = $request;
	 }
	 
	 public function rejectAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->request_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
        	$table = Engine_Api::_()->getDbTable('membershiprequests', 'user');
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
			$post =  $this->getRequest()->getPost();
            try {
            	$viewer = Engine_Api::_()->user()->getViewer();
				$request = Engine_Api::_()->getItem('user_membershiprequest', $id);
                $request->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This request has been rejected.')
            ));
        }
    }
	
	public function approveAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id', 0);
        $table = Engine_Api::_()->getDbTable('membershiprequests', 'user');
		$request = Engine_Api::_()->getItem('user_membershiprequest', $id);
		if (!request) {
			$this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> false,
                'messages' => array('Request not found.')
            ));
		}
		if( $this->getRequest()->isPost()) 
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
		    $post = $this->getRequest()->getPost();
		    try {
		    	$request->approved = 1;
				$request->save();
				
				// update subscription
				$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
				$package = $packagesTable->fetchRow(array(
			      'enabled = ?' => 1,
			      'package_id = ?' => (int) $request -> package_id,
			    ));
				$user = Engine_Api::_() -> getItem('user', $request -> user_id);
				$subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
				$currentSubscription = $subscriptionsTable->fetchRow(array(
			      'user_id = ?' => $user->getIdentity(),
			      'active = ?' => true,
			    ));
				$subscription = $subscriptionsTable->createRow();
			    $subscription->setFromArray(array(
			        'package_id' => $package->package_id,
			        'user_id' => $user->getIdentity(),
			        'status' => 'initial',
			        'active' => false, // Will set to active on payment success
			        'creation_date' => new Zend_Db_Expr('NOW()'),
			    ));
			    $subscription->save();
				$subscription->setActive(true);
		        $subscription->onPaymentSuccess();
		        if( $currentSubscription ) {
		          $currentSubscription->cancel();
        		}
		      	$db->commit();
		    } 
		    catch( Exception $e ) {
		      	$db->rollBack();
		      	if( APPLICATION_ENV == 'development' ) {
		        	throw $e;
		      	}
		    }
			
			$this->_forward('success', 'utility', 'core', array(
	            'smoothboxClose' => true,
	            'parentRefresh'=> true,
	            'messages' => array('Request has been approved.')
	        ));
        }
    }
	
	public function multirejectAction() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', NULL);
        $confirm = $this -> _getParam('confirm', FALSE);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == TRUE) {
            //Process delete
            $ids_array = explode(",", $ids);
			$table = Engine_Api::_()->getDbTable('membershiprequests', 'user');
            foreach ($ids_array as $id) {
                $request = Engine_Api::_()->getItem('user_membershiprequest', $id);
                if ($request) {
                    $request->delete();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('module'=>'user','controller'=>'upgrade-requests', 'action'=>'index'), 'admin_default', TRUE);
        }
    }
}