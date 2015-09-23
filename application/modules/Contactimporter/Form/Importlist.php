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
class Contactimporter_Form_Importlist extends Engine_Form
{
  public function init()
  {
    $this
      ->setAction($this->he)
      ->setTitle('Your Contacts')
      ->setDescription('')
      ->setAttrib('enctype', '');
	  
//   /* $this->addElement('Textarea', 'body', array(
//      'label' => 'Message',
//      //'required' => true,
//      //'allowEmpty' => false,
//    ));
//    
//    $this->addElement('File', 'Filedata', array(
//      'label' => 'Add a Photo!',
//      'destination' => APPLICATION_PATH.'/public/temporary/',
//      'multiFile' => 1,
//      'validators' => array(
//        array('Count', false, 1),
//        array('Size', false, 612000),
//        array('Extension', false, 'jpg,jpeg,png,gif'),
//      ),
//    ));
//
//    $this->addElement('Button', 'submit', array(
//      'label' => 'Create Contact Importer!',
//      'type' => 'submit',
//    ));        \application\modules\Contactimporter\Api\openinviter
//    $this->addElement('checkbox',"Hello",array('label'=>'Do you Like It','style'=>'color:red;border:1px solid'));*/
	  
	  $inviter=new OpenInviter();
		$oi_services=$inviter->getPlugins();
		
		$options = array();
		foreach ($oi_services as $type=>$providers)	
			{
			
			foreach ($providers as $provider=>$details)
				$options[$provider]="{$details['name']}";
			
			}
		
      $this->addElement('text','email_box',array('label'=>'Email','required' => true,'allowEmpty'=> false));
      $this->addElement('password','password_box',array('label'=>'Password','required' => true,'allowEmpty'=> false));
      $this->addElement('select','provider_box',array(
      						'label'=>'Email Provider',
      						'required' => true,
      						'allowEmpty'=> false,
      						'multiOptions' =>$options
      					));
      $this->addElement('Button', 'import', array(
		      'label' => 'Import Contact',
		      'type' => 'submit',
      		  
		    ));
  }
  
  function addfield($contact)
  {
  	 foreach ($contact as $email=>$name)
  	 {
  	 	
  	 }
  }
}