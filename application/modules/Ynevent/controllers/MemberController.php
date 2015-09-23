<?php
require_once APPLICATION_PATH . '/application/modules/Ynevent/Libs/PHPExcel.php';
require_once APPLICATION_PATH . '/application/modules/Ynevent/Libs/PHPExcel/IOFactory.php';

class Ynevent_MemberController extends Core_Controller_Action_Standard
{

	public function init()
	{
		if (0 !== ($event_id = (int)$this -> _getParam('event_id')) && null !== ($event = Engine_Api::_() -> getItem('event', $event_id)))
		{
			if (!Engine_Api::_()->core()->hasSubject()) {
				Engine_Api::_() -> core() -> setSubject($event);
			}
		}

		$this -> _helper -> requireUser();
		$this -> _helper -> requireSubject('event');
	}

	public function joinAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		// Check resource approval
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> membership() -> isResourceApprovalRequired())
		{
			$row = $subject -> membership() -> getReceiver() -> select() -> where('resource_id = ?', $subject -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> query() -> fetch(Zend_Db::FETCH_ASSOC, 0); ;
			if (empty($row))
			{
				// has not yet requested an invite
				return $this -> _helper -> redirector -> gotoRoute(array(
					'action' => 'request',
					'format' => 'smoothbox'
				));
			}
			elseif ($row['user_approved'] && !$row['resource_approved'])
			{
				// has requested an invite; show cancel invite page
				return $this -> _helper -> redirector -> gotoRoute(array(
					'action' => 'cancel',
					'format' => 'smoothbox'
				));
			}
		}

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Join();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject = Engine_Api::_() -> core() -> getSubject();
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$membership = $subject -> membership() -> getRow($viewer);
				$membership_status = false;
				if (!empty($membership))
				{
					$membership_status = $membership -> active;
				}

				$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);

				$row = $subject -> membership() -> getRow($viewer);

				$row -> rsvp = $form -> getValue('rsvp');
				$row -> save();

				// Add activity if membership status was not valid from before
				if (!$membership_status)
				{
					$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
					$action = $activityApi -> addActivity($viewer, $subject, 'ynevent_join');
				}

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}
			if ($row -> rsvp == 2)
			{
				$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
				$table -> setOptionFollowEvent($subject -> getIdentity(), $viewer -> getIdentity(), 1);
			}

			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event joined')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event joined')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function requestAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Request();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject = Engine_Api::_() -> core() -> getSubject();
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$subject -> membership() -> addMember($viewer) -> setUserApproved($viewer);

				// Add notification
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($subject -> getOwner(), $viewer, $subject, 'ynevent_approve');

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your invite request has been sent.')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your invite request has been sent.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function cancelAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Cancel();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$user_id = $this -> _getParam('user_id');
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject = Engine_Api::_() -> core() -> getSubject();
			if (!$subject -> authorization() -> isAllowed($viewer, 'invite') && $user_id != $viewer -> getIdentity() && $user_id)
			{
				return;
			}

			if ($user_id)
			{
				$user = Engine_Api::_() -> getItem('user', $user_id);
				if (!$user)
				{
					return;
				}
			}
			else
			{
				$user = $viewer;
			}

			$subject = Engine_Api::_() -> core() -> getSubject('event');
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$subject -> membership() -> removeMember($user);

				// Remove the notification?
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($subject -> getOwner(), $subject, 'event_approve');
				if ($notification)
				{
					$notification -> delete();
				}

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your invite request has been cancelled.')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your invite request has been cancelled.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function leaveAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> isOwner($viewer))
			return;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Leave();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$subject -> membership() -> removeMember($viewer);
				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			//unfollow
			$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
			$table -> setOptionFollowEvent($subject -> getIdentity(), $viewer -> getIdentity(), 0);

			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event left')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event left')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function acceptAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Join();

		// Process form
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Method');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Data');
			return;
		}

		// Process form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$membership_status = $subject -> membership() -> getRow($viewer) -> active;

			$subject -> membership() -> setUserApproved($viewer);

			$row = $subject -> membership() -> getRow($viewer);

			$row -> rsvp = $form -> getValue('rsvp');
			$row -> save();

			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'ynevent_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			// Add activity
			if (!$membership_status)
			{
				$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
				$action = $activityApi -> addActivity($viewer, $subject, 'ynevent_join');
			}
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		if ($form -> getValue('rsvp') == 2)
		{
			$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
			$table -> setOptionFollowEvent($subject -> getIdentity(), $viewer -> getIdentity(), 1);
			// Engine_Api::_()->ynevent()->setEventFollow($subject, $viewer);
		}

		$this -> view -> status = true;
		$this -> view -> error = false;

		$message = Zend_Registry::get('Zend_Translate') -> _('You have accepted the invite to the event %s');
		$message = sprintf($message, $subject -> __toString());
		$this -> view -> message = $message;

		if ($this -> _helper -> contextSwitch -> getCurrentContext() == "smoothbox")
		{
			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event invite accepted')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event invite accepted')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function rejectAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Reject();

		// Process form
		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Method');
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			$this -> view -> status = false;
			$this -> view -> error = true;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Invalid Data');
			return;
		}

		// Process form
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$subject -> membership() -> removeMember($viewer);

			// Set the request as handled
			$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($viewer, $subject, 'ynevent_invite');
			if ($notification)
			{
				$notification -> mitigated = true;
				$notification -> save();
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		//unfollow
		$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
		$table -> setOptionFollowEvent($subject -> getIdentity(), $viewer -> getIdentity(), 1);

		$this -> view -> status = true;
		$this -> view -> error = false;
		$message = Zend_Registry::get('Zend_Translate') -> _('You have ignored the invite to the event %s');
		$message = sprintf($message, $subject -> __toString());
		$this -> view -> message = $message;

		if ($this -> _helper -> contextSwitch -> getCurrentContext() == "smoothbox")
		{
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event invite rejected')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event invite rejected')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function removeAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		$event = Engine_Api::_() -> core() -> getSubject();

		if (!$event -> membership() -> isMember($user))
		{
			throw new Event_Model_Exception('Cannot remove a non-member');
		}

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Remove();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$db = $event -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				// Remove membership
				$event -> membership() -> removeMember($user);

				// Remove the notification?
				$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationByObjectAndType($event -> getOwner(), $event, 'ynevent_approve');
				if ($notification)
				{
					$notification -> delete();
				}

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event member removed.')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function inviteAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;
		// @todo auth
		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();

		// Prepare friends
		$friendsTable = Engine_Api::_() -> getDbtable('membership', 'user');
		$friendsIds = $friendsTable -> select() -> from($friendsTable, 'user_id') -> where('resource_id = ?', $viewer -> getIdentity()) -> where('active = ?', true) -> limit(100) -> query() -> fetchAll(Zend_Db::FETCH_COLUMN);
		if (!empty($friendsIds))
		{
			// 			$friends = Engine_Api::_()->getItemTable('user')->find($friendsIds);
			$friendTbl = Engine_Api::_() -> getItemTable('user');
			$select = $friendTbl -> select() -> where('user_id IN (?)', $friendsIds) -> order('displayname');
			$friends = $friendTbl -> fetchAll($select);
		}
		else
		{
			$friends = array();
		}
		$this -> view -> friends = $friends;

		// Prepare form
		$this -> view -> form = $form = new Ynevent_Form_Invite();

		$count = 0;
		foreach ($friends as $friend)
		{
			if ($event -> membership() -> isMember($friend, null))
			{
				continue;
			}
			$form -> users -> addMultiOption($friend -> getIdentity(), $friend -> getTitle());
			$count++;
		}
		$this -> view -> count = $count;

		// Not posting
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		$values = $form -> getValues();

		// Process
		$table = $event -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$usersIds = $form -> getValue('users');

			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if ($form -> getElement('message'))
			{
				$message = $form -> getElement('message') -> getValue();
			}
			foreach ($friends as $friend)
			{
				if (!in_array($friend -> getIdentity(), $usersIds))
				{
					continue;
				}

				$event -> membership() -> addMember($friend) -> setResourceApproved($friend);

				if (isset($message) && !empty($message))
				{
					$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite_message', array('message' => $message));
				}
				else
				{
					$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite');
				}
			}

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		//Invite people via email
		$recipients = $values['recipients'];
		$message = $values['message'];
		if (isset($message) && !empty($message))
		{
			$sent = $this -> InviteViaEmail($recipients, $message, $event, "ynevent_invite_message");
		}
		else
		{
			$sent = $this -> InviteViaEmail($recipients, $message, $event, "ynevent_invite");
		}
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$callbackUrl = $this -> view -> url(array('id' => $event -> getIdentity()), 'event_profile', true);
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRedirect' => $callbackUrl,
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited'))
			));
		}
		else
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function InviteViaEmail($recipients, $message = NULL, $object, $type)
	{
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$user = Engine_Api::_() -> user() -> getViewer();
		// Check recipients
		if (is_string($recipients))
		{
			$recipients = preg_split("/[\s,]+/", $recipients);
		}
		if (is_array($recipients))
		{
			$recipients = array_map('strtolower', array_unique(array_filter(array_map('trim', $recipients))));
		}
		if (!is_array($recipients) || empty($recipients))
		{
			return 0;
		}

		// Only allow a certain number for now
		$max = $settings -> getSetting('invite.max', 10);
		if (count($recipients) > $max)
		{
			$recipients = array_slice($recipients, 0, $max);
		}

		// Check message
		$message = trim($message);
		$emailsSent = 0;
		foreach ($recipients as $recipient)
		{
			try
			{
				$defaultParams = array(
					'host' => $_SERVER['HTTP_HOST'],
					'email' => $recipient,
					'date' => time(),
					'recipient_title' => "Guest",
					'sender_title' => $user -> getTitle(),
					'sender_link' => $user -> getHref(),
					'object_title' => $object -> getTitle(),
					'object_link' => $object -> getHref(),
					'object_photo' => $object -> getPhotoUrl('thumb.icon'),
					'object_description' => $object -> getDescription(),
					'message' => $message,
				);
				Engine_Api::_() -> getApi('mail', 'core') -> sendSystem($recipient, 'notify_' . $type, $defaultParams);
			}
			catch (Exception $e)
			{
				// Silence
				if (APPLICATION_ENV == 'development')
				{
					throw $e;
				}
				continue;
			}
			$emailsSent++;
		}
		return $emailsSent;
	}

	public function approveAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;

		// Get user
		if (0 === ($user_id = (int)$this -> _getParam('user_id')) || null === ($user = Engine_Api::_() -> getItem('user', $user_id)))
		{
			return $this -> _helper -> requireSubject -> forward();
		}

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Approve();

		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$subject = Engine_Api::_() -> core() -> getSubject();
			$db = $subject -> membership() -> getReceiver() -> getTable() -> getAdapter();
			$db -> beginTransaction();

			try
			{
				$subject -> membership() -> setResourceApproved($user);

				Engine_Api::_() -> getDbtable('notifications', 'activity') -> addNotification($user, $viewer, $subject, 'ynevent_accepted');

				$db -> commit();
			}
			catch (Exception $e)
			{
				$db -> rollBack();
				throw $e;
			}

			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event request approved')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function ajaxGroupsAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;

		$search = '';

		$search = $this -> _getParam('key');
		$groups = Engine_Api::_() -> ynevent() -> getGroupMember($viewer -> getIdentity(), $search);
		$gr = array();
		$view = Zend_Registry::get('Zend_View');
		foreach ($groups AS $g)
		{

			$gr[] = array(
				'group_id' => $g -> group_id,
				'photo' => $view -> itemPhoto($g, 'thumb.icon'), //$g->getPhotoUrl('thumb.icon'),
				'title' => Engine_Api::_() -> ynevent() -> subPhrase($g -> title, 20),
			);
		}
		$this -> view -> rows = $gr;
		$this -> view -> total = count($gr);

	}

	public function inviteGroupsAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;
		// @todo auth
		// Prepare data

		$event = Engine_Api::_() -> core() -> getSubject();

		$search = '';
		$this -> view -> groups = $groups = Engine_Api::_() -> ynevent() -> getGroupMember($viewer -> getIdentity(), $search);

		$this -> view -> event_id = $event -> getIdentity();

		//$this->view->maxgroups = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.groups',3);
		// Not posting
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$variable = $this -> getRequest() -> getPost();
		$send_groups = $variable['users'];
		$session = new Zend_Session_Namespace('mobile');
		if (is_array($send_groups))
		{

			$myfriend = array();
			foreach ($send_groups AS $key)
			{
				$gro = Engine_Api::_() -> getItem('group', $key);
				$members = $gro -> membership() -> getMembers();
				foreach ($members AS $mem)
				{
					if ($mem -> user_id != $viewer -> getIdentity())
						$myfriend[] = $mem;
				}
			}

			$myfriend = array_unique($myfriend);
			$notifyApi = Engine_Api::_() -> getDbTable('notifications', 'activity');

			foreach ($myfriend as $friend)
			{
				$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite');
			}
			if ($session -> mobile)
			{
				$callbackUrl = $this -> view -> url(array('id' => $event -> getIdentity()), 'event_profile', true);
				$this -> _forward('success', 'utility', 'core', array(
					'smoothboxClose' => true,
					'parentRedirect' => $callbackUrl,
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Groups invited'))
				));
			}
			else
			{
				return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Groups invited')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
				));
			}
		}
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			return $this -> _helper -> redirector -> gotoRoute(array('id' => $event -> getIdentity()), 'event_profile', true);
		}
		else
		{
			return $this -> _forward('success', 'utility', 'core', array(
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('')),
				'layout' => 'default-simple',
				'parentRefresh' => true,
			));
		}
	}

	public function suggestAction()
	{
		$event_id = $this -> _getParam('event_id');
		$event = Engine_Api::_() -> getItem('event', $event_id);
		if (!$event)
		{
			$data = null;
		}
		else
		{
			$table_user = Engine_Api::_() -> getDbtable('users', 'user');
			$table_user_name = $table_user -> info('name');
			$data = array();

			$table = Engine_Api::_() -> getItemTable('event');
			$select = $event -> membership() -> getMembersObjectSelect();

			if (0 < ($limit = (int)$this -> _getParam('limit', 10)))
			{
				$select -> limit($limit);
			}

			if (null !== ($text = $this -> _getParam('search', $this -> _getParam('value'))))
			{
				// fix for SEGROUP-193: Owner can invite himself
				$select -> where('displayname LIKE ?', '%' . $text . '%') -> where("$table_user_name.user_id <> ?", $event -> getOwner() -> getIdentity());
			}
			foreach ($select->getTable()->fetchAll($select) as $friend)
			{
				$data[] = array(
					'type' => 'user',
					'id' => $friend -> getIdentity(),
					'guid' => $friend -> getGuid(),
					'label' => $friend -> getTitle(),
					'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'),
					'url' => $friend -> getHref(),
				);
			}
		}
		return $this -> _helper -> json($data);
	}	
	
	public function exportCsvAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer)
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have permission to export guest list")
			));
			return;
		}
			
		$eventId = $this->_getParam('event_id', 0);
		if (!$eventId)
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing event id")
			));
			return;
		}
		
		$event = Engine_Api::_()->getItem("event", $eventId);
		if (is_null($event))
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This event is not existed!")
			));
			return;
		}
		
		$members = null;
		if (!$members) {
			$select = $event->membership()->getMembersObjectSelect();
			$userTable = Engine_Api::_()->getDbtable('users', 'user');
			$members = $userTable->fetchAll($select);
		}
		
		$translate = Zend_Registry::get("Zend_Translate");
		
		// Convert the dates for the viewer
		$startDateObject = new Zend_Date(strtotime($event->starttime));
		$endDateObject = new Zend_Date(strtotime($event->endtime));
		if( $viewer && $viewer->getIdentity() ) {
			$tz = $viewer->timezone;
			$startDateObject->setTimezone($tz);
			$endDateObject->setTimezone($tz);
		}
		$viewObject = Zend_Registry::get("Zend_View");
		$from = $viewObject->locale()->toDate($startDateObject) . " " . $viewObject->locale()->toTime($startDateObject);
		$to = $viewObject->locale()->toDate($endDateObject) . " " . $viewObject->locale()->toTime($endDateObject);
		
		$out  = $translate->_("Event Name") . ": " . $event->getTitle() . "\n\n";
		$out .= $translate->_("From") . ": " . $from  . " - ";
		$out .= $translate->_("To") . ": " . $to . "\n";
		$out .= $translate->_("Display Name") . ";" . $translate->_("RSVP") . ";" . $translate->_("Attendance") . "\n";
		
		$rsvpValues = array(
				$translate->_("Not Attending"), $translate->_("Maybe Attending"), $translate->_("Attending")
		);
		
		
		foreach ($members as $member){
			$memberInfo = $event->membership()->getMemberInfo($member);
			$out .= $member->getTitle() . ";" . @$rsvpValues[intval($memberInfo->rsvp)] . "\n";
		}
		
		$filename_prefix = 'export_members';
		$filename = $filename_prefix."_".date("Y-m-d_H-i",time());
		
		//Generate the CSV file header
		header("Content-type: application/vnd.ms-excel");
		header("Content-Encoding: UTF-8");
		header("Content-type: text/csv; charset=UTF-8");
		header("Content-disposition: csv" . date("Y-m-d") . ".csv");
		header("Content-disposition: filename=".$filename.".csv");
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		//Print the contents of out to the generated file.
		print $out;
		
		//Exit the script
		exit;
	}
	
	public function exportExcelAction()
	{
		if(!class_exists('PHPExcel'))
		{
			echo Zend_Registry::get('Zend_Translate') -> _('Missing libraries'); 
			exit;
		}
		
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!$viewer)
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("You do not have permission to export guest list")
			));
			return;
		}
			
		$eventId = $this->_getParam('event_id', 0);
		if (!$eventId)
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("Missing event id")
			));
			return;
		}
		
		$event = Engine_Api::_()->getItem("event", $eventId);
		if (is_null($event))
		{
			echo Zend_Json::encode(array(
					'error_code' => '1',
					'error_message' => Zend_Registry::get("Zend_Translate")->_("This event is not existed!")
			));
			return;
		}
		
		$members = null;
		if (!$members) {
			$select = $event->membership()->getMembersObjectSelect();
			$userTable = Engine_Api::_()->getDbtable('users', 'user');
			$members = $userTable->fetchAll($select);
		}
		
		$translate = Zend_Registry::get("Zend_Translate");
		
		// Convert the dates for the viewer
		$startDateObject = new Zend_Date(strtotime($event->starttime));
		$endDateObject = new Zend_Date(strtotime($event->endtime));
		if( $viewer && $viewer->getIdentity() ) {
			$tz = $viewer->timezone;
			$startDateObject->setTimezone($tz);
			$endDateObject->setTimezone($tz);
		}
		$viewObject = Zend_Registry::get("Zend_View");
		$from = $viewObject->locale()->toDate($startDateObject) . " " . $viewObject->locale()->toTime($startDateObject);
		$to = $viewObject->locale()->toDate($endDateObject) . " " . $viewObject->locale()->toTime($endDateObject);
		$eventDate = $translate->_("From") . ": " . $from  . " - " . $translate->_("To") . ": " . $to;
		
		$rsvpValues = array(
				$translate->_("Not Attending"), $translate->_("Maybe Attending"), $translate->_("Attending")
		);

		// init excel template
		define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load(APPLICATION_PATH . "/application/modules/Ynevent/Libs/templates/30template.xls");
		
		//init data
		$data = array();
		foreach ($members as $member){
			$memberInfo = $event->membership()->getMemberInfo($member);
			$data[] = array(
					'display_name' => $member->getTitle(),
					'rsvp' => @$rsvpValues[intval($memberInfo->rsvp)],
					'attendace' => '',
			);
		}

		// init title
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $translate->_("Event Name") . ": " . $event->getTitle());
		$objPHPExcel->getActiveSheet()->setCellValue('A2', $eventDate);
		$baseRow = 5;
		foreach($data as $r => $dataRow) {
			$row = $baseRow + $r;
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		
			$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$row, $dataRow['display_name'])
			->setCellValue('B'.$row, $dataRow['rsvp'])
			->setCellValue('C'.$row, $dataRow['attendace']);
		}
		
		$objPHPExcel->getActiveSheet()->removeRow($baseRow-1,1);
		
		// Write to Excel5 format
		$filename_prefix = 'export_members';
		$filename = $filename_prefix."_".date("Y-m-d_H-i",time());
		$tempFileName = $filename_prefix."_".date("Y-m-d_H-i",time()) . '.xls';
		$tempFilePath = APPLICATION_PATH . '/public/temporary/' . $tempFileName;
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save($tempFilePath);
		
		$out = file_get_contents($tempFilePath);
		
		//Generate the EXCEL file header
		header("Content-type: application/vnd.ms-excel");
		header("Content-Encoding: UTF-8");
		header("Content-disposition: attachment" . date("Y-m-d") . ".xls");
		header("Content-disposition: filename=".$filename.".xls");
		
		//Print the contents of out to the generated file.
		print $out;
		
		//Exit the script
		exit;
	}
	
	public function joinRepeatedEventsAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
	
		// Check resource approval
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> membership() -> isResourceApprovalRequired())
		{
			$row = $subject -> membership() -> getReceiver() -> select() -> where('resource_id = ?', $subject -> getIdentity()) -> where('user_id = ?', $viewer -> getIdentity()) -> query() -> fetch(Zend_Db::FETCH_ASSOC, 0); ;
			if (empty($row))
			{
				// has not yet requested an invite
				return $this -> _helper -> redirector -> gotoRoute(array(
						'action' => 'request',
						'format' => 'smoothbox'
				));
			}
			elseif ($row['user_approved'] && !$row['resource_approved'])
			{
				// has requested an invite; show cancel invite page
				return $this -> _helper -> redirector -> gotoRoute(array(
						'action' => 'cancel',
						'format' => 'smoothbox'
				));
			}
		}
	
		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Join();
	
		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$viewer = Engine_Api::_() -> user() -> getViewer();
			$ids = $this->_getParam('ids');
			$aIds = explode(",", $ids);
			if (count($aIds))
			{
				foreach ($aIds as $id)
				{
					$event = Engine_Api::_()->getItem("event", $id);
					
					if($event->membership()->isMember($viewer, null))
						continue;
					
					if ($event !== null){
						$db = $event -> membership() -> getReceiver() -> getTable() -> getAdapter();
						$db -> beginTransaction();
							
						try
						{
							$membership = $event -> membership() -> getRow($viewer);
							$membership_status = false;
							if (!empty($membership))
							{
								$membership_status = $membership -> active;
							}
								
							$event -> membership() -> addMember($viewer) -> setUserApproved($viewer);
								
							$row = $event -> membership() -> getRow($viewer);
								
							$row -> rsvp = $form -> getValue('rsvp');
							$row -> save();
								
							// Add activity if membership status was not valid from before
							if (!$membership_status)
							{
								$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
								$action = $activityApi -> addActivity($viewer, $event, 'ynevent_join');
							}
								
							$db -> commit();
						}
						catch (Exception $e)
						{
							$db -> rollBack();
							continue;
						}
						if ($row -> rsvp == 2)
						{
							$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
							$table -> setOptionFollowEvent($event -> getIdentity(), $viewer -> getIdentity(), 1);
						}
					}
					
				}
			}
	
			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event(s) joined successfully!')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event(s) joined successfully!')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}
	
	public function leaveRepeatedEventsAction()
	{
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
	
		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Member_Leave();
	
		// Process form
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$ids = $this->_getParam('ids');
			$aIds = explode(",", $ids);
			if (count($aIds))
			{
				foreach ($aIds as $id)
				{
					
					$event = Engine_Api::_()->getItem("event", $id);
					
					if ($event -> isOwner($viewer))
						continue;
					
					if(!$event->membership()->isMember($viewer, null))
						continue;
					
					$db = $event -> membership() -> getReceiver() -> getTable() -> getAdapter();
					$db -> beginTransaction();
					
					try
					{
						$event -> membership() -> removeMember($viewer);
						$db -> commit();
					}
					catch (Exception $e)
					{
						$db -> rollBack();
						continue;
					}
					
					//unfollow
					$table = Engine_Api::_() -> getDbTable('follow', 'ynevent');
					$table -> setOptionFollowEvent($event -> getIdentity(), $viewer -> getIdentity(), 0);
					
				}
			}
	
			$tabId = $this->_getParam('tab', null);
			if ($tabId)
			{
				return $this -> _forward('success', 'utility', 'core', array(
						'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event(s) left successfully!')),
						'layout' => 'default-simple',
						'parentRedirect' => $this->view->url(array('id' => $subject -> getIdentity(), 'tab' => $tabId), 'event_profile'),
				));
			}
			
			return $this -> _forward('success', 'utility', 'core', array(
					'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Event(s) left successfully!')),
					'layout' => 'default-simple',
					'parentRefresh' => true,
			));
		}
	}
	
	public function listingAction()
	{
		// Don't render this if not authorized
		$viewer = Engine_Api::_()->user()->getViewer();
		if (!Engine_Api::_()->core()->hasSubject()) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$subject = Engine_Api::_()->core()->getSubject('event');
		if (!$subject->authorization()->isAllowed($viewer, 'view')) {
			return $this->setNoRender();
		}
		
		// Get params
		$this->view->page = $page = $this->_getParam('page', 1);
		$this->view->search = $search = $this->_getParam('search');
		$this->view->waiting = $waiting = $this->_getParam('waiting', false);
		
		// Prepare data
		$this->view->event = $event = Engine_Api::_()->core()->getSubject();
		
		$this->view->isOwner = $event->isOwner($viewer);
		
		$members = null;
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() && $event->isOwner($viewer)) {
			$this->view->waitingMembers = Zend_Paginator::factory($event->membership()->getMembersSelect(false));
			if ($waiting) {
				$this->view->members = $members = $this->view->waitingMembers;
			}
		}
		
		if (!$members) {
			$select = $event->membership()->getMembersObjectSelect();
			if ($search) {
				$select->where('displayname LIKE ?', '%' . $search . '%');
			}
		
			$this->view->filter=$filter = $this->_getParam('filter');
		
			if (isset($filter) && $filter >= 0) {
		
				$membershipTable = Engine_Api::_()->getDbTable("membership", "ynevent");
				$membershipTableName = $membershipTable->info('name');
				$select->where("$membershipTableName.rsvp=?", $filter);
		
			}
			$userTable = Engine_Api::_()->getItemTable('user');
			$this->view->members = $members = $userTable->fetchAll($select);
		}
		
// 		$paginator = $members;
// 		$paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 2));
// 		$paginator->setCurrentPageNumber($this->_getParam('page', $page));

	}
	
	
}
