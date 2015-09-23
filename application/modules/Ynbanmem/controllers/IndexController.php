<?php

class Ynbanmem_IndexController extends Core_Controller_Action_Standard {

//6-6-2013
public function usersAction()
	{
		if( !$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'manage_user')->isValid() ) return;
		// Get navigation
        // $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                // ->getNavigation('ynbanmem_main');
				
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynbanmem_main', array(), 'ynbanmem_manage_users');
		
		$this->view->form = $form = new Ynbanmem_Form_ManageUsers();
		$table = Engine_Api::_()->getDbtable('users', 'user');
	    $select = $table->select();
	
	    // Process form
	    $values = array();
	    if( $form->isValid($this->_getAllParams()) ) {
	      $values = $form->getValues();
	    }
	
	    foreach( $values as $key => $value ) {
	      if( null === $value ) {
	        unset($values[$key]);
	      }
	    }
		$values = array_merge(array(
	      'order' => 'user_id',
	      'order_direction' => 'ASC',
	    ), $values);
	    
	    $this->view->assign($values);		
		
		
		// Build banned IPs
	    $bannedIpsNew = preg_split('/\s*[,\n]+\s*/', $values['ip']);

	    foreach( $bannedIpsNew as &$bannedIpNew ) {
	      if( false !== strpos($bannedIpNew, '-') ) {
	        $bannedIpNew = preg_split('/\s*-\s*/', $bannedIpNew, 2);
	      } else if( false != strpos($bannedIpNew, '*') ) {
	        $tmp = $bannedIpNew;
	        if( false != strpos($tmp, ':') ) {
	          $bannedIpNew = array(
	            str_replace('*', '0', $tmp),
	            str_replace('*', 'ffff', $tmp),
	          );
	        } else {
	          $bannedIpNew = array(
	            str_replace('*', '0', $tmp),
	            str_replace('*', '255', $tmp),
	          );
	        }
	      }
	    }
		
	    // Set up select info
	    $select->order(( !empty($values['order']) ? $values['order'] : 'user_id' ) . ' ' . ( !empty($values['order_direction']) ? $values['order_direction'] : 'ASC' ));
	
		if( !empty($values['user_id']) ) {
	      $select->where('user_id = ?',$values['user_id']);
	    }
		//search creation ip
		if($values['typeIp'] == 0)
		{
			$type = 'creation_ip';
		}//search last login ip
		elseif($values['typeIp'] == 1)
		{
			$type = 'lastlogin_ip';
		} //no search
		else {
			
		}
		
		if(isset($type))
		{
			if(is_array($bannedIpsNew[0]))
			{ 
				$start = substr($bannedIpsNew[0][0],strrpos($bannedIpsNew[0][0],".")+1,strlen($bannedIpsNew[0][0]));
				$end = substr($bannedIpsNew[0][1],strrpos($bannedIpsNew[0][1],".")+1,strlen($bannedIpsNew[0][1]));
				
				$temIp = substr($bannedIpsNew[0][0], 0, strrpos($bannedIpsNew[0][0],".")).".";
				
				for($i = $start; $i<=$end;$i++)
				{
					
					$stringIp =  new Engine_IP($temIp.$i);
					$stringIp = $stringIp->toBinary();	
					$ip .= "'".bin2hex($stringIp)."',";
				}
				$ip = "(".substr($ip,0, -1).")";	
							
				$this->view->type = $type;
				$select->where("HEX($type) IN ".$ip);
			}
			elseif(is_string($bannedIpsNew[0]) && !empty($bannedIpsNew[0]))
			{
				
				$stringIp =  new Engine_IP($bannedIpsNew[0]);
				$stringIp = $stringIp->toBinary();	
				$this->view->type = $type;		
				$select->where("HEX($type) = ?",bin2hex($stringIp));	
			}
			else {
				
			}
		}
				
	    if( !empty($values['displayname']) ) {
	      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
	    }
	    if( !empty($values['username']) ) {
	      $select->where('username LIKE ?', '%' . $values['username'] . '%');
	    }
	    if( !empty($values['email']) ) {
	      $select->where('email LIKE ?', '%' . $values['email'] . '%');
	    }
			
		
		
		
		$page = $this->_getParam('page', 1);
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
    	$this->view->paginator = $paginator->setCurrentPageNumber( $page );
	} 
    public function indexAction() {


        // Get navigation
        // $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        //        ->getNavigation('ynbanmem_main');
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynbanmem_main', array(), 'ynbanmem_main_browse');
        if (count($this->view->navigation) == 1) {
            $this->view->navigation = null;
        }

        // Check auth
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'manage')->isValid()) {
            return;
        }


        // Get data
        $bannedUsernameTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
        $bannedUsernames = $bannedUsernameTable->getAllBannedUsers();

        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($bannedUsernames);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));


        // Preload users
        $identities = array();
        foreach ($paginator as $item) {
            if (!empty($item['user'][0]['user_id'])) {
                $identities[] = $item['user'][0]['user_id'];
            }
        }
        $identities = array_unique($identities);

        $users = array();
        if (!empty($identities)) {
            foreach (Engine_Api::_()->getItemMulti('user', $identities) as $user) {
                $users[$user->getIdentity()] = $user;
            }
        }

        $this->view->users = $users;
        $this->view->viewer = $viewer;
    }

//6-6-2013
    public function viewEmailAction() {


        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main');

        if (count($this->view->navigation) == 1) {
            $this->view->navigation = null;
        }

        // Check auth
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'manage')->isValid()) {
            return;
        }


        // Get data
        $bannedEmailTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
        $bannedEmails = $bannedEmailTable->getAllBannedEmails();

        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($bannedEmails);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));


        // Preload users
        $identities = array();
        foreach ($paginator as $item) {
            if (!empty($item['user'][0]['user_id'])) {
                $identities[] = $item['user'][0]['user_id'];
            }
        }
        $identities = array_unique($identities);

        $users = array();
        if (!empty($identities)) {
            foreach (Engine_Api::_()->getItemMulti('user', $identities) as $user) {
                $users[$user->getIdentity()] = $user;
            }
        }
        $this->view->users = $users;
        $this->view->viewer = $viewer;
    }

    public function addAction() {

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main');
        if (count($this->view->navigation) == 1) {
            $this->view->navigation = null;
        }

        // Check auth
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'add')->isValid()) {
            return;
        }
        // Make params
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main', array(), 'ynbanmem_main_add');
        // Make form
        $this->view->form = $form = new Ynbanmem_Form_Add( array('type' => $this -> _getParam('type', 0)	));

        //  Get data if the request come from profile page
        $id = $this->_getParam('id', null);
        if ($id != "") {
            $user = Engine_Api::_()->getItem('user', $id);
            $form->populate(array('email' => $user->email));
        }

        if (!$this->getRequest()->isPost()) {
            return;
        }

        $post = $this->getRequest()->getPost();
		
        if (!$form->isValid($post))
            return;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $flag = true;
        // Process
        $bannedUsernamesTable = Engine_Api::_()->getDbTable('bannedusernames', 'ynbanmem');
        $bannedIpsTable = Engine_Api::_()->getDbTable('bannedips', 'ynbanmem');
        $bannedEmailsTable = Engine_Api::_()->getDbTable('bannedemails', 'ynbanmem');
        $extraInfoTable = Engine_Api::_()->getDbTable('extrainfo', 'ynbanmem');
        $userTable = Engine_Api::_()->getDbTable('users', 'user');
        try {
            $values = $form->getValues();
            
            $values['admin'] = Engine_Api::_()->user()->getViewer()->getIdentity();

            //Get expiry date
            if (strtotime($values['expiry_date']) > 0) {
                // Convert times
                $oldTz = date_default_timezone_get();
                date_default_timezone_set($viewer->timezone);
                $expiry_date = strtotime($values['expiry_date']);
                $now = strtotime(date('Y-m-d H:i:s'));
                date_default_timezone_set($oldTz);
                $values['expiry_date'] = date('Y-m-d H:i:s', $expiry_date);

                if ($expiry_date <= $now) {
                    $form->getElement('expiry_date')->addError('Expiry Date should be greater than Current Time!');
                    return;
                }
            } else {
                $values['expiry_date'] = "0000-00-00 00:00:00";
            }
            $info = $values;
       		$info['email_message'] = trim($values['email_message']);
            switch ($values['type']) {
                // Ban username
                case 1:
                    if ($values['username'] == "") {
                        $form->getElement('username')->addError('Please complete this field - it is required.');
                        return;
                    }
                    $info['type'] = 0; // Username
                    $bannedUsernamesNew = preg_split('/\s*[,\n]+\s*/', $values['username']);
                    foreach ($bannedUsernamesNew as $newUsername) {
                        $user = $userTable->select()
                                ->where('username = ?', $newUsername)
                                ->query()
                                ->fetchAll();
                       if (count($user) == 0 || $user[0]['level_id'] == 1 || $viewer->username == $user[0]['username']) {
                            $form->getElement('username')->addError('There is not any users relate to this username or the usernames you entered contains your own username or admin username.');
                            return;
                        }
                    }
                   $bannedUsernamesTable->setBannedUsernames($bannedUsernamesNew, $info);
                    $form->addNotice('Your changes have been saved.');
                    unset($values['bannedusernames']);
                    break;

                case 2:
                     if ($values['ip'] == "") {
                        $form->getElement('ip')->addError('Please complete this field - it is required.');
                        return;
                    }
                    $info['type'] = 1; // Ip
                    // Build banned IPs
					try
					{
						$bannedIpsNew = preg_split('/\s*[,\n]+\s*/', $values['ip']);
						foreach ($bannedIpsNew as &$bannedIpNew) {
							if (false !== strpos($bannedIpNew, '-')) {
								$bannedIpNew = preg_split('/\s*-\s*/', $bannedIpNew, 2);
							} else if (false != strpos($bannedIpNew, '*')) {
								$tmp = $bannedIpNew;
								if (false != strpos($tmp, ':')) {
									$bannedIpNew = array(
										str_replace('*', '0', $tmp),
										str_replace('*', 'ffff', $tmp),
									);
								} else {
									$bannedIpNew = array(
										str_replace('*', '0', $tmp),
										str_replace('*', '255', $tmp),
									);
								}
							}
						}
					}
					catch (Exception $e) {
						$db->rollBack();
						return $form->addError('');
					}

                    // Check if they are banning their own address
                    if ($bannedIpsTable->isAddressBanned(Engine_IP::getRealRemoteAddress(), $bannedIpsTable->normalizeAddressArray($bannedIpsNew))) {
                        return $form->addError('One of the IP addresses or IP address ranges you entered contains your own IP address.');
                    }

                    if (!empty($bannedIpNew)) {

                        // Save Banned IPs
                        $bannedIpsTable->setAddresses($bannedIpsNew, $info);
                        unset($values['bannedips']);
                    }
                    $form->addNotice('Your changes have been saved.');
                    break;

                // Ban Emails
                case 0:
				
                    if ($values['email'] == "") {
                        $form->getElement('email')->addError('Please complete this field - it is required.');
                        return;
                    }
                    $info['type'] = 2; // Email
                    // Save Banned Emails
                    $bannedEmailsNew = preg_split('/\s*[,\n]+\s*/', $values['email']);

                    foreach ($bannedEmailsNew as $newEmail) {

                        $user = $userTable->select()
                                ->where('email = ?', $newEmail)
                                ->query()
                                ->fetchAll();
						
                        if (count($user) == 0 || $user[0]['level_id'] == 1 || $viewer->email == $user[0]['email']) {
                            $form->getElement('email')->addError('There is not any users relate to the email(s) or the email(s) you entered contains your own email or admin email.');
                            return;
                        }
                    }
                   $bannedEmailsTable->setEmails($bannedEmailsNew, $info);
                    $form->addNotice('Your changes have been saved.');
                    unset($values['email']);
                    break;
                default:
                    break;
            }

            $db->commit();
            //$form->addNotice('There is not any user relate to this email.');
            switch ($values['type']) {
                // Ban username
                case 1:
                    return $this->_helper->redirector->gotoRoute(array('action' => ''), 'ynbanmem_general', true);
                    break;
                case 2:
                    return $this->_helper->redirector->gotoRoute(array('action' => 'view-ip'), 'ynbanmem_general', true);
                    break;
                case 0:
                    return $this->_helper->redirector->gotoRoute(array('action' => 'view-email'), 'ynbanmem_general', true);
                    break;
            }
//             
//      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'user_account_approved', array(
//        'host' => $_SERVER['HTTP_HOST'],
//        'email' => $user->email,
//        'date' => time(),
//        'recipient_title' => $user->getTitle(),
//        'recipient_link' => $user->getHref(),
//        'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
//        'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
//      ));
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
//6-6-2013
    public function viewIpAction() {

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main');

        if (count($this->view->navigation) == 1) {
            $this->view->navigation = null;
        }

        //Get banned ip list
        $bannedIpTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
        $bannedIps = $bannedIpTable->getAddresses();
		
        // Get paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($bannedIps);
        $paginator->setItemCountPerPage(20);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer = $viewer;
    }

    //6-6-2013
    public function deleteAction() {
        $id = $this->_getParam('id', null);
        //$type = $this->_getParam('type', null);
        $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
        $this->view->form = $form = new User_Form_Admin_Manage_Delete();
        // deleting user
        //$form->user_id->setValue($id);

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');

                $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');

                $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');

                $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');

                $bannedUsername = $bannedUsernamesTable->select()
                        ->where('username = ?', $user->username)
                        ->query()
                        ->fetchAll();
                $bannedEmail = $bannedEmailsTable->select()
                        ->where('email = ?', $user->email)
                        ->query()
                        ->fetchAll();

                if (count($bannedUsername) != 0) {
                    $exist1 = $extraInfoTable->select()
                            ->where('banned_id = ?', $bannedUsername[0]['bannedusername_id'])
                            ->where('banned_type = ?', 0)
                            ->query()
                            ->fetch();
                    if (count($exist1) != 0) {
                        $extraInfoTable->delete(array(
                            'banned_id = ?' => $bannedUsername[0]['bannedusername_id'],
                            'banned_type = ?' => 0
                        ));
                    }
                    $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $bannedUsername[0]['bannedusername_id']));
                }
                if (count($bannedEmail) != 0) {
                    $exist2 = $extraInfoTable->select()
                            ->where('banned_id = ?', $bannedEmail[0]['bannedemail_id'])
                            ->where('banned_type = ?', 2)
                            ->query()
                            ->fetch();

                    if (count($exist2) != 0) {
                        $extraInfoTable->delete(array(
                            'banned_id = ?' => $bannedEmail[0]['bannedemail_id'],
                            'banned_type = ?' => 2
                        ));
                    }
                    $bannedEmailsTable->delete(array('bannedemail_id = ?' => $bannedEmail[0]['bannedemail_id']));
                }

                // Delelete banned Username and Email
                $user->delete(); 
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => array('Delete successful.')
                    ));
        }
    }

    public function multiModifyAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {

                if ($key == 'modify_' . $value) {

                    $user = Engine_Api::_()->getItem('user', (int) $value);
                    if ($values['submit_button'] == 'delete') {

                        if ($user->level_id != 1) {
                            switch ($values['type']) {
                                case 'username':

                                    // Username
                                    $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'core');
                                    $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');

                                    $removedBannedUsername = $bannedUsernamesTable->select()
                                            ->where('username = ?', $user->username)
                                            ->query()
                                            ->fetchAll();

                                    $bannedUsernamesTable->delete(array('username = ?' => $user->username));
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $removedBannedUsername[0]['bannedusername_id'],
                                        'banned_type = ?' => '0'
                                    ));
                                    break;
                                    ;
                                case 'email':
                                    // Mail
                                    $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
                                    $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');

                                    $removedBannedEmail = $bannedEmailsTable->select()
                                            ->where('email = ?', $user->email)
                                            ->query()
                                            ->fetchAll();

                                    $bannedEmailsTable->delete(array('email = ?' => $user->email));
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $removedBannedEmail[0]['bannedusername_id'],
                                        'banned_type = ?' => '0'
                                    ));
                                    break;
                            }
                            //$user->delete();
                        }
                    } else
                    if ($values['submit_button'] == 'unban') {
                        $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
                        $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
                        $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
                        $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');
                        $id = $value;

                        switch ($values['type']) {
                            //Unban username
                            case 'username':

                                $exists = $extraInfoTable->select()
                                        ->where('banned_id = ?', $id)
                                        ->where('banned_type = ?', 0)
                                        ->query()
                                        ->fetch();

                                if (count($exists) != 0) {
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $id,
                                        'banned_type = ?' => 0
                                    ));
                                }
                                $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $id));

                                break;
                            //Unban Email -->done
                            case 'email':

                                $exists = $extraInfoTable->select()
                                        ->where('banned_id = ?', $id)
                                        ->where('banned_type = ?', 2)
                                        ->query()
                                        ->fetch();

                                if (count($exists) != 0) {

                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $id,
                                        'banned_type = ?' => 2
                                    ));
                                }
                                $bannedEmailsTable->delete(array('bannedemail_id = ?' => $id));
                                break;
                            //Unban Ips
                            case 'ip':
                                $id = $this->_getParam('id', null);

                                $exists = $extraInfoTable->select()
                                        ->where('banned_id = ?', $id)
                                        ->where('banned_type = ?', 1)
                                        ->query()
                                        ->fetch();

                                if (count($exists) != 0) {
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $id,
                                        'banned_type = ?' => 1
                                    ));
                                }

                                $bannedIpsTable->delete(array('bannedip_id = ?' => $id));

                                break;
                        }
                    }
                }
            }
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

// work on it
    public function multiDeleteAction() {

        //$this->view->form = $form = new Ynbanmem_Form_MultiDelete();
        if ($this->_getParam('userIds'))
            $this->view->userIds = $userIds = $this->_getParam('userIds');

        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            $values = $this->_getParam('userIds', null);

            $values = str_replace(array("(", ")", "on"), "", $values);
            $id_arr = array_filter(explode(",", $values));

            foreach ($id_arr as $key => $id) { {

                    $user = Engine_Api::_()->getItem('user', (int) $id); {

                        if ($user->level_id != 1) {

                            $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');

                            $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');

                            $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');

                            $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');

                            $bannedUsername = $bannedUsernamesTable->select()
                                    ->where('username = ?', $user->username)
                                    ->query()
                                    ->fetchAll();
                            $bannedEmail = $bannedEmailsTable->select()
                                    ->where('email = ?', $user->email)
                                    ->query()
                                    ->fetchAll();

                            if (count($bannedUsername) != 0) {
                                $exist1 = $extraInfoTable->select()
                                        ->where('banned_id = ?', $bannedUsername[0]['bannedusername_id'])
                                        ->where('banned_type = ?', 0)
                                        ->query()
                                        ->fetch();
                                if (count($exist1) != 0) {
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $bannedUsername[0]['bannedusername_id'],
                                        'banned_type = ?' => 0
                                    ));
                                }
                                $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $bannedUsername[0]['bannedusername_id']));
                            }
                            if (count($bannedEmail) != 0) {
                                $exist2 = $extraInfoTable->select()
                                        ->where('banned_id = ?', $bannedEmail[0]['bannedemail_id'])
                                        ->where('banned_type = ?', 2)
                                        ->query()
                                        ->fetch();

                                if (count($exist2) != 0) {
                                    $extraInfoTable->delete(array(
                                        'banned_id = ?' => $bannedEmail[0]['bannedemail_id'],
                                        'banned_type = ?' => 2
                                    ));
                                }
                                $bannedEmailsTable->delete(array('bannedemail_id = ?' => $bannedEmail[0]['bannedemail_id']));
                            }

                            // Delelete banned Username and Email
                            $user->delete();   
                        }
                    }
                }
            }
            $db->commit();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Delete users successful.')
            ));
        }
    }

    public function multiUnbanAction() {

        $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');
        $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');
        $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');
        $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');

        if ($this->_getParam('unbanList')) {
            $this->view->unbanList = $unbanList = $this->_getParam('unbanList');
            $this->view->type = $type = $this->_getParam('type');
        }
        if ($this->getRequest()->isPost()) {
            try {
                $db = Engine_Db_Table::getDefaultAdapter();
                $db->beginTransaction();
                $type = $this->_getParam('type', null);
                $values = $this->_getParam('unbanList', null);

                $values = str_replace(array("(", ")", "on"), "", $values);
                $id_arr = array_filter(explode(",", $values));

                foreach ($id_arr as $key => $id) {
                    switch ($type) {
                        //Unban username
                        case 'username':

                            $user = Engine_Api::_()->getItem('user', (int) $id); { {

                                    $bannedUsername = $bannedUsernamesTable->select()
                                            ->where('username = ?', $user->username)
                                            ->query()
                                            ->fetchAll();

                                    if (count($bannedUsername) != 0) {
                                        $exist1 = $extraInfoTable->select()
                                                ->where('banned_id = ?', $bannedUsername[0]['bannedusername_id'])
                                                ->where('banned_type = ?', 0)
                                                ->query()
                                                ->fetch();
                                        if (count($exist1) != 0) {
                                            $extraInfoTable->delete(array(
                                                'banned_id = ?' => $bannedUsername[0]['bannedusername_id'],
                                                'banned_type = ?' => 0
                                            ));
                                        }
                                        $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $bannedUsername[0]['bannedusername_id']));
                                    }
                                }
                            }
                            break;
                        //Unban Email -->done
                        case 'email':

                            $user = Engine_Api::_()->getItem('user', (int) $id); 
							 $bannedEmail = $bannedEmailsTable->select()
                                            ->where('email = ?', $user->email)
                                            ->query()
                                            ->fetchAll();

                                    if (count($bannedEmail) != 0) {
									
                                        $exist2 = $extraInfoTable->select()
                                                ->where('banned_id = ?', $bannedEmail[0]['bannedemail_id'])
                                                ->where('banned_type = ?', 2)
                                                ->query()
                                                ->fetch();

                                        if (count($exist2) != 0) {
										
                                            $extraInfoTable->delete(array(
                                                'banned_id = ?' => $bannedEmail[0]['bannedemail_id'],
                                                'banned_type = ?' => 2
                                            ));
                                        }
                                        $bannedEmailsTable->delete(array('bannedemail_id = ?' => $bannedEmail[0]['bannedemail_id']));
                            }
                            break;
                        //Unban Ips
                        case 'ip':

                            $exists = $extraInfoTable->select()
                                    ->where('banned_id = ?', $id)
                                    ->where('banned_type = ?', 1)
                                    ->query()
                                    ->fetch();

                            if (count($exists) != 0) {
                                $extraInfoTable->delete(array(
                                    'banned_id = ?' => $id,
                                    'banned_type = ?' => 1
                                ));
                            }

                            $bannedIpsTable->delete(array('bannedip_id = ?' => $id));

                            break;
                    }
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'format' => 'smoothbox',
                'messages' => array('Unban successful.')
            ));
        }
    }

    
    //6-6-2013
    public function noteAction() {

        // Check auth
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'manage')->isValid()) {
            return;
        }

        //
        $id = $this->_getParam('id', null);
        $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
        $this->view->form = $form = new Ynbanmem_Form_Note();


        if ($user->note != NULL)
            $form->populate(array('note' => $user->note));
        //check method/valid
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
		
        //add note
       
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        $values = $form->getValues();
		 
		$user->note = $values['note'];
			
        $user->save();
//            unset($user);$user = Engine_Api::_()->getItem('user', $id);
//            print_r($user);die;
        // $userTable->updateUserNote($id, $values['note']);
        $db->commit();
        return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
                    'format' => 'smoothbox',
                    'messages' => array('Update successful.')
                    ));
       
    }

   public function loginAction()
  {
    $id = $this->_getParam('id');
    $user = Engine_Api::_()->getItem('user', $id);
    
    // @todo change this to look up actual superadmin level
    if( $user->level_id == 1 || !$this->getRequest()->isPost() ) {
      if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
        return $this->_helper->redirector->gotoRoute(array('action' => 'index', 'id' => null));
      } else {
        $this->view->status = false;
        $this->view->error = true;
        return;
      }
    }

    // Login
    Zend_Auth::getInstance()->getStorage()->write($user->getIdentity());

    // Redirect
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      $this->view->status = true;
      return;
    }
  }
//6-6-2013
    public function unbanAction() {


        $type = $this->_getParam('type', null);
        $id = $this->_getParam('id', null);

        $this->view->form = $form = new Ynbanmem_Form_Unban();

        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        if ($this->getRequest()->isPost()) {

            try {

                $bannedUsernamesTable = Engine_Api::_()->getDbtable('bannedusernames', 'ynbanmem');

                $bannedEmailsTable = Engine_Api::_()->getDbtable('bannedemails', 'ynbanmem');

                $bannedIpsTable = Engine_Api::_()->getDbtable('bannedips', 'ynbanmem');

                $extraInfoTable = Engine_Api::_()->getDbtable('extrainfo', 'ynbanmem');


                switch ($type) {
                    //Uban user and email
                    case 0:
                        $bannedUser_id = $this->_getParam('user', null);
                        $bannedEmail_id = $this->_getParam('email', null);

                        $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $bannedUser_id)
                                ->where('banned_type = ?', 0)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {
                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $bannedUser_id,
                                'banned_type = ?' => 0
                            ));
                        }
                        $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $bannedUser_id));

                        $exists = (bool) $extraInfoTable->select()
                                        ->where('banned_id = ?', $bannedEmail_id)
                                        ->where('banned_type = ?', 2)
                                        ->query()
                                        ->fetch();

                        if (count($exists) != 0) {
                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $bannedEmail_id,
                                'banned_type = ?' => 2
                            ));
                        }
                        $bannedEmailsTable->delete(array('bannedemail_id = ?' => $bannedEmail_id));
                        break;
                    //Unban username
                    case 1:

                        $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $id)
                                ->where('banned_type = ?', 0)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {
                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $id,
                                'banned_type = ?' => 0
                            ));
                        }
                        $bannedUsernamesTable->delete(array('bannedusername_id = ?' => $id));

                        break;
                    //Unban Email -->done
                    case 2:

                        $id = $this->_getParam('id', null);

                        $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $id)
                                ->where('banned_type = ?', 2)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {

                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $id,
                                'banned_type = ?' => 2
                            ));
                        }
                        $bannedEmailsTable->delete(array('bannedemail_id = ?' => $id));
                        break;
                    //Unban Ips
                    case 3:
                        $id = $this->_getParam('id', null);

                        $exists = $extraInfoTable->select()
                                ->where('banned_id = ?', $id)
                                ->where('banned_type = ?', 1)
                                ->query()
                                ->fetch();

                        if (count($exists) != 0) {
                            $extraInfoTable->delete(array(
                                'banned_id = ?' => $id,
                                'banned_type = ?' => 1
                            ));
                        }

                        $bannedIpsTable->delete(array('bannedip_id = ?' => $id));

                        break;
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRefresh' => true,
                        'format' => 'smoothbox',
                        'messages' => array('Unban successful.')
                    ));
        }
    }


	public function composeAction()
  {
   
    // Render
    /*$this->_helper->content
       //->setNoRender()
       ->setEnabled()
       ;*/
    // Make form
    $this->view->form = $form = new Ynbanmem_Form_Compose();
    //$form->setAction($this->view->url(array('to' => null, 'multi' => null)));
	$multi = $this->_getParam('multi');
    $to = $this->_getParam('to');
    $viewer = Engine_Api::_()->user()->getViewer();
    $toObject = null;
 
    // Build
    $isPopulated = false;
    if( !empty($to) && (empty($multi) || $multi == 'user') ) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
	 
      $isMsgable = true;//( 'friends' != Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ||
          //$viewer->membership()->isMember($toUser) );
		  
      if( $toUser instanceof User_Model_User &&
          (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
          isset($toUser->user_id) &&
          $isMsgable ) {
        $this->view->toObject = $toObject = $toUser;
        $form->toValues->setValue($toUser->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if( !empty($to) && !empty($multi) ) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      // Potential point of failure if primary key column is something other
      // than $multi . '_id'
      $item_id = $multi . '_id';
      if( $item instanceof Core_Model_Item_Abstract &&
          isset($item->$item_id) && (
            $item->isOwner($viewer) ||
            $item->authorization()->isAllowed($viewer, 'edit')
          )) {
        $this->view->toObject = $toObject = $item;
        $form->toValues->setValue($item->getGuid());
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }
    $this->view->isPopulated = $isPopulated;

    // Build normal
    if( !$isPopulated ) {
      // Apparently this is using AJAX now?
//      $friends = $viewer->membership()->getMembers();
//      $data = array();
//      foreach( $friends as $friend ) {
//        $data[] = array(
//          'label' => $friend->getTitle(),
//          'id' => $friend->getIdentity(),
//          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
//        );
//      }
//      $this->view->friends = Zend_Json::encode($data);
    }
    
    // Assign the composing stuff
    $composePartials = array();
    foreach( Zend_Registry::get('Engine_Manifest') as $data ) {
      if( empty($data['composer']) ) {
        continue;
      }
      foreach( $data['composer'] as $type => $config ) {
        // is the current user has "create" privileges for the current plugin
        $isAllowed = Engine_Api::_()
            ->authorization()
            ->isAllowed($config['auth'][0], null, $config['auth'][1]);
            
        if( !empty($config['auth']) && !$isAllowed ) {
          continue;
        }
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;
    // $this->view->composePartials = $composePartials;

    // Get config
    $this->view->maxRecipients = $maxRecipients = 10;


    // Check method/data
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
        $type = $attachmentData['type'];
        $config = null;
        foreach( Zend_Registry::get('Engine_Manifest') as $data )
        {
          if( !empty($data['composer'][$type]) )
          {
            $config = $data['composer'][$type];
          }
        }
        if( $config ) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach'.ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if($parent->getType() === 'user'){
            $attachment->search = 0;
            $attachment->save();
          }
          else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }
      
      $viewer = Engine_Api::_()->user()->getViewer();
      $values = $form->getValues();

      // Prepopulated
      if( $toObject instanceof User_Model_User ) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
        // Validate friends
        /*if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
          if( !$viewer->membership()->isMember($recipients) ) {
            return $form->addError('One of the members specified is not in your friends list.');
          }
        }*/
        
      } else if( $toObject instanceof Core_Model_Item_Abstract &&
          method_exists($toObject, 'membership') ) {
        $recipientsUsers = $toObject->membership()->getMembers();
//        $recipients = array();
//        foreach( $recipientsUsers as $recipientsUser ) {
//          $recipients[] = $recipientsUser->getIdentity();
//        }
        $recipients = $toObject;
      }
      // Normal
      else {
        $recipients = preg_split('/[,. ]+/', $values['toValues']);
        // clean the recipients for repeating ids
        // this can happen if recipient is selected and then a friend list is selected
        $recipients = array_unique($recipients);
        // Slice down to 10
        $recipients = array_slice($recipients, 0, $maxRecipients);
        // Get user objects
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
        // Validate friends
       /*( if( 'friends' == Engine_Api::_()->authorization()->getPermission($viewer, 'messages', 'auth') ) {
          foreach( $recipientsUsers as &$recipientUser ) {
            if( !$viewer->membership()->isMember($recipientUser) ) {
              return $form->addError('One of the members specified is not in your friends list.');
            }
          }
        }*/
      }

	  // Get extra message info
	  $info['type'] = $values['type'];
	  $info['from'] = $values['from'];
	  $info['type'] = $values['type'];
	  $info['reason'] = $values['reason'];
	   if($info['from'] == 1)
		{
			$info['sender_email'] = $viewer->email;
			$sender = $viewer;
		}
		else
		{
			$settingTable = Engine_Api::_()->getDbTable('settings', 'core');
			$fromAddress = $settingTable->select()
									->from($settingTable,'value')
									->where('name = ?', 'core.mail.from')
									->query()
									->fetchAll();
			$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
			$select = $permissionsTable->select()
                ->from($permissionsTable,'level_id')
                ->where('type = ?', 'ynbanmem')
                ->where('name = ?', 'action')
                ->query()
                ->fetchAll();
			$usersTable = Engine_Api::_()->getDbtable('users', 'user');
			$exist =  $users = $usersTable->select()
										->from($usersTable,'user_id')
										->where('email = ?', $fromAddress)
										->where('level_id IN (?)', $select)
										->query()
										->fetchAll();
										
			if(count($exist) == 0)
				return $form->getElement('from')->addError("Site admin configured email is not belong to any member that has permission to send the notice message. Please change it to other email or use your own account to send the message.");
			$sender = Engine_Api::_()->getItem('user', $exist[0]['user_id']);
		}
	  if($info['type'] == 2 && $info['reason'] == "")
		return $form->getElement('reason')->addError('Please complete this field - it is required.');
	  
      // Create conversation
	   $conversationTable = Engine_Api::_()->getDbTable('conversations','ynbanmem');
	  $conversation = $conversationTable->send(
        $sender,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment,
		$info
      );
      
	
      // Send notifications
      foreach( $recipientsUsers as $user ) {
        if( $user->getIdentity() == $sender->getIdentity() ) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $sender,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

      // Commit
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
    
    if( $this->getRequest()->getParam('format') == 'smoothbox' ) {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
        'smoothboxClose' => true,
      ));
    } else {
      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
        'redirect' => $conversation->getHref(), //$this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
      ));
    }
    
  }
  
  public function noticeAction()
  {
	// Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main');

        if (count($this->view->navigation) == 1) {
            $this->view->navigation = null;
        }
    
        // Check auth
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!$this->_helper->requireUser()->isValid())
            return;
        if (!$this->_helper->requireAuth()->setAuthParams('ynbanmem', null, 'action')->isValid()) {
            return;
        }
	// Get level are allowed to view notice message
	$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
	$usersTable = Engine_Api::_()->getDbtable('users', 'user');
	$select = $permissionsTable->select()
                ->from($permissionsTable,'level_id')
                ->where('type = ?', 'ynbanmem')
                ->where('name = ?', 'action')
                ->query()
                ->fetchAll();
		
         $users = $usersTable->select()
                ->from($usersTable,'user_id')
                ->where('level_id IN (?)', $select)
                ->query()
                ->fetchAll();        
	$conversationsTable  =	Engine_Api::_()->getDbtable('conversations', 'ynbanmem');
        
    $this->view->paginator = $paginator = $conversationsTable->getAllOutboxPaginator($users);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
	$paginator -> setItemCountPerPage(10);	
   
     
  }

public function deleteNoticeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    $message_ids = $this->view->message_ids = $this->getRequest()->getParam('message_ids');
    $messages = explode(',', $message_ids);

    $place = $this->view->place = $this->getRequest()->getParam('place');
    
    if (!$this->getRequest()->isPost())
      return;
    
    // In smoothbox
    $this->_helper->layout->setLayout('default-simple');

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->deleted_conversation_ids = array();
    
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    try {
      foreach ($messages as $message_id) {
        $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
        //$recipients = Engine_Api::_()->getApi('core', 'messages')->getConversationRecipientsInfo($message_id);
        foreach ($recipients as $r) {
          //if ($viewer_id == $r->user_id) 
          {
            $this->view->deleted_conversation_ids[] = $r->conversation_id;
            $r->inbox_deleted  = true;
            $r->outbox_deleted = true;
            $r->save();
          }
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('The selected messages have been deleted.');
    
    if ($place != 'view') {
      return $this->_forward('success' ,'utility', 'core', array(
        'smoothboxClose' => true,
        'format'=> 'smoothbox',
        'parentRefresh' => true,        
        'messages' => Array($this->view->message)
      ));
    }
    else {
    
      return $this->_forward('success' ,'utility', 'core', array(
        'smoothboxClose' => true,
        'format'=> 'smoothbox',
        'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'inbox')),        
        'messages' => Array($this->view->message)
      ));
    }
  }
  
}
