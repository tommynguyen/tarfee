<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Fields.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Signup_Fields extends Fields_Form_Standard
{
  protected $_fieldType = 'user';

  public function init()
  {
    // Init form
    $this->setTitle('Profile Information');

    $this
      ->setIsCreation(true)
      ->setItem(Engine_Api::_()->user()->getUser(null));
    parent::init();
	
	$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
	$countriesAssoc = array('0'=>'') + $countriesAssoc;
	
	$request = Zend_Controller_Front::getInstance()->getRequest();
	
	$provincesAssoc = array();
	$country_id = $request->getParam('country_id', 0);
	if ($country_id) {
		$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
		$provincesAssoc = array('0'=>'') + $provincesAssoc;
	}
		
	$this->addElement('Select', 'country_id', array(
		'label' => 'Country',
		'multiOptions' => $countriesAssoc,
		'value' => $country_id
	));
	
	$citiesAssoc = array();
	$province_id = $request->getParam('province_id', 0);
	if ($province_id) {
		$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
		$citiesAssoc = array('0'=>'') + $citiesAssoc;
	}
	
	$this->addElement('Select', 'province_id', array(
		'label' => 'Province/State',
		'multiOptions' => $provincesAssoc,
		'value' => $province_id
	));
	
	$city_id = $request->getParam('city_id', 0);
	$this->addElement('Select', 'city_id', array(
		'label' => 'City',
		'multiOptions' => $citiesAssoc,
		'value' => $city_id
	));
	
	$continent = '';
	$country = Engine_Api::_()->getItem('user_location', $country_id);
	if ($country) $continent = $country->getContinent();
	$this->addElement('Text', 'continent', array(
		'label' => 'Continent',
		'value' => $continent,
		'disabled' => true
	));	
  }
}