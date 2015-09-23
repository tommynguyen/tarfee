<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
class Sladvsubscription_Plugin_Menus
{
	public function showUpgrade($row)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		
	    if( $viewer->getIdentity() ) {
	    	$level = Engine_Api::_()->getItem('authorization_level', $viewer->level_id);
	    	if($level->type == 'admin' || $level->type == 'moderator' )
	    		return false;
	    		
	    	$packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
	   		$gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
	
	    	// Have any gateways or packages been added yet?
	    	if( $gatewaysTable->getEnabledGatewayCount() <= 0 ||
	       		$packagesTable->getEnabledNonFreePackageCount() <= 0 ) {
	      		return false;
	    	}
	    	
		    return array(
		        'label' => $row->label,
		        'route' => 'default',
		        'params' => array(
		          'controller' => 'settings',
		          'action' => 'index',
		      	  'module' => 'payment',
		        ),
		    );
	    }
	    
	    return false;
	}
}