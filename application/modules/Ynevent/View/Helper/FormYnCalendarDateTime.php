<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FormCalendarDateTime.php 8217 2011-01-14 22:58:59Z char $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynevent_View_Helper_FormYnCalendarDateTime extends Zend_View_Helper_FormElement
{
  
  private static $_loaded = false;
  
  public function formYnCalendarDateTime($name, $value = null, $attribs = null,
      $options = null, $listsep = "<br />\n")
  {
    $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
    extract($info); // name, value, attribs, options, listsep, disable

    $dateFormat = 'mdy';

    // Get use military time
    if( isset($attribs['useMilitaryTime']) ) {
      $useMilitaryTime = $attribs['useMilitaryTime'];
      //unset($attribs['useMilitaryTime']);
    } else {
      $useMilitaryTime = true;
    }
    
    
    // Check value type
    if( is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})( (\d{2}):(\d{2})(:(\d{2}))?)?$/', $value, $m) ) {
      $tmpDateFormat = trim(str_replace(array('d', 'y', 'm'), array('-%3$s', '-%1$s', '-%2$s'), $dateFormat), '-');
      $value = array();
      
   
      // Get date
      $value['date'] = sprintf($tmpDateFormat, str_pad($m[1],2,'0',STR_PAD_LEFT), str_pad($m[2],2,'0', STR_PAD_LEFT), str_pad($m[3],2,'0',STR_PAD_LEFT));
      
      if( $value['date'] == '0-0-0' ) {
        unset($value['date']);
      }

      // Get time
      if( isset($m[6]) ) {
        $value['hour'] = $m[5];
        $value['minute'] = $m[6];
        if( !$useMilitaryTime ) {
          $value['ampm'] = ( $value['hour'] >= 12 ? 'PM' : 'AM' );
          if( 0 == (int) $value['hour'] ) {
            $value['hour'] = 12;
          } else if( $value['hour'] > 12 ) {
            $value['hour'] -= 12;
          }
        }
      }
    }

    if( !is_array($value) ) {
      $value = array();
    }
    


    // Prepare javascript
    
    // Prepare month and day names
    $localeObject = Zend_Registry::get('Locale');
    
    $months = Zend_Locale::getTranslationList('months', $localeObject);
    if($months['default'] == NULL) { $months['default'] = "wide"; }
    $months = $months['format'][$months['default']];

    $days = Zend_Locale::getTranslationList('days', $localeObject);
    if($days['default'] == NULL) { $days['default'] = "wide"; }
    $days = $days['format'][$days['default']];

    $calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
    $calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
    
    // Append files and script
    $dateId = $name . '-date';
    $toggleId = $name . '-toggle-date';
    
    $baseUrl = $this->view->baseUrl();
    
    if(!self::$_loaded){
        //$this->view->headScript()->appendFile($this->view->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/yncalendar.js');
        $this->view->headScript()->appendFile($baseUrl . '/application/modules/Ynevent/externals/scripts/yncalendar.js');
        self::$_loaded = true;
    }
    
    $this->view->headScript()->appendScript("en4.core.runonce.add(function() {
        new Picker.Date('{$dateId}', {
            startDay: 0,
            format: '%m-%d-%Y',
            pickerClass: 'datepicker_jqui',
            startView: 'days',
            draggable: false,
            yearPicker: false,
            invertAvailable: true,
            togglesOnly: false,
            toggle: $('{$toggleId}'),
        });
    });");
    
    if(isset($value['date']) && ($value['date'] == '00-00-0000' || $value['date'] == '0000-00-00')){
        $value['date'] = null;
    } 
    
    
    return
      '<div class="event_calendar_container" style="display:inline">' .
        $this->view->formText($name . '[date]', @$value['date'], array_merge(array('class' => 'calendar-date', 'id' => $dateId,'style'=>'width:120px'), (array) @$attribs['dateAttribs'])) .
        '<span class="yncalendar_button_span" id="'.$toggleId.'">&nbsp;'
        .'</span>' 
       . '</div>' 
      . $this->view->formYnTime($name, $value, $attribs, $options)
      ;
  }
}