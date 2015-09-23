<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 6588 2010-06-25 02:40:45Z steve $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Contactimporter_Form_Admin_Apisettings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Facebook Api Settings')
     ->setDescription('USER_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION');
     
	$description = $this->getTranslator()->translate('USER_ADMIN_SETTINGS_FACEBOOK_DESCRIPTION');
    $settings = Engine_Api::_()->getApi('settings', 'core');
	if( $settings->getSetting('user.support.links', 0) == 1 ) {
	$moreinfo = $this->getTranslator()->translate( 
        '<br>More Info: <a href="http://www.socialengine.net/support/documentation/article?q=166&question=Admin-Panel---Settings--Facebook-Integration" target="_blank"> KB Article</a>');
	} else {
	$moreinfo = $this->getTranslator()->translate( 
        '');
	}
	$description = vsprintf($description.$moreinfo, array(
      'http://www.facebook.com/developers/apps.php',
    ));
    $this->setDescription($description);


    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	 
    $this->addElement('Text', 'FBKey', array(
          'label' => 'Facebook APP ID',
          'size'=>80,
          'style'=>'width:400px'
    ));
     $this->addElement('Text', 'FBSecret', array(
          'label' => 'Facebook APP Secret',
          'size'=>80,
          'style'=>'width:400px'
    ));
    // Add submit button
    $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
    ));
  }

  public function saveValues()
  {
    $values   = $this->getValues();
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $settings->invite_message =  $values['message'];
 }
 public function setValue()
 {
     
 }
}