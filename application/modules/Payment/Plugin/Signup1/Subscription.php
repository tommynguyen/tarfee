<?php
class Payment_Plugin_Signup1_Subscription extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';

  //protected $_title = 'Choose Subscription Plan';

  protected $_formClass = 'Payment_Form_Signup1_Subscription';

  protected $_script = array('_signupSubscription1.tpl', 'payment');

  public function init()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $session = $this->getSession();
    if( $request->getParam('package_id') &&
        (empty($session->data) || empty($session->data['package_id'])) ) {
      if( !isset($session->data) ) {
        $session->data = array();
      }
      $session->data['package_id'] = $request->getParam('package_id');
      if( $request->getParam('package_skip') ) {
        $session->active = false;
      }
    }
  }

  public function getForm()
  {
    if( null === $this->_form ) {
      $form = parent::getForm();
      // @todo gateway and package check
      $this->_form = $form;
    }
    return $this->_form;
  }

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    return parent::onSubmit($request);
  }

  public function onProcess()
  {
    // In this case, the step was placed before the account step.
    // Register a hook to this method for onUserCreateAfter
    if( !$this->_registry->user ) {
      // Register temporary hook
      Engine_Hooks_Dispatcher::getInstance()->addEvent('onUserCreateAfter', array(
        'callback' => array($this, 'onProcess'),
      ));
      return;
    }
    $user = $this->_registry->user;


    // Actual processing
    
    // Get selected package
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
	
	if(empty($this->getSession()->data['package_id'])){
		//@TODO if skip will select plan ~~ free
		$this->getSession()->data['package_id'] = Engine_Api::_() -> user() -> getDefaultPackageId();
	}
    $package = $packagesTable->find($this->getSession()->data['package_id'])->current();
    if( !$package ) {
      throw new Engine_Exception('No subscription plan');
    }

	if($package -> getIdentity() != Engine_Api::_() -> user() -> getDefaultPackageId()) {
		//if not free plan set code is used
	    // Get codes
        $referCode = $_SESSION['ref_code'];
		if(isset($referCode) && !is_null($referCode) && !empty($referCode))
	    {
			$isValid = Engine_Api::_() -> payment() -> checkValidCode($referCode);
		    if($isValid) {
				$inviteRow = Engine_Api::_() -> invite() -> getRowCode($referCode);
				if($inviteRow) {
					$inviteRow -> discount_used = true;
					$inviteRow -> save(); 
				}
		    }
		}
	}



    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');

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
        //if( $currentSubscription ) {
        //  $currentSubscription->cancel();
        //}
      }

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }


    // Check if the subscription is ok
    if( $package->isFree() && $subscriptionsTable->check($user) ) {
      return;
    }

    // Prepare subscription session
    $session = new Zend_Session_Namespace('Payment_Subscription');
    $session->is_change = true;
    $session->user_id = $user->getIdentity();
    $session->subscription_id = $subscription_id;
  }

  public function onAdminProcess($form)
  {
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'Payment_Plugin_Signup_Subscription'));
    $step_row->enable = $form->getValue('enable');
    $step_row->save();
  }
}