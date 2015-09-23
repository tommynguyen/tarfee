<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Create.php 6072 2010-06-02 02:36:45Z john $
 */

/**
 * @category   Application_Extensions
 * @package    Contactimporter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Contactimporter_Form_Import extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction($this)
      ->setTitle('Import Your Social Contact List')
      ->setDescription('')
      ->setAttrib('enctype', '')
	  ->setMethod('post');
      $this->addElement('text','email_box',array('label'=>'Email','required' => true,'allowEmpty'=> false));
      $this->addElement('password','password_box',array('label'=>'Password','required' => true,'allowEmpty'=> false));
     
	
  }
  public function add_provider($options)
  {
       $this->addElement('select','provider_box',array(
                              'label'=>'Email Provider',
                              'required' => false,
                              'allowEmpty'=> true,
                              'multiOptions' =>$options
                          ));
      $this->addElement('Button', 'import', array(
              'label' => 'Import Contact',
              'type' => 'submit',
                
            ));
  }
 
  
  
}