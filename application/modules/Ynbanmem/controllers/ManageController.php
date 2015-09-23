<?php
/**
 */
class Ynbanmem_ManageController extends Core_Controller_Action_Standard
{
	public function sendMessageAction()
	{
		echo 'send-message';die;
	}
	public function banAction()
	{
		echo 'ban';die;
	}
	public function unbanAction()
	{
		echo 'Unban';die;
	}
	public function reSendAction()
	{
		$email = $this->_getParam('email');
		
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !$email ) {
	      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
	    }		
	    
	    $userTable = Engine_Api::_()->getDbtable('users', 'user');
	    $user = $userTable->fetchRow($userTable->select()->where('email = ?', $email));
	    
	    if( !$user ) {
	      $this->view->error = 'That email was not found in our records.';
	      return;
	    }
	    if( $user->verified ) {
	      $this->view->error = 'That email has already been verified. You may now login.';
	      return;
	    }
	    
	    // resend verify email
	    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
	    $verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->user_id)->limit(1));
	    
	    if( !$verifyRow ) {
	      $settings = Engine_Api::_()->getApi('settings', 'core');
	      $verifyRow = $verifyTable->createRow();
	      $verifyRow->user_id = $user->getIdentity();
	      $verifyRow->code = md5($user->email
	          . $user->creation_date
	          . $settings->getSetting('core.secret', 'staticSalt')
	          . (string) rand(1000000, 9999999));
	      $verifyRow->date = $user->creation_date;
	      $verifyRow->save();
	    }
	    
	    $mailParams = array(
	      'host' => $_SERVER['HTTP_HOST'],
	      'email' => $user->email,
	      'date' => time(),
	      'recipient_title' => $user->getTitle(),
	      'recipient_link' => $user->getHref(),
	      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
	      'queue' => false,
	    );
	    
	    $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
	          'action' => 'verify',
	          //'email' => $email,
	          //'verify' => $verifyRow->code
	        ), 'user_signup', true)
	      . '?'
	      . http_build_query(array('email' => $email, 'verify' => $verifyRow->code))
	      ;
	    
	    Engine_Api::_()->getApi('mail', 'core')->sendSystem(
	      $user,
	      'core_verification',
	      $mailParams
	    );
		return $this->_forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
	        'parentRefresh' => true,
	        'format'=> 'smoothbox',
	        'messages' => array('This member has been successfully re-send email.')
	      ));
	}
	public function deleteAction()
	{
		$id = $this->_getParam('id', null);
	    $this->view->user = $user = Engine_Api::_()->getItem('user', $id);
	    $this->view->form = $form = new User_Form_Admin_Manage_Delete();
	    // deleting user
	    //$form->user_id->setValue($id);
	
	    if( $this->getRequest()->isPost() ) {
	      $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
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
			
	        $user->delete();
	        
	        $db->commit();
	      } catch( Exception $e ) {
	        $db->rollBack();
	        throw $e;
	      }
	      
	      return $this->_forward('success', 'utility', 'core', array(
	        'smoothboxClose' => true,
	        'parentRefresh' => true,
	        'format'=> 'smoothbox',
	        'messages' => array('This member has been successfully deleted.')
	      ));
	    }
	}
	
	
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

	public function ipsAction()
	{
		
		$user_id = $this->_getParam('id', null);
		if(!$user_id) 
		{
			$this->view->noview = true;
		}
		else{
			$this->view->user = Engine_Api::_() -> getItem('user', $user_id);
		}
		
		
		
		// Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynbanmem_main');
		$this->view->form = $form = new Ynbanmem_Form_ManageUsers();
		$table = Engine_Api::_()->getDbtable('ips', 'ynbanmem');
	    $select = $table->select();
		
	  
	    // Set up select info
	    $select->order("ip ASC");
	
		if( !empty($user_id )) {
	      $select->where('user_id = ?',$user_id);
	    }	
		
		
		$page = $this->_getParam('page', 1);
		$this->view->paginator = $paginator = Zend_Paginator::factory($select);
    	$this->view->paginator = $paginator->setCurrentPageNumber( $page );
	} 
	public function approveAction()
	{
		$id = $this->_getParam('id', null);
	    $user = Engine_Api::_()->getItem('user', $id);
	 	
	    
      $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
      $db->beginTransaction();

      try {
      	$user->approved = true;
        $user->save();
        
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array('This member has been successfully approved.')
      ));
	   
	}
	
	
	public function multiModifyAction()
    {
    $this->view->form = $form = new User_Form_Admin_Manage_Delete(); 
    if( $this->getRequest()->isPost() ) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key=>$value) {
        if( $key == 'modify_' . $value ) {
          $user = Engine_Api::_()->getItem('user', (int) $value);
          if( $values['submit_button'] == 'delete' ) {
            if( $user->level_id != 1 ) {
            	
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
	
              $user->delete();
            }
          } else if( $values['submit_button'] == 'approve' ) {
            $old_status = $user->enabled;
            $user->enabled = 1;
            $user->approved = 1;
            $user->save();
            
            // ORIGINAL WAY
            // Send a notification that the account was not approved previously
            if( $old_status == 0 ) {
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'user_account_approved', array(
                'host' => $_SERVER['HTTP_HOST'],
                'email' => $user->email,
                'date' => time(),
                'recipient_title' => $user->getTitle(),
                'recipient_link' => $user->getHref(),
                'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
                'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
              ));
              // Send hook to add activity
              Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserEnable', $user);                           
            }
          }
        }
      }
    }

    return $this->_helper->redirector->gotoRoute(array('controller' => 'index', 'action' => 'users'), 'ynbanmem_general');
  } 
	public function init()
	{
		ini_set('display_startup_errors', 1);
		ini_set('display_errors', 1);
		ini_set('error_reporting', -1);
	}
}
