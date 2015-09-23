<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */

class Sladvsubscription_AdminSubscriptionController extends Core_Controller_Action_Admin
{
	public function compareAction()
	{
		$this->view->levels = $levels = Engine_Api::_()->sladvsubscription()->getLevels();
	    $this->view->feature = Engine_Api::_()->getApi('settings', 'core')->getSetting('advsubscription.popular', '0');		
	    $comparesTbl = Engine_Api::_()->getDbtable('compares', 'sladvsubscription');
	    $compares = $comparesTbl->fetchAll();
	    $array_compares = array();
	    if (count($compares))
	    {
	    	foreach ($compares as $compare)
	    	{
	    		$array_compares[]=array('title'=>$compare->compare_name,'package'=>Zend_Json::decode($compare->compare_value));
	    	}
	    }
	    $this->view->check = false;
	    $this->view->array_compares = $array_compares = Engine_Api::_()->sladvsubscription()->getCompares();
	    
	    if(!$this->getRequest()->isPost())
	    	return;
	    $values = $this->_getParam('compare');	    

	    if (!is_array($values))
	    	return;
	    	
	    	
	    unset($values[99999999]);

	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    
	    try {
	    	$db->beginTransaction();
	    	
		    $values = array_values($values);
		    if (count($compares))
		    {
		    	$i = 0;
		    	foreach ($compares as $compare)
		    	{
		    		if (isset($values[$i]))
		    		{
		    			$compare->compare_name = $values[$i]['title'];
		    			$compare->compare_value = Zend_Json::encode($values[$i]['package']);
		    			$compare->save();
		    			unset($values[$i]);
		    		}
		    		else 
		    		{
		    			$compare->delete();
		    		}
		    		$i++;
		    	}
		    }
		    if (count($values))
		    	$values = array_values($values);
		    foreach ($values as $value)
		    {
		    	$row = $comparesTbl->createRow();
		    	$row->compare_name = $value['title'];
		    	$row->compare_value = Zend_Json::encode($value['package']);
		    	$row->save();
		    }
		    $this->view->feature = $this->_getParam('feature');
		    Engine_Api::_()->getApi('settings', 'core')->setSetting('advsubscription.popular', $this->_getParam('feature'));
		    
		    $db->commit();
	    }catch (Exception $e)
	    {
	    	$db->rollBack();
	    	throw $e;
	    }
	    $values = $this->_getParam('compare');
	    unset($values[99999999]);
	    $this->view->array_compares = array_values($values);
	    $this->view->check = true;
	}
}