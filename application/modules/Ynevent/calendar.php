<?php
$request = $_REQUEST;

$viewer = Engine_Api::_() -> user() -> getViewer();
$oldTz = date_default_timezone_get();
$userTz = $oldTz;

if ($viewer -> getIdentity())
{
    $userTz = $viewer -> timezone;
}

date_default_timezone_set($userTz);

$month = isset($_REQUEST['month'])?$_REQUEST['month']:date('m');
$year = isset($_REQUEST['year'])?$_REQUEST['year']:date('Y');

date_default_timezone_set($oldTz);
$search = Engine_Api::_() -> ynevent() -> getDateSearch($month, $year);
$eventTable = Engine_Api::_() -> getItemTable('event');
list($fromdate, $todate) = $search;

//Get first date and last day in month server time zone
$events = $eventTable -> getGeneralCalendar($fromdate, $todate);

$result = array('timezoneOffset'=>Engine_Api::_() -> ynevent()->getTimezoneOffset(), 'events' => $events -> toArray(),'month'=>$month, 'year'=>$year,'total'=>count($events));
echo Zend_Json::encode($result);
