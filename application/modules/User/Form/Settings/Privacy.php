<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Privacy.php 9747 2012-07-26 02:08:08Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Settings_Privacy extends Engine_Form
{
  public    $saveSuccessful  = FALSE;
  protected $_roles           = array('owner', 'member', 'network', 'registered', 'everyone');
  protected $_item;

  public function setItem(User_Model_User $item)
  {
    $this->_item = $item;
  }

  public function getItem()
  {
    if( null === $this->_item ) {
      throw new User_Model_Exception('No item set in ' . get_class($this));
    }

    return $this->_item;
  }
  
  public function init()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = $this->getItem();


    $this->setTitle('Privacy Settings')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init blocklist
    $this->addElement('Hidden', 'blockList', array(
      'label' => 'Blocked Members',
      'description' => 'Adding a person to your block list makes your profile (and all of your other content) unviewable to them. Any connections you have to the blocked person will be canceled.',
      'order' => -1
    ));
    Engine_Form::addDefaultDecorators($this->blockList);
    
    // Init search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Do not display me in searches, browsing members, or the "Online Members" list.',
      'checkedValue' => 0,
      'uncheckedValue' => 1,
    ));

    $availableLabels = array(
      'owner'       => 'Only Me',
      'member'      => 'Only My Followers',
      'network'     => 'Followers & Networks',
      'registered'  => 'All Registered Members',
      'everyone'    => 'Everyone',
    );
    
    // Init profile view
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    $this->addElement('Radio', 'privacy', array(
      'label' => 'Profile Privacy',
      'description' => 'Who can view your profile?',
      'multiOptions' => $view_options,
    ));

    foreach( $this->_roles as $role ) {
      if( 1 === $auth->isAllowed($user, $role, 'view') ) {
        $this->privacy->setValue($role);
      }
    }

    $availableLabelsComment = array(
      'owner'       => 'Only Me',
      'member'      => 'Only My Followers',
      'network'     => 'Followers & Networks',
      'registered'  => 'All Registered Members',
    );

    // Init profile comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment');
    $comment_options = array_intersect_key($availableLabelsComment, array_flip($comment_options));
    
    $this->addElement('Radio', 'comment', array(
      'label' => 'Profile Posting Privacy',
      'description' => 'Who can post on your profile?',
      'multiOptions' => $comment_options,
    ));
    
    foreach( $this->_roles as $role ) {
      if( 1 === $auth->isAllowed($user, $role, 'comment') ) {
        $this->comment->setValue($role);
      }
    }
	
	 $this->addElement('Radio', 'private_contact', array(
      'label' => 'Private Contact Information',
      'description' => 'Who can view my private contact information?',
      'multiOptions' => array(0 => 'Everyone',
      						  1 => 'Followers & Professionals',
	  						  2 => 'Professionals Only'),
    ));
    
	$availableLabels = array(
	      'owner'       => 'Only Me',
	      'member'      => 'Only My Followers',
	      'network'     => 'Followers & Networks',
	      'registered'  => 'All Registered Members',
	);
	// Init profile view
	$get_notification_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmember_user', $user, 'auth_get_notification');
	$get_notification_options = array_intersect_key($availableLabels, array_flip($get_notification_options));

	$this->addElement('Radio', 'get_notification_privacy', array(
      'label' => 'Get Notification Privacy',
      'description' => 'Who can get notification for some of my action? Actions: Create new items. 
                        Join/attend Club/Event. 
                        All actions on members (follow/rate member, etc...). 
                        Like/comment an item.',
      'multiOptions' => $get_notification_options,
	));

	foreach( $this->_roles as $role ) {
		if( 1 === $auth->isAllowed($user, $role, 'get_notification') ) {
			$this->get_notification_privacy->setValue($role);
		}
	}

    // Init publishtypes
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.publish', true) ) {
      $this->addElement('MultiCheckbox', 'publishTypes', array(
        'label' => 'Recent Activity Privacy',
        'description' => 'Which of the following things do you want to have published about you in the recent activity feed? Note that changing this setting will only affect future news feed items.',
      ));
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
    
    return $this;
  }

  public function save()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = $this->getItem();

    // Process member profile viewing privacy
    $privacy_value = $this->getValue('privacy');
    if( empty($privacy_value) ) {
      $privacy_setting = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view'));
      // If admin did not choose any options, make it everyone.
      // If not, use the one option they have set since the only option may not aways be set to 'everyone'.
      $privacy_value = empty($privacy_setting)
                     ? 'everyone'
                     : $privacy_setting;
    }

    $privacy_max_role = array_search($privacy_value, $this->_roles);
    foreach( $this->_roles as $i => $role )
      $auth->setAllowed($user, $role, 'view', ($i <= $privacy_max_role) );


    // Process member profile commenting privacy
    $comment_value = $this->getValue('comment');
    if( empty($comment_value) ) {
      $comment_setting = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment'));
      $comment_value = empty($comment_setting)
                     ? 'registered'
                     : $comment_setting;
    }

    $comment_max_role = array_search($comment_value, $this->_roles);
    foreach( $this->_roles as $i => $role )
      $auth->setAllowed($user, $role, 'comment', ($i <= $comment_max_role) );
	
	// Process member profile getting notification privacy
	$notification_privacy_value = $this->getValue('get_notification_privacy');
	if( empty($notification_privacy_value) ) 
	{
		$privacy_setting = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_get_notification'));
		// If admin did not choose any options, make it everyone.
		// If not, use the one option they have set since the only option may not aways be set to 'everyone'.
		$notification_privacy_value = empty($privacy_setting)
			? 'everyone'
			: $privacy_setting;
	}

	$privacy_max_role = array_search($notification_privacy_value, $this->_roles);
	foreach( $this->_roles as $i => $role )
	{
		$auth->setAllowed($user, $role, 'get_notification', ($i <= $privacy_max_role) );
	}
  }
} // end public function save()