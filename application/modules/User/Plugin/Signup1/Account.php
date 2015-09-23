<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 10099 2013-10-19 14:58:40Z ivan $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Plugin_Signup1_Account extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';

  protected $_formClass = 'User_Form_Signup1_Account';

  protected $_script = array('signup/form/account1.tpl', 'user');

  public $email = null;

  public function onProcess()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $random = ($settings->getSetting('user.signup.random', 0) == 1);
	$emailadmin = ($settings->getSetting('user.signup.adminemail', 0) == 1);
	if( $emailadmin ) {
	  // the signup notification is emailed to the first SuperAdmin by default
	  $users_table = Engine_Api::_()->getDbtable('users', 'user');
	  $users_select = $users_table->select()
  	    ->where('level_id = ?', 1)
	    ->where('enabled >= ?', 1);
	  $super_admin = $users_table->fetchRow($users_select);
	}
    $data = $this->getSession()->data;
	$sessionStep1 = new Zend_Session_Namespace('User_Plugin_Signup1_Step1');
	$dataStep1 = $sessionStep1 -> data;
	$data = array_merge($data, $dataStep1);
	$data['profile_type'] = Engine_Api::_() -> user() -> getDefaultProfileTypeId();
    // Add email and code to invite session if available
    $inviteSession = new Zend_Session_Namespace('invite');
    if( isset($data['email']) ) {
      $inviteSession->signup_email = $data['email'];
    }
    if( isset($data['code']) ) {
      $inviteSession->signup_code = $data['code'];
    }

    if( $random ) {
      $data['password'] = Engine_Api::_()->user()->randomPass(10);
    }
    
    if (isset($data['language'])) {
      $data['locale'] = $data['language'];
    }
    // Create user
    // Note: you must assign this to the registry before calling save or it
    // will not be available to the plugin in the hook
    $this->_registry->user = $user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
    $user->setFromArray($data);
    $user->save();
    
	
    Engine_Api::_()->user()->setViewer($user);

    // Increment signup counter
    Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');
    
    if( $user->verified && $user->enabled ) {
      // Create activity for them
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'signup');
      // Set user as logged in if not have to verify email
      Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
    }

    $mailType = null;
    $mailParams = array(
      'host' => $_SERVER['HTTP_HOST'],
      'email' => $user->email,
      'date' => time(),
      'recipient_title' => $user->getTitle(),
      'recipient_link' => $user->getHref(),
      'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
      'object_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
    );

    // Add password to email if necessary
    if( $random ) {
      $mailParams['password'] = $data['password'];
    }

    // Mail stuff
    switch( $settings->getSetting('user.signup.verifyemail', 0) ) {
      case 0:
        // only override admin setting if random passwords are being created
        if( $random ) {
          $mailType = 'core_welcome_password';
        }
		if( $emailadmin ) {
		$mailAdminType = 'notify_admin_user_signup';
        
		$mailAdminParams = array(
			'host' => $_SERVER['HTTP_HOST'],
			'email' => $user->email,
			'date' => date("F j, Y, g:i a"),
			'recipient_title' => $super_admin->displayname,
			'object_title' => $user->displayname,
			'object_link' => $user->getHref(),
		);
	
		}
        break;

      case 1:
        // send welcome email
        $mailType = ($random ? 'core_welcome_password' : 'core_welcome');
		if( $emailadmin ) {
		$mailAdminType = 'notify_admin_user_signup';
		     
		$mailAdminParams = array(
			'host' => $_SERVER['HTTP_HOST'],
			'email' => $user->email,
			'date' => date("F j, Y, g:i a"),
			'recipient_title' => $super_admin->displayname,
			'object_title' => $user->getTitle(),
			'object_link' => $user->getHref(),
		);
	
		}
        break;

      case 2:
        // verify email before enabling account
        $verify_table = Engine_Api::_()->getDbtable('verify', 'user');
        $verify_row = $verify_table->createRow();
        $verify_row->user_id = $user->getIdentity();
        $verify_row->code = md5($user->email
            . $user->creation_date
            . $settings->getSetting('core.secret', 'staticSalt')
            . (string) rand(1000000, 9999999));
        $verify_row->date = $user->creation_date;
        $verify_row->save();
        
        $mailType = ($random ? 'core_verification_password' : 'core_verification');
        
        $mailParams['object_link'] = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'action' => 'verify',
              'email' => $user->email,
              'verify' => $verify_row->code
            ), 'user_signup', true);
		
		if( $emailadmin ) {
		$mailAdminType = 'notify_admin_user_signup';
        
		$mailAdminParams = array(
			'host' => $_SERVER['HTTP_HOST'],
			'email' => $user->email,
			'date' => date("F j, Y, g:i a"),
			'recipient_title' => $super_admin->displayname,
			'object_title' => $user->getTitle(),
			'object_link' => $user->getHref(),
		);
	
		}
        break;

      default:
        // do nothing
        break;
    }
    
    if( !empty($mailType) ) {
      $this->_registry->mailParams = $mailParams;
      $this->_registry->mailType   = $mailType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }
	
    if( !empty($mailAdminType) ) {
      $this->_registry->mailAdminParams = $mailAdminParams;
      $this->_registry->mailAdminType   = $mailAdminType;
      // Moved to User_Plugin_Signup_Fields
      // Engine_Api::_()->getApi('mail', 'core')->sendSystem(
      //   $user,
      //   $mailType,
      //   $mailParams
      // );
    }
    
    // Attempt to connect facebook
    if( !empty($_SESSION['facebook_signup']) ) {
      try {
        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $facebook = $facebookTable->getApi();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $facebook && $settings->core_facebook_enable ) {
          $facebookTable->insert(array(
            'user_id' => $user->getIdentity(),
            'facebook_uid' => $facebook->getUser(),
            'access_token' => $facebook->getAccessToken(),
            //'code' => $code,
            'expires' => 0, // @todo make sure this is correct
          ));
        }
      } catch( Exception $e ) {
        // Silence
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
    
    // Attempt to connect twitter
    if( !empty($_SESSION['twitter_signup']) ) {
      try {
        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $twitter = $twitterTable->getApi();
        $twitterOauth = $twitterTable->getOauth();
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        if( $twitter && $twitterOauth && $settings->core_twitter_enable ) {
          $accountInfo = $twitter->account->verify_credentials();
          $twitterTable->insert(array(
            'user_id' => $user->getIdentity(),
            'twitter_uid' => $accountInfo->id,
            'twitter_token' => $twitterOauth->getToken(),
            'twitter_secret' => $twitterOauth->getTokenSecret(),
          ));
        }
      } catch( Exception $e ) {
        // Silence?
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
    
    // Attempt to connect twitter
    if( !empty($_SESSION['janrain_signup']) ) {
      try {
        $janrainTable = Engine_Api::_()->getDbtable('janrain', 'user');
        $settings = Engine_Api::_()->getDbtable('settings', 'core');
        $info = $_SESSION['janrain_signup_info'];
        if( $settings->core_janrain_enable ) {
          $janrainTable->insert(array(
            'user_id' => $user->getIdentity(),
            'identifier' => $info['identifier'],
            'provider' => $info['providerName'],
            'token' => (string) @$_SESSION['janrain_signup_token'],
          ));
        }
      } catch( Exception $e ) {
        // Silence?
        if( 'development' == APPLICATION_ENV ) {
          echo $e;
        }
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();
    $settings->user_signup = $values;
    if( $values['inviteonly'] == 1 ) {
      $step_table = Engine_Api::_()->getDbtable('signup', 'user');
      $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
      $step_row->enable = 0;
    }

    $form->addNotice('Your changes have been saved.');
  }

}
