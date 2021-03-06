<?php

class Payment_Form_Signup1_Subscription extends Engine_Form
{
  protected $_isSignup = true;
  
  protected $_packages;
  
  public function setIsSignup($flag)
  {
    $this->_isSignup = (bool) $flag;
  }
  
  public function init()
  {
    $this
      ->setTitle('Subscription Plan')
      ->setDescription('Please select a subscription plan from the list below.')
      ;

    // Get available subscriptions
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $packagesSelect = $packagesTable
      ->select()
      ->from($packagesTable)
      ->where('enabled = ?', true)
      ;

    if( $this->_isSignup ) {
      $packagesSelect->where('signup = ?', true);
    }
    else{
      $packagesSelect->where('after_signup = ?', true);
    }
	
	
    $multiOptions = array();
    $this->_packages = $packagesTable->fetchAll($packagesSelect);
    foreach( $this->_packages as $package ) {
      $multiOptions[$package->package_id] = $package->title
        . ' (' . $package->getPackageDescription() . ')'
        ;
    }
    
    // Element: package_id
    //if( count($multiOptions) > 1 ) {
      $this->addElement('Radio', 'package_id', array(
        'label' => 'Choose Plan:',
        'multiOptions' => $multiOptions,
      ));
    //}

    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
  
  public function getPackages()
  {
    return $this->_packages;
  }
  
  public function setPackages($packages)
  {
    $this->_packages = $packages;
  }
}