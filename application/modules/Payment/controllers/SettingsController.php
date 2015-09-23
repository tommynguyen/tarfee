<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SettingsController.php 10123 2013-12-11 17:29:35Z andres $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Payment_SettingsController extends Core_Controller_Action_User
{
  public function init()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id ) {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    } else {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );

    // Set up navigation
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
  }
  
  public function indexAction()
  {
    $user = Engine_Api::_()->core()->getSubject('user');

    // Check if they are an admin or moderator (don't require subscriptions from them)
    $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
    if( in_array($level->type, array('admin', 'moderator')) ) {
      $this->view->isAdmin = true;
      return;
    }
    
    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->packages = $packages = $packagesTable->fetchAll(array('enabled = ?' => 1, 'after_signup = ?' => 1));

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $this->view->currentSubscription = $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    if( $currentSubscription ) {
      $this->view->currentPackage = $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Get current gateway?
  }
  
 public function checkDiscountCode($code)
 {
    	$viewer = Engine_Api::_() -> user() -> getViewer();
	    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
	    $select = $inviteTable->select()
	      ->from($inviteTable->info('name'), 'COUNT(*)')
	      ->where('code = ?', trim($code))
		  ->where('active = 1')
		  ->where('discount_used = 0')
		  ->where('new_user_id = ?', $viewer -> getIdentity())
	      ;
	    return (bool) $select->query()->fetchColumn(0);
 }
  
  public function confirmAction()
  {
    // Process
    $user = Engine_Api::_()->core()->getSubject('user');

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array(
      'enabled = ?' => 1,
      'package_id = ?' => (int) $this->_getParam('package_id'),
    ));
	
	//check discount code
	$code = $this ->_getParam('discount');
	if(isset($code) && $this -> checkDiscountCode($code)) {
		//update discount for invite code
		$inviteCode = Engine_Api::_() -> invite() -> getRowCode(trim($code));
		if($inviteCode) {
			$_SESSION['ref_code'] = trim($code);
			$inviteCode -> discount_used = true;
			$inviteCode -> save();
		}
	}
	
    // Check if it exists
    if( !$package ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    $currentPackage = null;
    if( $currentSubscription ) {
      $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Check if they are the same
    if( $package->package_id == $currentPackage->package_id ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }


    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }




    // Cancel any other existing subscriptions
    Engine_Api::_()->getDbtable('subscriptions', 'payment')
      ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);
    

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray(array(
        'package_id' => $package->package_id,
        'user_id' => $user->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      ));
      $subscription->save();

      // If the package is free, let's set it active now and cancel the other
      if( $package->isFree() ) {
        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        if( $currentSubscription ) {
          $currentSubscription->cancel();
        }
      }

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    
    // Check if the subscription is ok
    if( $package->isFree() && $subscriptionsTable->check($user) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
    
    // Prepare subscription session
    $session = new Zend_Session_Namespace('Payment_Subscription');
    $session->is_change = true;
    $session->user_id = $user->getIdentity();
    $session->subscription_id = $subscription_id;

    // Redirect to subscription handler
    return $this->_helper->redirector->gotoRoute(array('controller' => 'subscription',
      'action' => 'gateway'));
  }


  public function contactUsAction()
  {
    // Process
    $user = Engine_Api::_()->core()->getSubject('user');
	// Make form
	$this -> view -> form = $form = new Payment_Form_Request();
    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array(
      'enabled = ?' => 1,
      'package_id = ?' => (int) $this->_getParam('package_id'),
    ));
	
	//check discount code
	$code = $this ->_getParam('discount');
	if(isset($code) && $this -> checkDiscountCode($code)) {
		//update discount for invite code
		$inviteCode = Engine_Api::_() -> invite() -> getRowCode(trim($code));
		if($inviteCode) {
			$_SESSION['ref_code'] = trim($code);
			$inviteCode -> discount_used = true;
			$inviteCode -> save();
		}
	}
	
    // Check if it exists
    if( !$package ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    $currentPackage = null;
    if( $currentSubscription ) {
      $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Check if they are the same
    if( $package->package_id == $currentPackage->package_id ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
	$arr_user = $user -> toArray();
	$arr_user['emailconf'] = $arr_user['email'];
	$displayName = $arr_user['displayname'];
	if($displayName)
	{
		$arrDisplayName = explode(' ', $displayName);
		if($arrDisplayName)
		{
			$arr_user['first_name'] = $arrDisplayName[0];
			$arr_user['last_name'] = $arrDisplayName[1];
		}
	}
    // Check method
    if( !$this->getRequest()->isPost() ) 
    {
    	if (isset($arr_user['country_id']))
		{
			$provincesAssoc = array();
			$country_id = $arr_user['country_id'];
			if ($country_id) 
			{
				$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
				$provincesAssoc = array('0'=>'') + $provincesAssoc;
			}
			$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
		}
		
		if (isset($arr_user['province_id']))
		{
			$citiesAssoc = array();
			$province_id = $arr_user['province_id'];
			if ($province_id) {
				$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
				$citiesAssoc = array('0'=>'') + $citiesAssoc;
			}
			$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
		}
		$form -> populate($arr_user);
      return;
    }
	
	// submit form
	$posts = $this -> getRequest() -> getPost();
	$provincesAssoc = array();
	$country_id = $posts['country_id'];
	if ($country_id) 
	{
		$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
		$provincesAssoc = array('0'=>'') + $provincesAssoc;
	}
	$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
	
	$citiesAssoc = array();
	$province_id = $posts['province_id'];
	if ($province_id) {
		$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
		$citiesAssoc = array('0'=>'') + $citiesAssoc;
	}
	$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
	// Check data
	if (!$form -> isValid($posts))
	{
		return;
	}
	
	$values = $form -> getValues();
	$table = Engine_Api::_()->getDbtable('membershiprequests', 'user');
    $request = $table->fetchRow($table->select()->where('email = ?', $values['email'])->where('approved = 0'));

    if($request) {
      $form->addError($this->view->translate('The request with this email is exists.'));
      return;
	}
	$db = $table->getAdapter();
	$db->beginTransaction();
    try 
    {
        $request = $table->createRow();
		$values['package_id'] = $this->_getParam('package_id');
		$values['user_id'] = $user -> getIdentity();
        $request->setFromArray($values);
        $request->save();
		
		$users_table = Engine_Api::_()->getDbtable('users', 'user');
	  	$users_select = $users_table->select()
  	    	->where('level_id = ?', 1)
	    	->where('enabled >= ?', 1);
	  	$super_admin = $users_table->fetchRow($users_select);
		
		$mailAdminType = 'notify_admin_user_upgrade';
		$mailAdminParams = array(
			'host' => $_SERVER['HTTP_HOST'],
			'sender_email' => $user->email,
			'date' => date("F j, Y, g:i a"),
			'recipient_title' => $super_admin->displayname,
			'sender_title' => $user -> displayname,
			'object_title' => $user->displayname,
			'object_link' => $user->getHref(),
		);
		Engine_Api::_()->getApi('mail', 'core')->sendSystem(
	         $super_admin,
	         $mailAdminType,
	         $mailAdminParams
      	);
		
        $db->commit();
		$form->addNotice($this->view->translate('The request sent successfully!'));
    }
    catch( Exception $e ) {
        $db->rollBack();
        throw $e;
    } 
  }

}