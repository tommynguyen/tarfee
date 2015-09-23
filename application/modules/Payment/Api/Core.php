<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Payment_Api_Core extends Core_Api_Abstract
{
  	public function checkValidCode($referCode) {
  		
  	  $isEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('user_referral_enable', 1);
	  $period = Engine_Api::_()->getApi('settings', 'core')->getSetting('user_referral_trial', 0);
	  $now =  date("Y-m-d H:i:s");
	  
  	  $invite = Engine_Api::_() -> invite() -> getRowCode($referCode);
	  
	  //check invite code active
	  if(isset($invite) && !$invite -> active) {
	  	 return false;	
	  }
	  
	  if($isEnabled && $invite)
	  {
	  	  //if exist code then get expire date
		  if($period == 1)
		  {
				$type = 'day';
	      }
		  else 
		  {
				$type = 'days';
		  }
		  $expiration_date = date_add(date_create($invite->timestamp),date_interval_create_from_date_string($period." ".$type));
		  
		  $nowDate = date_create($now);
		  
	  	  if($period != 0) 
	  	  {
	  	  	  if ($nowDate >= $expirationDate) 
			  {
			  	return true;
			  }
			  else
			  {
			  	return false;
			  }
	  	  }
		  else 
		  {
		  	   //if code never expired
	  	   		return true;
		  }
	  }
	  else 
	  {
	  	return false;
	  }
  	}
}