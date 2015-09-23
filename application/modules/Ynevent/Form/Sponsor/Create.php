<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 7659 2010-10-19 02:24:28Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynevent_Form_Sponsor_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Sponsor')
      ->setAttrib('id', 'ynevent_sponsor_create')
      ->setAttrib('class', 'global_form_popup');
      //->setAttrib('onsubmit', 'reloadSponsor()');
      
	//Name
    $this->addElement('Text', 'name', array(
      'label' => 'Sponsor Name',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
      'validators' => array(
        array('StringLength', true, array(1, 64)),
      ),
      'style' => "width:250px",
    ));
	
    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Main Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    
    // Link
    $this->addElement('Text', 'url', array(
      'label' => 'Link',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
      'style' => "width:250px",
    ));
    
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
      'style' => "width:250px",
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Create',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

   	$this->addElement('Cancel', 'cancel', array(
	      'prependText' => ' or ',
	      'label' => 'Cancel',
	      'link' => true,
	      'href' => '',
	      'onclick' => 'parent.Smoothbox.close();',
	      'decorators' => array(
	        'ViewHelper'
	      ),
	    ));
		    
	$this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}