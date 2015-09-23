<?php
class User_Model_Recommendation extends Core_Model_Item_Abstract {
    protected $_type = 'user_recommendation';
    protected $_searchTriggers = false;
    
    public function getGivenDate() {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $givenDate = new Zend_Date(strtotime($this->given_date));
        $givenDate->setTimezone($timezone);
        return $givenDate;
    }
}
