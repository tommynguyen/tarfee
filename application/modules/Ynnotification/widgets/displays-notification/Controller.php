<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Adv notification
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: displays notification
 * @author     Luan Nguyen
 */
class Ynnotification_Widget_DisplaysNotificationController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {  	  		  
  		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();	
  }
}