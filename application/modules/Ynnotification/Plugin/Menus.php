<?php
/**
 */
class Ynnotification_Plugin_Menus
{
  public function onMenuInitialize_UserSettingsSoundNotification($row) 
  {
  
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.setting') ) {
      return true;
    }
    return false;
  }
}