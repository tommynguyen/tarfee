<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */
class Sladvsubscription_Plugin_Core extends Core_Model_Abstract
{
	public function onRenderLayoutDefault($event)
	{
		$view = $event->getPayload();
	    if( !($view instanceof Zend_View_Interface) ) {
	    	return;
	    }
	    $result = Engine_Api::_()->socialloft()->validate_license('advsubscription');			
        if ($result['RESULT'] != 'OK')
        	return;
        	
    	$style = '.core_mini_advsubscription {'.Engine_Api::_()->sladvsubscription()->getStyle('menu').'}';
    	$view->headStyle()->appendStyle($style);
	}
	
	public function onAuthorizationLevelCreateAfter($event)
	{
		$level = $event->getPayload();
	    if( !($level instanceof Authorization_Model_Level) ) {
	    	return;
	    }
	    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
	    $max = $table->select()
		    ->from($table->info('name'), new Zend_Db_Expr('MAX(`order`)'))
		      ->query()
		      ->fetchColumn(0)
		      ;
		if ($max) 
		{
			$level->order = $max + 1;
			$level->save();
		}
	}
}