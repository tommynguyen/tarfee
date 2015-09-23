<?php

class Ynevent_Plugin_Menus
{
	public function canCreateEvents()
	{
		// Must be logged in
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer || !$viewer -> getIdentity())
		{
			return false;
		}

		// Must be able to create events
		if (!Engine_Api::_() -> authorization() -> isAllowed('event', $viewer, 'create'))
		{
			return false;
		}

		return true;
	}

	public function canViewEvents()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		// Must be able to view events
		if (!Engine_Api::_() -> authorization() -> isAllowed('event', $viewer, 'view'))
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_YneventMainManage()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return true;
	}
	
	public function onMenuInitialize_YneventMainFollowing()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_YneventMainCreate()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		if (!Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create'))
		{
			return false;
		}

		return true;
	}

	public function onMenuInitialize_YneventMainCalendar()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return true;

	}

	public function onMenuInitialize_YneventProfileEdit()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('Whoops, not a event!');
		}

		if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return false;
		}

		if (!$subject -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return false;
		}

		return array(
			'label' => 'Edit Event Details',
			'icon' => 'application/modules/Ynevent/externals/images/edit.png',
			'route' => 'event_specific',
			'params' => array(
				'action' => 'edit',
				'event_id' => $subject -> getIdentity(),
				'ref' => 'profile'
			)
		);
	}

	public function onMenuInitialize_YneventProfileStyle()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('Whoops, not a event!');
		}

		if (!$viewer -> getIdentity() || !$subject -> authorization() -> isAllowed($viewer, 'edit'))
		{
			return false;
		}

		if (!$subject -> authorization() -> isAllowed($viewer, 'style'))
		{
			return false;
		}

		return array(
			'label' => 'Edit Event Style',
			'icon' => 'application/modules/Ynevent/externals/images/style.png',
			'class' => 'smoothbox',
			'route' => 'event_specific',
			'params' => array(
				'action' => 'style',
				'event_id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			)
		);
	}

	public function onMenuInitialize_YneventProfileMember()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();

		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('Whoops, not a event!');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		$row = $subject -> membership() -> getRow($viewer);
		// Not yet associated at all
		if (null === $row)
		{
			if ($subject -> membership() -> isResourceApprovalRequired())
			{
				return array(
					'label' => 'Request Invite',
					'icon' => 'application/modules/Ynevent/externals/images/member/join.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'request',
						'event_id' => $subject -> getIdentity(),
					),
				);
			}
			else
			{

				if ($subject -> capacity == 0 || ($subject -> capacity != 0 && Engine_Api::_() -> ynevent() -> chkEventFollow($subject -> getIdentity()) < $subject -> capacity))
				{
					return array(
						'label' => 'Join Event',
						'icon' => 'application/modules/Ynevent/externals/images/member/join.png',
						'class' => 'smoothbox',
						'route' => 'event_extended',
						'params' => array(
							'controller' => 'member',
							'action' => 'join',
							'event_id' => $subject -> getIdentity(),
						),
					);
				}

			}
		}

		// Full member
		// @todo consider owner
		else
		if ($row -> active)
		{
			if (!$subject -> isOwner($viewer))
			{
				return array(
					'label' => 'Leave Event',
					'icon' => 'application/modules/Ynevent/externals/images/member/leave.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'leave',
						'event_id' => $subject -> getIdentity()
					),
				);
			}
			else
			{
				return false;
				/*
				 return array(
				 'label' => 'Delete Event',
				 'icon' => 'application/modules/Event/externals/images/delete.png',
				 'class' => 'smoothbox',
				 'route' => 'event_specific',
				 'params' => array(
				 'action' => 'delete',
				 'event_id' => $subject->getIdentity()
				 ),
				 );
				 */
			}
		}
		else
		if (!$row -> resource_approved && $row -> user_approved)
		{
			return array(
				'label' => 'Cancel Invite Request',
				'icon' => 'application/modules/Ynevent/externals/images/member/cancel.png',
				'class' => 'smoothbox',
				'route' => 'event_extended',
				'params' => array(
					'controller' => 'member',
					'action' => 'cancel',
					'event_id' => $subject -> getIdentity()
				),
			);
		}
		else
		if (!$row -> user_approved && $row -> resource_approved)
		{
			return array(
				array(
					'label' => 'Accept Event Invite',
					'icon' => 'application/modules/Ynevent/externals/images/member/accept.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'accept',
						'event_id' => $subject -> getIdentity()
					),
				),
				array(
					'label' => 'Ignore Event Invite',
					'icon' => 'application/modules/Ynevent/externals/images/member/reject.png',
					'class' => 'smoothbox',
					'route' => 'event_extended',
					'params' => array(
						'controller' => 'member',
						'action' => 'reject',
						'event_id' => $subject -> getIdentity()
					),
				),
			);
		}

		else
		{
			throw new Event_Model_Exception('An error has occurred.');
		}

		return false;
	}

	public function onMenuInitialize_YneventProfileReport()
	{
		return false;
	}

	public function onMenuInitialize_YneventProfileInvite()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}
		if (!$subject -> authorization() -> isAllowed($viewer, 'invite'))
		{
			return false;
		}
		$class = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
		}
		if ($subject -> capacity == 0 || ($subject -> capacity != 0 && Engine_Api::_() -> ynevent() -> chkEventFollow($subject -> getIdentity()) < $subject -> capacity))
		{
			return array(
				'label' => 'Invite Guests',
				'icon' => 'application/modules/Ynevent/externals/images/member/invite.png',
				'class' => $class,
				'route' => 'event_extended',
				'params' => array(
					//'module' => 'event',
					'controller' => 'member',
					'action' => 'invite',
					'event_id' => $subject -> getIdentity(),
				),
			);
		}
		else
		{
			return false;
		}

	}

	public function onMenuInitialize_YneventProfileShare()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Share This Event',
			'icon' => 'application/modules/Ynevent/externals/images/share.png',
			'class' => 'smoothbox',
			'route' => 'default',
			'params' => array(
				'module' => 'activity',
				'controller' => 'index',
				'action' => 'share',
				'type' => $subject -> getType(),
				'id' => $subject -> getIdentity(),
				'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_YneventProfileMessage()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}

		if (!$viewer -> getIdentity() || !$subject -> isOwner($viewer))
		{
			return false;
		}

		return array(
			'label' => 'Message Members',
			'icon' => 'application/modules/Messages/externals/images/send.png',
			'route' => 'messages_general',
			'params' => array(
				'action' => 'compose',
				'to' => $subject -> getIdentity(),
				'multi' => 'event'
			)
		);
	}

	public function onMenuInitialize_YneventProfileDelete()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}
		else
		if (!$subject -> authorization() -> isAllowed($viewer, 'delete'))
		{
			return false;
		}

		return array(
			'label' => 'Delete Event',
			'icon' => 'application/modules/Ynevent/externals/images/delete.png',
			'class' => 'smoothbox',
			'route' => 'event_specific',
			'params' => array(
				'action' => 'delete',
				'event_id' => $subject -> getIdentity(),
				//'format' => 'smoothbox',
			),
		);
	}

	public function onMenuInitialize_YneventProfilePromote()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}

		if (!$viewer -> getIdentity())
		{
			return false;
		}

		return array(
			'label' => 'Promote This Event',
			'icon' => 'application/modules/Ynevent/externals/images/promote.png',
			'class' => 'smoothbox',
			'route' => 'event_specific',
			'params' => array(
				'action' => 'promote',
				'event_id' => $subject -> getIdentity(),
			),
		);
	}

	public function onMenuInitialize_YneventProfileInviteGroup()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('This event does not exist.');
		}
	
		if ($subject -> group_invite == 0 || !in_array(Engine_Api::_() -> ynevent()->getPlugins(),array('group', 'yngroup')))
			return false;

		if (!$subject -> authorization() -> isAllowed($viewer, 'invite'))
		{
			return false;
		}

		if (Engine_Api::_() -> ynevent() -> getPlugins() == "")
			return false;
		$class = 'smoothbox';
		$format = 'smoothbox';
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';
			$format = '';
		}
		return array(
			'label' => 'Invite Groups',
			'icon' => 'application/modules/Ynevent/externals/images/member/invite.png',
			'class' => $class,
			'route' => 'event_extended',
			'params' => array(
				//'module' => 'event',
				'controller' => 'member',
				'action' => 'invite-groups',
				'event_id' => $subject -> getIdentity(),
				'format' => $format,
			),
		);
	}
	public function onMenuInitialize_YneventProfileTransfer()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$subject = Engine_Api::_() -> core() -> getSubject();
		if ($subject -> getType() !== 'event')
		{
			throw new Ynevent_Model_Exception('Whoops, not a event!');
		}
		if (!$viewer -> isAdmin() && !$subject -> isOwner($viewer))
		{
			return false;
		}
		$class = 'smoothbox';

		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			$class = '';

		}
		return array(
			'label' => 'Transfer Owner',
			'icon' => 'application/modules/Ynevent/externals/images/member/join.png',
			'route' => 'event_specific',
			'class' => $class,
			'params' => array(
				'action' => 'transfer',
				'event_id' => $subject -> getIdentity(),
			),
		);
	}
	
	public function renderEventAction($event = null)
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($event !== null)
		{
			$subject = $event;
		}
		else
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
		}
	
	
		if ($subject -> getType() !== 'event')
		{
			throw new Event_Model_Exception('Whoops, not a event!');
		}
	
		if (!$viewer -> getIdentity())
		{
			return false;
		}
	
		$row = $subject -> membership() -> getRow($viewer);
		// Not yet associated at all
		if (null === $row)
		{
			if ($subject -> membership() -> isResourceApprovalRequired())
			{
				return array(
						'label' => 'Request Invite',
						'icon' => 'application/modules/Ynevent/externals/images/member/join.png',
						'class' => 'smoothbox',
						'route' => 'event_extended',
						'params' => array(
								'controller' => 'member',
								'action' => 'request',
								'event_id' => $subject -> getIdentity(),
						),
				);
			}
			else
			{
	
				if ($subject -> capacity == 0 || ($subject -> capacity != 0 && Engine_Api::_() -> ynevent() -> chkEventFollow($subject -> getIdentity()) < $subject -> capacity))
				{
					return array(
							'label' => 'Join Event',
							'icon' => 'application/modules/Ynevent/externals/images/member/join.png',
							'class' => 'smoothbox',
							'route' => 'event_extended',
							'params' => array(
									'controller' => 'member',
									'action' => 'join',
									'event_id' => $subject -> getIdentity(),
							),
					);
				}
	
			}
		}
	
		// Full member
		// @todo consider owner
		else
			if ($row -> active)
			{
				if (!$subject -> isOwner($viewer))
				{
					return array(
							'label' => 'Leave Event',
							'icon' => 'application/modules/Ynevent/externals/images/member/leave.png',
							'class' => 'smoothbox',
							'route' => 'event_extended',
							'params' => array(
									'controller' => 'member',
									'action' => 'leave',
									'event_id' => $subject -> getIdentity()
							),
					);
				}
				else
				{
					return false;
				}
			}
			else
				if (!$row -> resource_approved && $row -> user_approved)
				{
					return array(
							'label' => 'Cancel Invite Request',
							'icon' => 'application/modules/Ynevent/externals/images/member/cancel.png',
							'class' => 'smoothbox',
							'route' => 'event_extended',
							'params' => array(
									'controller' => 'member',
									'action' => 'cancel',
									'event_id' => $subject -> getIdentity()
							),
					);
				}
				else
					if (!$row -> user_approved && $row -> resource_approved)
					{
						return array(
								array(
										'label' => 'Accept Event Invite',
										'icon' => 'application/modules/Ynevent/externals/images/member/accept.png',
										'class' => 'smoothbox',
										'route' => 'event_extended',
										'params' => array(
												'controller' => 'member',
												'action' => 'accept',
												'event_id' => $subject -> getIdentity()
										),
								),
								array(
										'label' => 'Ignore Event Invite',
										'icon' => 'application/modules/Ynevent/externals/images/member/reject.png',
										'class' => 'smoothbox',
										'route' => 'event_extended',
										'params' => array(
												'controller' => 'member',
												'action' => 'reject',
												'event_id' => $subject -> getIdentity()
										),
								),
						);
					}
	
					else
					{
						throw new Event_Model_Exception('An error has occurred.');
					}
	
					return false;
	}
}
