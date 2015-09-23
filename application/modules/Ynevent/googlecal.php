<?php
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');

require_once APPLICATION_PATH . '/application/modules/Ynevent/Libs/google-api-php-client/src/Google_Client.php';
require_once APPLICATION_PATH . '/application/modules/Ynevent/Libs/google-api-php-client/src/contrib/Google_CalendarService.php';

$view = Zend_Registry::get("Zend_View");

$tz = date_default_timezone_get();
$viewer = Engine_Api::_() -> user() -> getViewer();
if( $viewer->getIdentity() ) {
	$tz = $viewer->timezone;
}

$client = new Google_Client();
$cal = new Google_CalendarService($client);

if (isset($_GET['error']) && $_GET['error']=='access_denied')
{
	$_SESSION['google_calendar_message'] = Zend_Registry::get("Zend_Translate")->_("Error occurred!");
	$url = $view->url(array('id' => $event_id), 'event_profile', true);
	header('location: ' . $url); exit;
}

if (isset($_GET['code']))
{
    $client->authenticate($_GET['code']);
    $_SESSION['accesstoken'] = $client->getAccessToken();
    $url =  '?'. http_build_query(array('m'=>'lite','module'=>'ynevent','name'=>'googlecal'));
    header('location: ' . $url);
}

if (isset($_SESSION['accesstoken']))
{
    $client->setAccessToken($_SESSION['accesstoken']);
}

if ($client->getAccessToken())
{
    $event_id = $_GET['event_id'];
	if (!is_numeric($event_id))
	{
		$_SESSION['google_calendar_message'] = Zend_Registry::get("Zend_Translate")->_("This event is not existed!");
		$url = $view->url(array('id' => $event_id), 'event_profile', true);
		header('location: ' . $url); exit;
	}
    
    $seevent = Engine_Api::_()->getItem('event', $event_id);
    if (is_null($seevent))
    {
    	$_SESSION['google_calendar_message'] = Zend_Registry::get("Zend_Translate")->_("This event is not existed!");
    	$url = $view->url(array('id' => $event_id), 'event_profile', true);
		header('location: ' . $url); exit;
    }
    
    #Create Google Event
    $event = new Google_Event();
	
    #Set Summary
    $event->setSummary(html_entity_decode($seevent->getTitle(), ENT_QUOTES, 'UTF-8'));

    #Set Description
    $sDescription = strip_tags(html_entity_decode($seevent->description, ENT_QUOTES, 'UTF-8'));
    $sEventUrl = $seevent->getHref();
    $event->setDescription($sDescription);

    #Set Location
    $sLocation = html_entity_decode($seevent->location, ENT_QUOTES, 'UTF-8');
    if ($seevent->address != '')
    {
        $sLocation .= ', ' . html_entity_decode($seevent->address, ENT_QUOTES, 'UTF-8');
    }
    if ($seevent->city != '')
    {
        $sLocation .= ', ' . html_entity_decode($seevent->city, ENT_QUOTES, 'UTF-8');
    }
    $event->setLocation($sLocation);
    
    $strStart = Engine_Api::_()->ynevent()->getDateStringForCalendar($seevent->starttime);
    $strEnd = Engine_Api::_()->ynevent()->getDateStringForCalendar($seevent->endtime);
    
    #Set Start time
    $start = new Google_EventDateTime();
    $start->setDateTime($strStart);
    $event->setStart($start);
 
    #Set End time
    $end = new Google_EventDateTime();
    $end->setDateTime($strEnd);
    $event->setEnd($end);

    #Get Calendar list
    $calendarList = $cal->calendarList->listCalendarList();

    #Check main Calendar
    if (count($calendarList))
    {
        foreach ($calendarList['items'] as $calendar)
        {
            if ($calendar['accessRole'] == 'owner')
            {
                #Post event to Google Calendar
                $cal->events->insert($calendar['id'], $event);
            }
        }
    }
    $_SESSION['accesstoken'] = $client->getAccessToken();
    
    #Redirect to event
    $_SESSION['google_calendar_message'] = Zend_Registry::get("Zend_Translate")->_("Added successfully!");
	$url = $view->url(array('id' => $event_id), 'event_profile', true);
	header('location: ' . $url); exit;
}
else
{
    $authUrl = $client->createAuthUrl();
    header("Location: " . $authUrl);
    die;
}