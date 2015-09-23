<?php
/**
 * Younetco
 *
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Copyright 2014 Younet SE Developments
 * @license    http://www.younetco.com
 * @author     LongL
 */
require_once APPLICATION_PATH . '/application/modules/Contactimporter/Plugin/constants.php';
class Contactimporter_Plugin_Core
{
	public function onUserSignupAfter($event)
	{
	    $payload = $event->getPayload();
	    if( $payload instanceof User_Model_User ) 
	    {
	    	if (isset($_COOKIE[INVITER_COOKIE_NAME]))
			{
				$inviterId = $_COOKIE[INVITER_COOKIE_NAME];
				$inviter = Engine_Api::_()->user()->getUser($inviterId);
				if (!is_null($inviter) && $inviter->getIdentity())
				{
					// Make friends
					try
					{
						$inviter -> membership() -> addMember($payload) 
							-> setUserApproved($payload)
							-> setResourceApproved($payload);
					}
					catch(Exception $e)
					{
						$inviter -> membership()
							-> setUserApproved($payload)
							-> setResourceApproved($payload);
							
						$payload -> membership()
							-> setUserApproved($inviter)
							-> setResourceApproved($inviter);
					}
					 
					// Remove notifications if any
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($payload, $inviter, 'friend_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					$notification = Engine_Api::_() -> getDbtable('notifications', 'activity') -> getNotificationBySubjectAndType($payload, $inviter, 'friend_follow_request');
					if ($notification)
					{
						$notification -> mitigated = true;
						$notification -> read = 1;
						$notification -> save();
					}
					 
					// Add activity
					if (!$inviter -> membership() -> isReciprocal())
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($inviter, $payload, 'friends_follow', '{item:$subject} is now following {item:$object}.');
					}
					else
					{
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($payload, $inviter, 'friends', '{item:$object} is now friends with {item:$subject}.');
						Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($inviter, $payload, 'friends', '{item:$object} is now friends with {item:$subject}.');
					}
					
					$joinedTbl = Engine_Api::_()->getDbTable("joined","contactimporter");
					$joined = $joinedTbl->createRow();
					$joined->inviter_id = $inviterId;
					$joined->recipient_id = $payload->getIdentity();
					$joined->save();
					
					$invitationTbl = Engine_Api::_()->getDbTable("invitations","contactimporter");
					$select = $invitationTbl->select()
						->where("inviter_id = ?", $inviterId)
						->where("email = ?", $payload->email);
					
					$invitations = 	$invitationTbl->fetchAll($select);
					
					if (count($invitations))
					{
						foreach ($invitations as $invitation) 
						{
							$invitation->inviter_deleted = 1;
							$invitation->save();
						}
					}
					
					
					// Delete cookies
					setcookie(INVITER_COOKIE_NAME, "", time()-604800, "/");
					unset($_COOKIE[INVITER_COOKIE_NAME]);
				}
			}
		}
	}
}