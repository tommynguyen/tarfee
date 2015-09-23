<?php
require_once APPLICATION_PATH . '/application/modules/Contactimporter/Plugin/constants.php';

class Contactimporter_IndexController extends Core_Controller_Action_Standard
{
	public function indexAction()
	{
		
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> _helper -> redirector -> gotoRoute(array('action' => 'import'));
	}

	public function addAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'contactimporter', 'max');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getParams();
			$this -> view -> plugType = $values['plugType'];
			$this -> view -> oi_session_id = $values['oi_session_id'];
			$this -> view -> provider_box = $values['provider_box'];
			if (isset($values['task']) && $values['task'] == 'do_add')
			{
				$selected_contacts = array();
				$aFriendIdSelected = explode(',', $values['friendIds']);
				$aFriendNameSelected = explode(',', $values['friendNames']);
				foreach ($aFriendIdSelected as $key => $val)
				{
					if ($val)
					{
						$selected_contacts[$val] = $aFriendNameSelected[$key];
					}
				}
				if (count($selected_contacts) == 0)
					$ers['contacts'] = Zend_Registry::get('Zend_Translate') -> _("You haven't selected any contacts to add connection !");
				// ADD CONNECTION HERE
				// Process
				foreach ($selected_contacts as $email => $name)
				{
					$user = $this -> _helper -> api() -> user() -> getUser($email);
					$db = Engine_Api::_() -> getDbtable('membership', 'user') -> getAdapter();
					$db -> beginTransaction();

					try
					{
						// check friendship verification settings
						// add membership if allowed to have unverified friendships

						$user -> membership() -> addMember($viewer) -> setUserApproved($viewer);
						// if one way friendship and verification not required
						if (!$user -> membership() -> isUserApprovalRequired() && !$user -> membership() -> isReciprocal())
						{
							// Add activity
							Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');
							// Add notification
							Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow');
							$message = Zend_Registry::get('Zend_Translate') -> _("You are now following this member.");
						}
						// if two way friendship and verification not required
						else
						if (!$user -> membership() -> isUserApprovalRequired() && $user -> membership() -> isReciprocal())
						{
							// Add activity
							Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
							Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

							// Add notification
							Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_accepted');
							$message = Zend_Registry::get('Zend_Translate') -> _("You are now friends with this member.");
						}

						// if one way friendship and verification required
						else
						if (!$user -> membership() -> isReciprocal())
						{
							// Add notification
							Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow_request');
							$message = Zend_Registry::get('Zend_Translate') -> _("Your friend request has been sent.");
						}
						// if two way friendship and verification required
						else
						if ($user -> membership() -> isReciprocal())
						{
							// Add notification
							Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_request');
							$message = Zend_Registry::get('Zend_Translate') -> _("Your friend request has been sent.");
						}
						$db -> commit();
					}
					catch( Exception $e )
					{
						$db -> rollBack();
					}
				}

			}

			if (isset($values['invite_list']) && $values['invite_list'])
			{
				$contacts = array();
				$contacts_invite = explode(';', $values['invite_list']);
				foreach ($contacts_invite as $contact)
				{
					$tmp = explode("~~~~", $contact);
					if (isset($tmp[1]))
						$contacts[$tmp[0]] = $tmp[1];
				}
				$this -> view -> contacts = $contacts;
				$settings = Engine_Api::_() -> getApi('settings', 'core');
				$this -> view -> default_message = $settings -> getSetting('invite.message');
			}
		}
	}

	public function saveInvitations($invitationArr)
	{
		if (!count($invitationArr['list']))
			return;
		
		$invitationTbl = Engine_Api::_() -> getDbTable('invitations', 'contactimporter');
		try
		{
			foreach ($invitationArr['list'] as $key => $value) 
			{
				$mailId = 0;
				if ( isset($invitationArr['mail_ids']) 
					&& isset($invitationArr['mail_ids'][$key])
					&& ($invitationArr['mail_ids'][$key]) )
				{
					$mailId = $invitationArr['mail_ids'][$key];	
				}
					
				$invitation = $invitationTbl->createRow();
				$invitation->setFromArray(array(
					'inviter_id' => $invitationArr['inviter_id'],
					'service' => $invitationArr['service'],
					'type' => $invitationArr['type'],
					'mail_id' => $mailId,
					'message' => $invitationArr['message'],
					'creation_date' => date('Y-m-d H:i:s'),
					'uid' => (($invitationArr['type'] == 'social')) ? $key : "",
					'uname' => (($invitationArr['type'] == 'social')) ? $value : "",
					'email' => (($invitationArr['type'] == 'email')) ? $key : "",
				));
				$invitation->save();
			}	
		}
		catch(Exception $e)
		{
			echo $e->getMessage(); exit;
		}
	}

	public function inviteAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		if ($this -> getRequest() -> isPost())
		{
			// Get the already invited contacts
			$invites_table = Engine_Api::_() -> getDbTable('invites', 'invite');
			
			$values = $this -> getRequest() -> getPost();
			$plugType = $values['plugType'];
			$oi_session_id = $values['oi_session_id'];
			if (isset($values['task']) && $values['task'] == 'do_add')
			{
				$viewer = Engine_Api::_() -> user() -> getViewer();
				$settings = Engine_Api::_() -> getApi('settings', 'core');
				$translate = Zend_Registry::get('Zend_Translate');
				$message = $values['message'];
				$message = trim($message);
				$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'contactimporter', 'max');
				$selected_contacts = array();
				$aFriendIdSelected = explode(',', $values['friendIds']);
				$aFriendNameSelected = explode(',', $values['friendNames']);
				foreach ($aFriendIdSelected as $key => $val)
				{
					if ($val)
					{
						$selected_contacts[$val] = $aFriendNameSelected[$key];
						if (--$max_invitation < 1)
							break;
					}
				}
				if (count($selected_contacts) == 0)
					$ers['contacts'] = Zend_Registry::get('Zend_Translate') -> _("You haven't selected any contacts to invite !");
				// ADD INVITE HERE
				if (is_array($selected_contacts) && !empty($selected_contacts))
				{
					$pageURL = 'http';
					if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
					{
						$pageURL .= "s";
					}
					$link = $pageURL. "://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $viewer->getIdentity()), 'contactimporter_ref');
					// Init invitation array
					$invitationArr = array(
						'inviter_id' => $viewer->getIdentity(),
						'list' => $selected_contacts,
					);
					
					if (isset($values['openId']) && !empty($values['openId']))
					{
						switch($values['openId'])
						{
							case 'twitter' :
								$obj = Engine_Api::_() -> socialbridge() -> getInstance('twitter');
								$params = $_SESSION['socialbridge_session']['twitter'];
								$params['list'] = $selected_contacts;
								$params['link'] = $link;
								$params['message'] = $message;
								$params['user_id'] = $viewer -> getIdentity();
								$params['uid'] = $obj -> getOwnerId();
								echo "<br/>";
								$obj -> sendInvites($params);
								$invitationArr['service'] = 'twitter';
								$invitationArr['type'] = 'social';
								break;
							default :
								break;
						}
					}
					else
					{
						// Initiate objects to be used below
						$table = Engine_Api::_() -> getDbtable('invites', 'invite');
						//IF PLUGIN IS EMAIL THEN SEND VIA EMAIL
						
						$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
						$mailIds = $api -> sendInvitationEmail($viewer, $selected_contacts, $message);
						$invitationArr['mail_ids'] = $mailIds;
						$invitationArr['service'] = 'email';
						$invitationArr['type'] = 'email';
					}
					$invitationArr['message'] = $message;
					$this->saveInvitations($invitationArr);
				} // END INVITE
				unset($_SESSION['ynfriends_checked']);
			}
			elseif (isset($values['task']) && $values['task'] == 'skip')
			{
				unset($_SESSION['ynfriends_checked']);
				$this -> _helper -> redirector -> gotoRoute(array('action' => 'import'));
			}
		} // END CHECK POST
	}

	public function getInvitedUids($provider="facebook")
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$invitationTbl = Engine_Api::_()->getDbTable("invitations", "contactimporter");
		$select = $invitationTbl->select()
			->where("`service` = ?", $provider)
			->where("`inviter_id` = ?",  $viewer->getIdentity())
			->where("`inviter_deleted` = 0");
			
		$invitations = $invitationTbl->fetchAll($select);
		$uids = array();
		foreach($invitations as $invitation)
		{
			if($invitation->uid)
			{
				$uids[] = $invitation->uid;
			}
		}
		return $uids;
	}

	public function getInvitedEmails()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$invitationTbl = Engine_Api::_()->getDbTable("invitations", "contactimporter");
		$select = $invitationTbl->select()
			->where("`service` = ?", "email")
			->where("`inviter_id` = ?",  $viewer->getIdentity());
			
		$invitations = $invitationTbl->fetchAll($select);
		$emails = array();
		foreach($invitations as $invitation)
		{
			if($invitation->email)
			{
				$emails[] = $invitation->email;
			}
		}
		return $emails;
	}

	public function importAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
			
		// Render
		$this -> _helper -> content
			-> setEnabled();

		$table = $this -> _helper -> api() -> getDbtable('providers', 'Contactimporter');
		$select = $table -> select() -> where('enable = ?', 1);
		$select -> order('order', 'ASC');
		$this -> view -> providers = $providers = $table -> fetchAll($select);
		
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> default_message = $settings -> getSetting('invite.message');
		
		// get facebook API
		if (Engine_Api::_() -> hasModuleBootstrap('socialbridge'))
		{
			$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
			$select = $apiSetting->select()->where('api_name = ?', 'facebook');
			$provider = $apiSetting->fetchRow($select);
			if($provider)
			{
				$api_params = unserialize($provider -> api_params);
				$this -> view -> facebookAPI = $api_params['key'];
			}
		}

		/* get invite link */
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$link = $pageURL. "://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $viewer->getIdentity()), 'contactimporter_ref');
		$this -> view -> invite_link = $link;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'contactimporter', 'max');
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');

		/******Get contact from openId*****/
		$cur_url = $_SERVER['REQUEST_URI'];
		parse_str($cur_url, $params);
		$contacts = array();
		$index = 0;
		$id = "";
		$name = "";
		$pic = "";
		$getcontact = false;
		$token = "";
		$secret_token = "";
		$provider = "";
		$is_openid = false;
		$page = 1;
		$checked = 0;

		foreach ($params as $key => $val)
		{
			if (strpos($key, '?user') !== false)
			{
				$provider = 'twitter';
				$_SESSION['socialbridge_session']['provider'] = $provider;
				$_SESSION['socialbridge_session'][$provider]['user_id'] = $val;
				$is_openid = true;
			}
			if (strpos($key, 'oauth_token_secret') !== false)
			{
				$secret_token = $val;
				$_SESSION['socialbridge_session'][$provider]['secret_token'] = $secret_token;
				$is_openid = true;
			}
			if (strpos($key, 'oauth_tok3n') !== false)
			{
				$token = $val;
				$_SESSION['socialbridge_session'][$provider]['access_token'] = $token;
				$is_openid = true;

			}
			$pic = null;
			if (strpos($key, 'id_') !== false)
			{
				$id = $val;
			}
			if (strpos($key, 'name_') !== false)
			{
				$name = $val;
			}
			if (strpos($key, 'pic_') !== false)
			{
				$pic = $val;
				$index = 2;
			}
			if ($index >= 2)
			{
				$contacts[$id] = array(
					'id' => $id,
					'name' => $name,
					'pic' => $pic
				);

			}
		}

		$req = $this -> getRequest();
		$callbackUrl = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'contactimporter');
		$totalFriends = $totalFriendSearch = 0;
		if ($provider == 'twitter')
		{
			$obj = Engine_Api::_() -> socialbridge() -> getInstance('twitter');
			$params = $_SESSION['socialbridge_session']['twitter'];
			$params['invited_uids'] = $this->getInvitedUids('twitter');
			$contacts = $obj -> getContacts($params);
		}
		if ($provider)
		{
			$values = array(
				'user_id' => $viewer -> getIdentity(),
				'uid' => $obj -> getOwnerId(),
				'service' => $provider,
				'date' => date('Y-m-d')
			);
			$total_invited = $obj -> getTotalInviteOfDay($values);
			$this -> view -> total_invited = $total_invited;
			$apiSetting = Engine_Api::_() -> getDbtable('apisettings', 'socialbridge');
			$select = $apiSetting -> select() -> where('api_name = ?', $provider);
			$apisettings = $apiSetting -> fetchRow($select);
			if ($apisettings)
			{
				$api_params = unserialize($apisettings -> api_params);
				if ($api_params['max_invite_day'])
				{
					$this -> view -> max_invite = $max_invite = $api_params['max_invite_day'];
				}
			}
			
			if($_REQUEST['page_id'])
			{
				$page = $_REQUEST['page_id'];
			}

			if(!isset($_SESSION['ynfriends_checked']))
			{
				$_SESSION['ynfriends_checked']['page_friendIds'] ='';
				$_SESSION['ynfriends_checked']['page_friendNames'] ='';
			}
			// check total checked
			$arr_Friends = explode(',', $_SESSION['ynfriends_checked']['page_friendIds']);
			if(isset($_REQUEST['page_friendIds']) && $_REQUEST['page_friendIds'])
			{
				$arr_FriendNames = explode(',', $_REQUEST['page_friendNames']);
				foreach (explode(',', $_REQUEST['page_friendIds']) as $key => $value) 
				{
					if($value && !in_array($value, $arr_Friends))
					{
						$_SESSION['ynfriends_checked']['page_friendIds'] .= $value.",";
						$_SESSION['ynfriends_checked']['page_friendNames'] .= $arr_FriendNames[$key].',';
					}
				}
			}
			$checked = count(explode(',', $_SESSION['ynfriends_checked']['page_friendIds']));
			if($checked)
			{
				$checked = $checked - 1;
			}
			$this -> view -> friendIds = $_SESSION['ynfriends_checked']['page_friendIds'];
			$this -> view -> friendNames = $_SESSION['ynfriends_checked']['page_friendNames'];
			$this -> view -> page = $page;
			$this -> view -> checked = $checked;
			$this -> view -> openId = $provider;
			$this -> view -> step = 'get_invite';
			$this -> view -> contacts = $contacts;
			$this -> view -> totalFriends = $totalFriends;
			$this -> view -> totalFriendSearch = $totalFriendSearch;
			
			$this -> view -> show_photo = 1;
			$getcontact = true;
		}

		//Yahoo and Gmail
		if (isset($_POST) && isset($_POST['contact']))
		{
			//get all user email list
			$membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
			$membershipName = $membershipTable -> info('name');
			$select = $membershipTable -> select() -> from($membershipTable);
			$select -> where("{$membershipName}.user_id = ?", $viewer -> user_id) -> where('active = ?', 1);
			// Get stuff
			$friends = $membershipTable -> fetchAll($select);
			$ids = array();
			foreach ($friends as $friend)
			{
				$ids[] = $friend -> resource_id;
			}
			$user_email = array();
			$ignore_list = array();

			foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $user)
			{
				if ($viewer -> user_id != $user -> user_id)
				{
					//check is not friend
					if (!$viewer -> membership() -> getRow($user))
					{
						$user_email[] = $user -> email;
						continue;
					};
				}
				$ignore_list[] = $user -> email;
			}

			$contacts = array();
			$aYahooContacts = $_POST['contact'];
			$aYahooContacts = urldecode($aYahooContacts);
			$aYahooContacts = Zend_Json::decode($aYahooContacts);

			if (!$aYahooContacts || count($aYahooContacts) <= 0)
			{
				return $contacts;
			}
			
			$invitedEmails = $this->getInvitedEmails();
			
			foreach ($aYahooContacts as $key => $aContact)
			{
				if (in_array($aContact['email'], $invitedEmails))
				{
					continue;
				}
				$contacts[$aContact['email']] = $aContact['name'];
			}
			// Divide to 2 type: already register recipients & non-registered recipients

			$social_network = array();
			$invite_list = array();
			$invite_list_to_st = "";

			foreach ($contacts as $email => $name)
			{
				if (in_array($email, $user_email) && $email)
				{
					$user = Engine_Api::_() -> contactimporter() -> getUser($email);
					$social_network[$email] = array(
						'name' => $user -> getTitle(),
						'pic' => $user -> getPhotoUrl('thumb.icon')
					);
				}
				elseif (!in_array($email, $ignore_list))
				{
					$invite_list[$email] = $name;
					if (is_array($name))
					{
						$invite_list_to_st .= ";{$email}~~~~{$name['name']}";
					}
					else
					{
						$invite_list_to_st .= ";{$email}~~~~{$name}";
					}
				}
			}
			if ($social_network)
			{
				$this -> view -> contacts = $social_network;
				$this -> view -> invite_list = substr($invite_list_to_st, 1);
				$this -> view -> step = 'add_contact';
				$this -> view -> page = $page;
				$this -> view -> checked = $checked;
			}
			else
			{
				$is_openid = false;
				$this -> view -> contacts = $contacts;
				$this -> view -> step = 'get_invite';
				$this -> view -> show_photo = 0;
				$this -> view -> plugType = "email";
				$this -> view -> page = $page;
				$this -> view -> checked = $checked;
			}
			$getcontact = true;
			return;
		}
		if(!$getcontact)
			unset($_SESSION['ynfriends_checked']);
	}

	public function pendingAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		//$this->setupNavigation();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');

		$item_per_page = 10;
		$current_page = $this -> _getParam('page', 1);

		// Show list of pending invitations
		$invites_table = Engine_Api::_() -> getDbTable('invites', 'invite');
		// Duplicate invites_table for easy editting part of functions
		$select = $invites_table -> select() -> where('user_id = ? ', $viewer -> getIdentity()) -> order(array('timestamp DESC'));

		// Make paginator

		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($item_per_page);
		$this -> view -> paginator = $paginator;

		$paginator -> setCurrentPageNumber($current_page);
	}

	/*
	 * RESEND EMAIL FROM QUEUE EMAIL LIST
	 */
	public function inviteresendAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Get selected contacts
		$invite_id = intval($this -> _getParam('id'));

		$table = Engine_Api::_() -> getDbtable('invites', 'invite');

		$select = $table -> select() -> where('id = ?', $invite_id) -> where('user_id = ?', $viewer -> getIdentity());

		$invitation = $table -> fetchRow($select);

		if (empty($invitation))
		{
			return;
		}

		$result_message = $api -> resendInvitationEmail($viewer, $invitation -> recipient, $invitation -> message, '');
	}

	/*
	 * DELETE EMAIL FROM QUEUE EMAIL LIST
	 */
	public function invitedeleteAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$invites_table = Engine_Api::_() -> getDbtable('invites', 'invite');
		$invite_id = $this -> _getParam('id');

		// UPDATE INVITATIONS TABLE
		$invitationTable = Engine_Api::_() -> getDbtable('invitations', 'contactimporter');
		$select = $invites_table->select()->where("id = ?", $invite_id);
		$invite = $invites_table->fetchRow($select);
		$recipient =  $invite->recipient;
		$invitationTable->update(array('inviter_deleted' => 1) ,"email = '".$recipient."'");

		// REMOVE QUEUE MAIL
		$invites_table -> delete(array(
			'id = ?' => $invite_id,
			'user_id = ?' => $viewer -> getIdentity()
		));

	}

	public function uploadAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> plugType = 'email';
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}

		if ($this -> getRequest() -> isPost())
		{
			$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($viewer -> level_id, 'contactimporter', 'max');
			if (!isset($err))
			{
				// if no error
				$api = Engine_Api::_() -> getApi('core', 'Contactimporter');

				$import_result = $api -> uploadContactFile(Engine_Api::_() -> user() -> getViewer());

				if ($import_result['is_error'] != 0)
				{
					$this -> view -> ers = $import_result['error_message'];
				}
				else
				{
					$uploadedContacts = $import_result['contacts'];
					$invitedEmails = $this->getInvitedEmails();
					$contacts = array();
					foreach ($uploadedContacts as $email => $name)
					{
						if (in_array($email, $invitedEmails))
						{
							continue;
						}
						$contacts[$email] = $name;
						
					}

					//get all user email list
					$membershipTable = Engine_Api::_() -> getDbtable('membership', 'user');
					$membershipName = $membershipTable -> info('name');
					$select = $membershipTable -> select() -> from($membershipTable);
					$select -> where("{$membershipName}.user_id = ?", $viewer -> user_id) -> where('active = ?', 1);
					// Get stuff
					$friends = $membershipTable -> fetchAll($select);
					$ids = array();
					foreach ($friends as $friend)
					{
						$ids[] = $friend -> resource_id;
					}
					$user_email = array();
					$ignore_list = array();
		
					foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $user)
					{
						if ($viewer -> user_id != $user -> user_id)
							if (!$viewer -> membership() -> getRow($user))
							{
								$user_email[] = $user -> email;
								continue;
							}
						;
						$ignore_list[] = $user -> email;
					}
					// Divide to 2 type: already register recipients & non-registered recipients

					$social_network = array();
					$invite_list = array();
					$invite_list_to_st = "";
					foreach ($contacts as $email => $name)
					{
						
						if (in_array($email, $user_email))
						{
							$user = $this -> _helper -> api() -> user() -> getUser($email);
							$social_network[$email] = array(
								'name' => $user -> getTitle(),
								'pic' => $user -> getPhotoUrl('thumb.icon')
							);
						}
						elseif (!in_array($email, $ignore_list))
						{
							$invite_list[$email] = $name;
							$invite_list_to_st .= ";{$email}~~~~{$name}";
						}
					}
					if ($social_network)
					{
						$this -> view -> contacts = $social_network;
						$this -> view -> invite_list = substr($invite_list_to_st, 1);
						$this -> view -> step = 'add_contact';
					}
					elseif ($invite_list)
					{
						$this -> view -> step = 'get_invite';
						$this -> view -> contacts = $invite_list;
						$settings = Engine_Api::_() -> getApi('settings', 'core');
						$this -> view -> default_message = $settings -> getSetting('invite.message');
					}
					$this -> view -> page = 1;
					$this -> view -> checked = 0;
				}
			}
		}
	}

	public function ajaxAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if ($this -> getRequest() -> isGet())
		{
			$values = $this -> getRequest() -> getParam('hide');

			if ($values == true)
			{
				$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
				$result = $api -> setEnableHomepageInvite($viewer);

				if (!$result)
				{
					echo Zend_Registry::get('Zend_Translate') -> _("Can't update database");
				}
			}
		}
		else
		{
			echo Zend_Registry::get('Zend_Translate') -> _("Invalid request!");
		}
	}

	public function popupAction()
	{
		$viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> view -> provider = $provider = $this -> getRequest() -> getParam('provider');
		$this -> view -> type = $type = $this -> getRequest() -> getParam('type');
		$signup = $this -> getRequest() -> getParam('signup');
		$notFromLogin = array(
			'facebook',
			'gmail',
			'yahoo',
			'twitter',
			'hotmail'
		);
		$this -> view -> checkFromLogin = $checkFromLogin = in_array($provider, $notFromLogin);

		$req = $this -> getRequest();
		if ($signup)
			$callbackUrl = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'user_signup');
		else
			$callbackUrl = $req -> getScheme() . '://' . $req -> getHttpHost() . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'contactimporter');

		if ($checkFromLogin)
		{
			$url = "";
			switch ($provider)
			{
				case'twitter' :
					if (!Engine_Api::_() -> contactimporter() -> checkSocialBridgePlugin())
					{
						echo $this -> view -> translate("Please install or enable Social Bridge plugin!");
						exit ;
					}
					$obj = Engine_Api::_() -> socialbridge() -> getInstance('twitter');
					$url = $obj -> getConnectUrl() . "&" . http_build_query(array('callbackUrl' => $callbackUrl));
					break;
					
				case 'hotmail' :
                    $url = "http://tarfee.com/test/openid/contact/index.php?service=live&login=1&" . http_build_query(array('callbackUrl' => $callbackUrl));
                    break;

                default :
                    $url = "http://tarfee.com/test/openid/contact/index.php?service=".$provider."&" . http_build_query(array('callbackUrl' => $callbackUrl));
                    break;
			}
			$this -> view -> url = $url;
		}
		else
		{
			$this -> view -> form = $form = new Contactimporter_Form_Login();
			if ($this -> getRequest() -> isPost())
			{
				$_SESSION['contact_login'] = $this -> getRequest() -> getPost();
				$_SESSION['contact_login']['url'] = $callbackUrl;
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'login_openid'),
					'format' => 'smoothbox',
					'messages' => array($this -> view -> translate("Sending request!"))
				));
			}
		}
	}

	public function loginOpenidAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> view -> info = $_SESSION['contact_login'];
		unset($_SESSION['contact_login']);
	}
	
	public function queueEmailAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		
		$this -> _helper -> content -> setEnabled();
		
		$mailTbl = Engine_Api::_()->getDbTable("mail", "core");
		$mailTblName = $mailTbl->info("name");
		$select = $mailTbl->select()->from($mailTblName, array('mail_id'));
		$mailIds = array();
		foreach ($mailTbl->fetchAll($select) as $mail)
		{
			$mailIds[] = $mail->mail_id;
		}							
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');

		// Show list of queue emails
		$invitations_table = Engine_Api::_() -> getDbTable('invitations', 'contactimporter');
		$select = $invitations_table -> select() 
			-> where('inviter_id = ? ', $viewer -> getIdentity()) 
			-> where('type = ? ', 'email')
			-> where('mail_id > 0');
		if(count($mailIds))
		{
			$select-> where('mail_id IN (?)', $mailIds);
		}
		else 
		{
			$select->where(0);	
		}

		// Make paginator
		$item_per_page = 10;
		$current_page = $this -> _getParam('page', 1);
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($item_per_page);
		$this -> view -> paginator = $paginator;

		$paginator -> setCurrentPageNumber($current_page);
	}
	
	public function queueMessageAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$this -> _helper -> content -> setEnabled();
		
		// Building SELECT query
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$queuesTable = Engine_Api::_() -> getDbtable('queues', 'socialbridge');
		$select = $queuesTable -> select() 
			-> where('user_id = ? ', $viewer -> getIdentity())
			-> where('type = ?', "sendInvite")
			-> order(array('queue_id ASC'));
		
		$result = array();
		$queues = $queuesTable->fetchAll($select);
		
		foreach($queues as $queue)
		{
			$exParams = $queue->extra_params;
			$exParams = unserialize($exParams);
			if (count($exParams['list']))
			{
				foreach ($exParams['list'] as $k => $v)
				$result[] = array(
					'uid' => $k,
					'uname' => $v,
					'service' => $queue->service,
					'queue_id' => $queue->queue_id,
				);
			}
		}
		
		// Load paginator object
		$item_per_page = 10;
		$current_page = $this -> _getParam('page', 1);
		$paginator = Zend_Paginator::factory($result);
		$paginator -> setItemCountPerPage($item_per_page);
		$paginator -> setCurrentPageNumber($current_page);
		
		$this -> view -> paginator = $paginator;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');
	}
	
	public function queuedeleteAction()
	{ 
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$queuesTable = Engine_Api::_() -> getDbtable('queues', 'socialbridge');
		$id = $this -> _getParam('id');
		
		$args = explode("_", $id);
		if (count($args) != 2)
		{
			echo Zend_Json::encode(array(
					"error"=> 1,
					"error_message" => Zend_Registry::get("Zend_Translate")->_("Can not delete this queue message!")
			));
			exit;
		}
		
		$queueId = $args[0];
		$uid = $args[1];
		$queuesTable = Engine_Api::_() -> getDbtable('queues', 'socialbridge');
		$select = $queuesTable->select()->where("queue_id = ? ", $queueId)->limit(1);
		$queue = $queuesTable->fetchRow($select);
		$service = $queue->service;
		if (is_null($queue))
		{
			echo Zend_Json::encode(array(
					"error"=> 2,
					"error_message" => Zend_Registry::get("Zend_Translate")->_("Can not delete this queue message!")
			));
			exit;
		}
		
		$extra_params = unserialize($queue->extra_params);
		if (isset($extra_params['list'][$uid]))
		{
			unset($extra_params['list'][$uid]);
		}
		
		if (count($extra_params['list']))
		{
			$queue->extra_params = serialize($extra_params);
			$queue->save();
		}
		else
		{
			$queue->delete();
		}
		
		// UPDATE INVITATIONS TABLE
		$invitationTable = Engine_Api::_() -> getDbtable('invitations', 'contactimporter');
		$invitationTable->update(array('inviter_deleted' => 1) ,"`uid` = '$uid' AND `service` = '$service'");
		
		echo Zend_Json::encode(array(
					"error"=> 0,
					"message" => Zend_Registry::get("Zend_Translate")->_("Deleted messages successfully!")
		));
		exit;
	}
	
	public function refAction()
	{
		$inviterId = $this->_getParam("user_id");
		if ( is_numeric($inviterId) && ($inviterId > 0) )
		{
			setcookie(INVITER_COOKIE_NAME, $inviterId, time() + 604800, "/");
		}
		
		return $this->_helper->redirector->gotoRoute(array(), 'user_signup');
	}
	
	public function pendingInvitationAction()
	{
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$this -> _helper -> content -> setEnabled();
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		// GET QUEUE OF EMAIL ID
		$mailTbl = Engine_Api::_()->getDbTable("mail", "core");
		$mailTblName = $mailTbl->info("name");
		$select = $mailTbl->select()->from($mailTblName, array('mail_id'));
		$mailIds = array();
		foreach ($mailTbl->fetchAll($select) as $mail)
		{
			$mailIds[] = $mail->mail_id;
		}
		
		// GET MESSAGE QUEUE
		$queuesTable = Engine_Api::_() -> getDbtable('queues', 'socialbridge');
		$select = $queuesTable -> select() 
			-> where('user_id = ? ', $viewer -> getIdentity())
			-> where('type = ?', "sendInvite");
		
		$queues = $queuesTable->fetchAll($select);
		$facebook = array(); $twitter = array();	
		foreach($queues as $queue)
		{
			$exParams = $queue->extra_params;
			$exParams = unserialize($exParams);
			if (count($exParams['list']))
			{
				foreach ($exParams['list'] as $k => $v)
				if ($queue->service == 'twitter')
				{
					$twitter[] = $k;
				}
			}
		}
		
		// Building SELECT query
		$invitationTbl = Engine_Api::_() -> getDbtable('invitations', 'contactimporter');
		$select = $invitationTbl -> select() 
			-> where('inviter_id = ? ', $viewer -> getIdentity())
			-> where('status = ? ', 0)
			-> where('inviter_deleted = ? ', 0)
			-> order(array('invitation_id ASC'));
		
		if (count($mailIds))
		{
			$select->where("`mail_id` NOT IN (?)", $mailIds);
		}
		
		if (count($facebook))
		{
			$select->where("(`service` != 'facebook') OR (`uid` NOT in (?))", $facebook);
		}
		
		if (count($twitter))
		{
			$select->where("(`service` != 'twitter') OR (`uid` NOT in (?))", $twitter);
		}
		
		$item_per_page = 10;
		$current_page = $this -> _getParam('page', 1);
		
		$paginator = Zend_Paginator::factory($select);
		$paginator -> setItemCountPerPage($item_per_page);
		$paginator -> setCurrentPageNumber($current_page);
		
		$this -> view -> paginator = $paginator;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('contactimporter_main');
	}
	
	/*
	 * DELETE MESSAGES FROM PENDING INVITATION LIST
	 */
	public function invitationdeleteAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		$invitationTable = Engine_Api::_() -> getDbtable('invitations', 'contactimporter');
		$mailTable = Engine_Api::_() -> getDbtable('mail', 'core');
		
		$invitationId  = $this -> _getParam('id');

		if (strpos($invitationId, ",") !== false) // ONLY on page PENDING INVITATIONS	
		{
			$invitationTable->update(array('inviter_deleted' => 1) ,'invitation_id IN('.$invitationId.')');
			// no emails in queue to delete
		} 
		
		else // on page PENDING INVITATIONS and page INVITATIONS
		{
			$invitation = $invitationTable->find($invitationId)->current();
			$invitation->inviter_deleted = 1;
			$invitation->save();
			
			if ($invitation->mail_id)
			{
				$where = $mailTable->getAdapter()->quoteInto('mail_id = ?', $invitation->mail_id);
				$mailTable->delete($where);
			}
		}
		
		echo Zend_Json::encode(array(
					"error"=> 0,
					"message" => Zend_Registry::get("Zend_Translate")->_("Deleted messages successfully!")
		));
		exit;
	}
	
	/*
	 * SEND MESSAGES FROM PENDING INVITATION LIST - 
	 */
	public function invitationsendAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		
		// Authentication
		if (!$this -> _helper -> requireUser -> isValid())
		{
			return;
		}
		
		$invitationTable = Engine_Api::_() -> getDbtable('invitations', 'contactimporter');
		$invitationId = $this -> _getParam('id');
		$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (strpos($invitationId, ",") === FALSE)
		{
			$invitation = $invitationTable->find($invitationId)->current();
			if ($invitation->type == 'email')
			{
				$result_message = $api -> resendInvitationEmail($viewer, $invitation -> email, $invitation -> message, '');	
			}	
		}
		else 
		{
			$invitationIds = explode(",", $invitationId);
			$select = $invitationTable->select()->where("`invitation_id` IN (?)", $invitationIds);
			//echo $select; exit;
			$invitations = $invitationTable->fetchAll($select);
			foreach($invitations as $invitation)
			{
				if ($invitation->type == 'email')
				{
					$result_message = $api -> resendInvitationEmail($viewer, $invitation -> email, $invitation -> message, '');	
				}
			}
		}		
		
		echo Zend_Json::encode(array(
					"error"=> 0,
					"message" => Zend_Registry::get("Zend_Translate")->_("Resent invitation successfully!")
		));
		exit;
	}
	
	public function fbCanvasAction()
	{
		$this->_helper->layout->setLayout('default-simple');
		$params = $this -> _getAllParams();
		$requestIds = empty($params['request_ids']) ? array() : explode(',', $params['request_ids']);
		
		$from = array();
		$obj = Engine_Api::_()->socialbridge()->getInstance("facebook");
		$user_id = 0;
		foreach ( $requestIds as $rid )
		{
		    $request = $obj -> getUserInfo(array('uid' => $rid));
		    if ($request)
		    {
				$from[$request['from']['id']] = $request['from'];
				$this -> view -> message = $message = $request['message'];
				$user_id = (!empty($request['data']))?$request['data']:0;
		    }
		}
		
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$link = $pageURL. "://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $user_id), 'contactimporter_ref');
		$this -> view -> link_invite = $link;
		
		$from = array_reverse($from);
		$site_name = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this-> view -> translate('_SITE_TITLE'));
		
		switch ( count($from) )
		{
		    case 1:
			$user = reset($from);
			$this -> view -> content = $this -> view -> translate("FACEBOOK_CANVAS_PAGE_1", $user['name'], $site_name);
			break;
	
		    case 2:
			$user1 = reset($from);
			$user2 = next($from);
			$this -> view -> content = $this -> view -> translate("FACEBOOK_CANVAS_PAGE_2", $user1['name'], $user2['name'], $site_name);
			break;
	
		    default:
			$user = reset($from);
			$this -> view -> content = $this -> view -> translate("FACEBOOK_CANVAS_PAGE_X", $user['name'], count($from) - 1, $site_name);
		}
	}
	public function fbSaveInvitationsAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$ids = $this -> _getParam('ids', array());
		$selected_contacts = array();
		$obj = Engine_Api::_()->socialbridge()->getInstance("facebook");
		if(!$ids || !$viewer->getIdentity())
		{
			return;
		}
		foreach($ids as $id)
		{
			$user = $obj -> getUserInfo(array('uid' => $id));
			$selected_contacts[$id] = $user['name'];
		}
		
		$invitationArr = array(
				'inviter_id' => $viewer->getIdentity(),
				'list' => $selected_contacts,
			);
		$default_message = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('invite.message');
		$invitationArr['message'] = $default_message;
		$invitationArr['service'] = 'facebook';
		$invitationArr['type'] = 'social';
		$this -> saveInvitations($invitationArr);
	}
	public function fbInviteSuccessfullAction()
	{
		$refresh = $this -> _getParam('refresh', false);
		if($refresh)
			$refresh = 10;
		$total_send = $this -> _getParam('total_send', 0);
		$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRefresh' => $refresh,
					'format' => 'smoothbox',
					'messages' => array($this -> view -> translate('You have invited %s members', $total_send))
				));
	}
}
