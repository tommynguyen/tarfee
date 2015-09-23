<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2004 Younetco Developments
 * @author     LongL
 */

class Ynevent_Form_Review_Create extends Engine_Form
{
	protected  $_tab;
	protected  $_event;
	
	public function getTab()
	{
		return $this->_tab;
	}
	
	public function setTab($tab)
	{
		$this->_tab = $tab;
	}
	
	public function getEvent()
	{
		return $this->_event;
	}
	
	public function setEvent($event)
	{
		$this->_event = $event;
	}
	
	public function init()
	{
	    $this
	      ->setAttrib('id', 'ynevent_review_create')
	      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('id' => $this->_event->getIdentity(), 'tab' => $this->_tab), 'event_profile', true))
	      ->setAttrib('onsubmit', 'return checkReviewBody();')
	      ->setAttrib('class', '');
	    
	    // Review content body
	    $this->addElement('Textarea', 'body', array(
	      'label' => 'Write Review',
	      'filters' => array(
	        'StripTags',
	        new Engine_Filter_Censor(),
	      ),
	      'style' => "width:100%",
	    ));
	
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Submit',
	      'ignore' => true,
	      'type' => 'submit',
	      'decorators' => array(
	        'ViewHelper',
	      ),
	      'style' => 'margin-top:6px'
	    ));
	}
}