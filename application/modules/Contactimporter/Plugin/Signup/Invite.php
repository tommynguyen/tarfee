<?php
class Contactimporter_Plugin_Signup_Invite extends Core_Plugin_FormSequence_Abstract
{
	protected $_name = 'invite';
	protected $_formClass = 'Contactimporter_Form_Abstract';
	protected $_script = array(
		'signup/import.tpl',
		'contactimporter'
	);
	protected $_adminScript = array(
		'admin-signup/invite.tpl',
		'user'
	);
	protected $_adminFormClass = 'Contactimporter_Form_Admin_Signup_Invite';
	protected $_skip;

	public function onSubmit(Zend_Controller_Request_Abstract $request)
	{
		if ($request -> getParam("skip") == "skipForm")
		{
			$this -> setActive(false);
			$this -> onSubmitIsValid();
			$this -> getSession() -> skip = true;
			$this -> _skip = true;
			return true;
		}
		//get default level_id
		$table = Engine_Api::_() -> getDbtable('levels', 'authorization');
		$defaultLevelDuplicates = $table -> select() -> from($table) -> where('flag = ?', 'default') -> query() -> fetchAll();
		$default_level = @$defaultLevelDuplicates[0]['level_id'];

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		if (null !== $viewRenderer && $viewRenderer -> view instanceof Zend_View_Interface)
		{
			$this -> view = $viewRenderer -> view;
		}

		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> step = '';

		$task = $request -> getPost('task', '');
		$is_error = 0;

		if ($request -> isPost())
		{
			$values = $request -> getPost();
			$this -> view -> login = $post_login = $request -> getPost('user');
			if ($task == 'manual_invite')
			{
				$this -> getSession() -> manual_invite = $values;
				$this -> onSubmitIsValid();
				parent::onSubmit($request);
				return true;
			}
			elseif ($task == 'get_contacts' || isset($_POST['get_success']))
			{
				$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($default_level, 'contactimporter', 'max');
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

					if (strpos($key, 'oauth_tok3n') !== false)
					{
						$token = $val;
						$_SESSION['socialbridge_session'][$provider]['access_token'] = $token;
						$is_openid = true;

					}
					if (strpos($key, 'oauth_token_secret') !== false)
					{
						$secret_token = $val;
						$_SESSION['socialbridge_session'][$provider]['secret_token'] = $secret_token;
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
				$totalFriends = $totalFriendSearch = $checked = 0;
				if ($provider == 'twitter')
				{
					$obj = Engine_Api::_() -> socialbridge() -> getInstance('twitter');
					$params = $_SESSION['socialbridge_session']['twitter'];
					$contacts = $obj -> getContacts($params);
				}
				if ($provider)
				{
					$getcontact = true;
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
					$this -> view -> provider = $provider;
					$this -> view -> step = 'invite';
					$this -> view -> plugType = "social";
					$this -> view -> contacts = $contacts;
					$this -> view -> totalFriends = $totalFriends;
					$this -> view -> totalFriendSearch = $totalFriendSearch;
					$settings = Engine_Api::_() -> getApi('settings', 'core');
					$this -> view -> default_message = $settings -> getSetting('invite.message');
					$this -> view -> show_photo = 1;
					
					return;
				}

				//Yahoo and Gmail
				if (isset($_POST) && isset($_POST['contact']))
				{
					$contacts = array();
					$aYahooContacts = $_POST['contact'];
					$aYahooContacts = urldecode($aYahooContacts);
					$aYahooContacts = Zend_Json::decode($aYahooContacts);

					if (!$aYahooContacts || count($aYahooContacts) <= 0)
					{
						return $contacts;
					}
					foreach ($aYahooContacts as $key => $aContact)
					{

						$contacts[$aContact['email']] = $aContact['name'];
					}
					// Divide to 2 type: already register recipients & non-registered recipients

					$social_network = array();
					$invite_list = array();
					$invite_list_to_st = "";

					foreach ($contacts as $email => $name)
					{
						if (Engine_Api::_() -> contactimporter() -> getUser($email))
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
						$this -> view -> step = 'add';
						$this -> view -> page = 1;
						$this -> view -> checked = 0;
					}
					else
					{
						$is_openid = false;
						$this -> view -> contacts = $contacts;
						$this -> view -> step = 'invite';
						$settings = Engine_Api::_() -> getApi('settings', 'core');
						$this -> view -> default_message = $settings -> getSetting('invite.message');
						$this -> view -> show_photo = 0;
						$this -> view -> plugType = "email";
						$this -> view -> page = 1;
						$this -> view -> checked = 0;
					}
					$getcontact = true;
					return;
				}
				$table = Engine_Api::_() -> getDbtable('providers', 'Contactimporter');
				$select = $table -> select();
				$select -> where('enable = ?', 1) -> order('order', 'ASC');
				$oi_services = $table -> fetchAll($select);
				$this -> view -> step = 'get_contacts';
				$this -> view -> providers = $providers = $table -> fetchAll($select);
				
				unset($_SESSION['ynfriends_checked']);
			}
			//ADD CONTACT LIST
			if ($task == 'do_add')
			{
				$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($default_level, 'contactimporter', 'max');

				$this -> view -> plugType = $values['plugType'];
				$this -> view -> oi_session_id = $values['oi_session_id'];
				$this -> view -> provider = $values['provider'];
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
				$this -> getSession() -> Contactimporter_add = $selected_contacts;
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
					$this -> view -> page = 1;
					$this -> view -> checked = 0;
					if ($contacts)
					{
						$this -> view -> step = 'invite';
					}
				}
				else
				{
					// FINISHED
					$this -> onSubmitIsValid();
					parent::onSubmit($request);
					return true;
				}
				unset($_SESSION['ynfriends_checked']);
			}

			if ($task == 'do_invite')
			{
				$plugType = $values['plugType'];
				$oi_session_id = $values['oi_session_id'];

				$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($default_level, 'contactimporter', 'max');

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
				$this -> getSession() -> Contactimporter_invite = array(
					"contacts" => $selected_contacts,
					'plugin_type' => $plugType,
					'provider' => $values['provider'],
					'message' => $values['message'],
					'oi_session_id' => $oi_session_id,
				);
				// FINISHED
				unset($_SESSION['ynfriends_checked']);
				$this -> onSubmitIsValid();

				parent::onSubmit($request);
				return true;
			}

			if ($task == 'skip_add')
			{
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
					$this -> view -> step = 'invite';
					$this -> view -> max_invitation = $max_invitation = Engine_Api::_() -> authorization() -> getPermission($default_level, 'contactimporter', 'max');
					$this -> view -> plugType = $values['plugType'];
					$this -> view -> oi_session_id = $values['oi_session_id'];
					$this -> view -> provider = $values['provider'];
					$this -> view -> page = 1;
					$this -> view -> checked = 0;
				}
				else
				{
					$this -> setActive(false);
					$this -> onSubmitIsValid();
					$this -> getSession() -> skip = true;
					$this -> _skip = true;
					return true;
				}
				unset($_SESSION['ynfriends_checked']);
			}

			if ($task == 'skip_invite')
			{
				unset($_SESSION['ynfriends_checked']);
				// FINISHED
				$this -> onSubmitIsValid();
				parent::onSubmit($request);
				return true;
			}
		}
		$this -> getSession() -> active = true;
		$this -> onSubmitNotIsValid();
		return false;

	}

	public function onView()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		if (null !== $viewRenderer && $viewRenderer -> view instanceof Zend_View_Interface)
		{
			$this -> view = $viewRenderer -> view;
		}
		
		//get default level_id
		$table = Engine_Api::_() -> getDbtable('levels', 'authorization');
		$defaultLevelDuplicates = $table -> select() -> from($table) -> where('flag = ?', 'default') -> query() -> fetchAll();
		$default_level = @$defaultLevelDuplicates[0]['level_id'];
		$this -> view -> max_invitation = Engine_Api::_() -> authorization() -> getPermission($default_level, 'contactimporter', 'max');

		// Init settings object
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> default_message = $settings -> getSetting('invite.message');
		$this -> view -> invite_form = $invite_form = new User_Form_Signup_Invite();
		$invite_form -> addElement('Hidden', 'task', array(
			'order' => 6,
			'value' => 'manual_invite'
		));
		$this -> view -> action = $this -> view -> url(array(
			'module' => 'core',
			'controller' => 'signup'
		), 'default', true);

		if (!isset($this -> view -> step))
		{
			$table = Engine_Api::_() -> getDbtable('providers', 'Contactimporter');
			$select = $table -> select();
			$select -> where('enable = ?', 1) -> order('order', 'ASC');
			$oi_services = $table -> fetchAll($select);
			$this -> view -> step = 'get_contacts';
			$this -> view -> providers = $table -> fetchAll($select);
			
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
			
		}
	}

	public function onProcess()
	{
		// In this case, the step was placed before the account step.
	    // Register a hook to this method for onUserCreateAfter
	    if( !$this->_registry->user ) 
	    {
	      // Register temporary hook
	      Engine_Hooks_Dispatcher::getInstance()->addEvent('onUserCreateAfter', array(
	        'callback' => array($this, 'onProcess'),
	      ));
	      return;
	    }
	    $viewer = $this->_registry->user;
		//IF MANUAL INVITE
		if (isset($this -> getSession() -> manual_invite))
		{
			$form = new User_Form_Signup_Invite();
			$data = $this -> getSession() -> manual_invite;
			foreach ($data as $key => $val)
			{
				$el = $form -> getElement($key);
				if ($el)
				{
					$el -> setValue($val);
				}
			}
			if (!$this -> _skip && !$this -> getSession() -> skip)
			{
				if ($form -> isValid($data))
				{
					$form -> sendInvites();
				}
			}
			return;
		}

		// ADD FRIEND IF EXIST
		$add_data = $this -> getSession() -> Contactimporter_add;

		if ($add_data)
		{
			foreach ($add_data as $email => $name)
			{
				$user = Engine_Api::_() -> getApi('core', 'user') -> getUser($email);
				$db = Engine_Api::_() -> getDbtable('membership', 'user') -> getAdapter();
				$db -> beginTransaction();
				try
				{
					// check friendship verification settings
					// add membership if allowed to have unverified friendships
					// else send request
					$user -> membership() -> addMember($viewer) -> setUserApproved($viewer);
					// if one way friendship and verification not required
					if (!$user -> membership() -> isUserApprovalRequired() && !$user -> membership() -> isReciprocal())
					{
						// Add activity
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');
						// Add notification
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow');
						$message = "You are now following this member.";
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
						$message = "You are now friends with this member.";
					}
					// if one way friendship and verification required
					else
					if (!$user -> membership() -> isReciprocal())
					{
						// Add notification
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_follow_request');
						$message = "Your friend request has been sent.";
					}
					// if two way friendship and verification required
					else
					if ($user -> membership() -> isReciprocal())
					{
						// Add notification
						Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $user, 'friend_request');
						$message = "Your friend request has been sent.";
					}
					$this -> view -> status = true;
					$db -> commit();
					$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your friend request has been sent.');
				}
				catch( Exception $e )
				{
					$db -> rollBack();
					$this -> view -> status = false;
					$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('An error has occurred.');
					$this -> view -> exception = $e -> __toString();
				}
			}
		}
		// INVITE FRIEND IF EXIST
		$invite_data = $this -> getSession() -> Contactimporter_invite;
		if ($invite_data)
		{
			// Get the already invited contacts
			$invites_table = Engine_Api::_() -> getDbTable('invites', 'invite');
			$settings = Engine_Api::_() -> getApi('settings', 'core');
			$translate = Zend_Registry::get('Zend_Translate');
			$message = $invite_data['message'];
			$message = trim($message);
			if (is_array($invite_data['contacts']) && !empty($invite_data['contacts']))
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$link = $pageURL. "://" . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('user_id' => $viewer->getIdentity()), 'contactimporter_ref');
				if (isset($invite_data['provider']) && !empty($invite_data['provider']))
				{
					if($invite_data['provider'] == 'twitter')
					{
						$obj = Engine_Api::_() -> socialbridge() -> getInstance('twitter');
						$params = $_SESSION['socialbridge_session']['twitter'];
						$params['list'] = $invite_data['contacts'];
						$params['link'] = $link;
						$params['message'] = $message;
						$params['user_id'] = $viewer -> getIdentity();
						$params['uid'] = $obj -> getOwnerId();
						$obj -> sendInvites($params);
					}
				}
				else
				{
					// Initiate objects to be used below
					$table = Engine_Api::_() -> getDbtable('invites', 'invite');
					// Iterate through each recipient
					//IF PLUGIN IS EMAIL THEN SEND VIA EMAIL
					if ($invite_data['plugin_type'] == 'email')
					{
						$api = Engine_Api::_() -> getApi('core', 'Contactimporter');
						$result_message = $api -> sendInvitationEmail($viewer, $invite_data['contacts'], $message);
					}
				}
			}
			$viewer -> save();
		}

	}

	public function onAdminProcess($form)
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');

		$step_table = Engine_Api::_() -> getDbtable('signup', 'user');
		$step_row = $step_table -> fetchRow($step_table -> select() -> where('class = ?', 'Contactimporter_Plugin_Signup_Invite'));
		$step_row -> enable = $form -> getValue('enable') && ($settings -> getSetting('user.signup.inviteonly') != 1);
		$step_row -> save();
	}

}
