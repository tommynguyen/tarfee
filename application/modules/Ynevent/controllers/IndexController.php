<?php

class Ynevent_IndexController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('event', null, 'view') -> isValid())
			return;

		$id = $this -> _getParam('event_id', $this -> _getParam('id', null));
		if ($id)
		{
			$event = Engine_Api::_() -> getItem('event', $id);
			if ($event)
			{

				Engine_Api::_() -> core() -> setSubject($event);
			}
		}
	}
	
	// Upcoming events page
	public function browseAction()
	{
		// Landing page mode
		$this->_helper->content->setNoRender ()->setEnabled ();
	}
	
	// Past events page
	public function pastAction()
	{
		// Landing page mode
		$this->_helper->content->setNoRender ()->setEnabled ();
	}

	// Search events pages (listing page)
	public function listingAction()
	{
		// Render
		$this->_helper->content->setNoRender ()->setEnabled ();
	}

	public function followingAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		// Render
		$this -> _helper -> content
		-> setEnabled();

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$tableName = $table -> info('name');
		$values = $this ->_getAllParams();
		$select = $table -> select() -> from($tableName);
		if (!empty($values['text']))
		{
			$select -> where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
		}
		$followTable = Engine_Api::_()->getDbTable('follow', 'ynevent');
		$followTableName = $followTable -> info('name');
		$select -> joinLeft($followTableName, "$followTableName.resource_id = $tableName.event_id", "")
				-> where("$followTableName.user_id = ?", $viewer -> getIdentity())
				-> where("$followTableName.follow = 1");
		$select -> order('creation_date DESC');
		$select -> group('repeat_group');
		
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> text = (isset($values['text'])?$values['text']:"");
		$paginator -> setItemCountPerPage(20);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));

	}

	public function manageAction()
	{
		// Create form
		if (!$this -> _helper -> requireAuth() -> setAuthParams('event', null, 'edit') -> isValid())
			return;

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main',  array(), 'ynevent_main_manage');

		// Render
		$this -> _helper -> content
		-> setEnabled();

		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_quick');

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$table = Engine_Api::_() -> getDbtable('events', 'ynevent');
		$tableName = $table -> info('name');
		$values = $this ->_getAllParams();
		
		// Only mine
		if (@$values['view'] == 2)
		{
			$select = $table -> select() -> where('user_id = ?', $viewer -> getIdentity());
		}
		// All membership
		else
		{
			$membership = Engine_Api::_() -> getDbtable('membership', 'ynevent');
			$select = $membership -> getMembershipsOfSelect($viewer);
		}

		if (!empty($values['text']))
		{
			$select -> where("`{$tableName}`.title LIKE ?", '%' . $values['text'] . '%');
		}
		$select -> order('creation_date DESC');
		$select -> group('repeat_group');
		
		$this -> view -> paginator = $paginator = Zend_Paginator::factory($select);
		$this -> view -> text = (isset($values['text'])?$values['text']:"");

		$this -> view -> view = (isset($values['view'])?$values['view']:"");

		$paginator -> setItemCountPerPage(20);
		$paginator -> setCurrentPageNumber($this -> _getParam('page'));

		// Check create
		$this -> view -> canCreate = $canCreate = Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create');
	}

	public function createAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams('event', null, 'create') -> isValid())
			return;

		// Render
		$this -> _helper -> content	-> setEnabled();

		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main');

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$parent_type = $this -> _getParam('parent_type');
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('subject_id'));

		if ($parent_type == 'group' && Engine_Api::_() -> hasItemType('group'))
		{
			$this -> view -> group = $group = Engine_Api::_() -> getItem('group', $parent_id);
			if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'event') -> isValid())
			{
				return;
			}
		}
		else
		{
			$parent_type = 'user';
			$parent_id = $viewer -> getIdentity();
		}

		// Create form
		$this -> view -> parent_type = $parent_type;
		$this -> view -> gEndDate = Engine_Api::_() -> getApi('settings', 'core') -> getSetting("ynevent.day", "");
		$this -> view -> form = $form = new Ynevent_Form_Create( array(
			'parent_type' => $parent_type,
			'parent_id' => $parent_id
		));
		
		// Not post/invalid
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		
		$localeObject = Zend_Registry::get('Locale');
	    $dateLocaleString = $localeObject->getTranslation('short', 'Date', $localeObject);
	    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
	    $dateLocaleString = strtolower($dateLocaleString);
	    $dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('Y', 'm', 'd'), $dateLocaleString);
		$dateLocaleString = str_replace('  ', '/', $dateLocaleString);
		$dateLocaleString = str_replace('.', '/', $dateLocaleString);
		$dateLocaleString = str_replace('-', '/', $dateLocaleString);
		
		$date_default = trim(date($dateLocaleString));
		
		$language = $localeObject -> getLanguage();
		if($language == 'ar')
		{
			$date_default = date('d/m/Y');
		}
		
		$_post =  $this -> getRequest() -> getPost();
		$_post['repeatstartdate']['hour'] = 1;
		$_post['repeatstartdate']['minute'] = 10;
		$_post['repeatstartdate']['ampm'] = 'AM';
		$_post['repeatenddate']['hour'] = 1;
		$_post['repeatenddate']['minute'] = 20;
		$_post['repeatenddate']['ampm'] = 'AM';
		$_post['repeatstarttime']['date'] = $date_default;
		$_post['repeatendtime']['date'] = $date_default;
		if(!$_post['repeat_type'])
		{
			$_post['repeatstartdate']['date'] = $date_default;
			$_post['repeatenddate']['date'] = $date_default;
			
			$_post['repeatstarttime']['hour'] = 1;
			$_post['repeatstarttime']['minute'] = 10;
			$_post['repeatstarttime']['ampm'] = 'AM';
			$_post['repeatendtime']['hour'] = 1;
			$_post['repeatendtime']['minute'] = 20;
			$_post['repeatendtime']['ampm'] = 'AM';
			
			$_post['spec_start_date']['date'] = $date_default;
			$_post['spec_end_date']['date'] = $date_default;
			$_post['spec_start_date']['hour'] = 1;
			$_post['spec_start_date']['minute'] = 10;
			$_post['spec_start_date']['ampm'] = 'AM';
			$_post['spec_end_date']['hour'] = 1;
			$_post['spec_end_date']['minute'] = 20;
			$_post['spec_end_date']['ampm'] = 'AM';
		}
		else 
		{
			$_post['starttime']['date'] = $date_default;
			$_post['endtime']['date'] = $date_default;
			$_post['starttime']['hour'] = 1;
			$_post['starttime']['minute'] = 10;
			$_post['starttime']['ampm'] = 'AM';
			$_post['endtime']['hour'] = 1;
			$_post['endtime']['minute'] = 20;
			$_post['endtime']['ampm'] = 'AM';
			
			if($_post['repeat_frequency'] == 99)
			{
				$_post['repeatstartdate']['date'] = $date_default;
				$_post['repeatenddate']['date'] = $date_default;
				$_post['repeatstarttime']['hour'] = 1;
				$_post['repeatstarttime']['minute'] = 10;
				$_post['repeatstarttime']['ampm'] = 'AM';
				$_post['repeatendtime']['hour'] = 1;
				$_post['repeatendtime']['minute'] = 20;
				$_post['repeatendtime']['ampm'] = 'AM';
				if($_post['input_start_specifys'])
				{
					$_post['spec_start_date']['date'] = $date_default;
					$_post['spec_end_date']['date'] = $date_default;
					$_post['spec_start_date']['hour'] = 1;
					$_post['spec_start_date']['minute'] = 10;
					$_post['spec_start_date']['ampm'] = 'AM';
					$_post['spec_end_date']['hour'] = 1;
					$_post['spec_end_date']['minute'] = 20;
					$_post['spec_end_date']['ampm'] = 'AM';
				}
			}
			else 
			{
				$_post['spec_start_date']['date'] = $date_default;
				$_post['spec_end_date']['date'] = $date_default;
				$_post['spec_start_date']['hour'] = 1;
				$_post['spec_start_date']['minute'] = 10;
				$_post['spec_start_date']['ampm'] = 'AM';
				$_post['spec_end_date']['hour'] = 1;
				$_post['spec_end_date']['minute'] = 20;
				$_post['spec_end_date']['ampm'] = 'AM';
			}
		}

		// Location
		$provincesAssoc = array();
		$country_id = $_post['country_id'];
		if ($country_id) {
			$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
			$provincesAssoc = array('0'=>'') + $provincesAssoc;
		}
		$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
		
		$citiesAssoc = array();
		$province_id = $_post['province_id'];
		if ($province_id) {
			$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
			$citiesAssoc = array('0'=>'') + $citiesAssoc;
		}
		$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
		if (!$form -> isValid($_post))
		{
			return;
		}
		// Process
		$values = $form -> getValues();
		if(isset($_post['input_start_specifys']))
		{
			$values['input_start_specifys'] = $_post['input_start_specifys'];
			$values['input_end_specifys'] = $_post['input_end_specifys'];
			unset($_post);
		}
		
		//Start time of event
		$first_date = $values['starttime'];
		//End time of event
		$first_end_date = $values['endtime'];

		$maxInstance = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynevent.instance', 50);
		if ($maxInstance == '')
			$maxInstance = 50;
		$arrStart = $endStart = array();
		
		//If repeat
		if ($values['repeat_type'] == 1 && $values['repeat_frequency'] != 99)
		{
			//End repeat date
			$configDate = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynevent.day', '');
			
			if ($configDate != '' && $values['repeatenddate'] > $configDate)
			{
				$values['repeatenddate'] = $configDate;
			}
			
			$arr_temp = explode(' ', $values['repeatstarttime']);
			$repeat_start_time = $arr_temp['1'];
			
			$arr_temp = explode(' ', $values['repeatendtime']);
			$repeat_end_time = $arr_temp['1'];
			
			$repeat_start = strtotime($values['repeatstartdate']);
			$repeat_start = date('Y-m-d '.$repeat_start_time, $repeat_start);
			
			$repeat_end = strtotime($values['repeatenddate']);
			$repeat_end = date('Y-m-d '.$repeat_end_time, $repeat_end);
			
			if ($repeat_start > $repeat_end)
			{
				$form -> addError("Start date of the event must be less than the end repeat date");
				return;
			}
			
			// 1, 7, monthly~30
			$step = $values['repeat_frequency'];

			//Duration between starttime and endtime
			$duration = Engine_Api::_() -> ynevent() -> dateDiffBySec($values['repeatstarttime'], $values['repeatendtime']);

			//Start of repeat
			$loopstart = $repeat_start;
			$i = 1;
			//When start date still <= end repeat date
			while ($loopstart <= $repeat_end)
			{
				//If not monthly repeat
				if ($step != 30)
				{
					$arrStart[] = $loopstart;
					$loopstart = Engine_Api::_() -> ynevent() -> dateAdd($loopstart, $step);
				}
				else
				{
					$arrStart[] = $loopstart;
					$loopstart = Engine_Api::_() -> ynevent() -> monthAdd($repeat_start, $i);
					echo $day."</br>";
					$i++;
				}
			}
		}
		elseif ($values['repeat_type'] == 1 && $values['repeat_frequency'] == 99 )
		{
			
			if(!$values['input_start_specifys'])
			{
				if($values['spec_start_date'] >= $endStart[] = $values['spec_end_date'])
				{
					$arrStart[] = $values['spec_start_date'];
					$endStart[] = $values['spec_end_date'];
				}
			}
			else 
			{
				foreach($values['input_start_specifys'] as $id => $value)
				{
					$arrStart[] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $value)));
					$endStart[] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $values['input_end_specifys'][$id])));
				}
			}
		}		
		else
		{
			if ($first_date > $first_end_date)
			{
				$form -> addError("Start time of the event must be less than the end time.");
				return;
			}
			//Not repeat event
			$arrStart[] = $first_date;
		}
		
		if ($maxInstance <= count($arrStart))
		{
			$str = $this -> view -> translate(array(
				'You are allowed creating only %s event in the repeat chain.',
				'You are allowed creating only %s events in the repeat chain.',
				$maxInstance
			), $this -> view -> locale() -> toNumber($maxInstance));
			$form -> addError($str);
			return;
		}
		
		//Set value
		$values['user_id'] = $viewer -> getIdentity();
		$values['parent_type'] = $parent_type;
		$values['parent_id'] = $parent_id;
		
		if ($parent_type == 'group' && Engine_Api::_() -> hasItemType('group') && empty($values['host']))
		{
			$values['host'] = $group -> getTitle();
		}

		$db = Engine_Api::_() -> getDbtable('events', 'ynevent') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create event
			$table = Engine_Api::_() -> getDbtable('events', 'ynevent');

			//Generate repeat group value
			$values['repeat_group'] = microtime(true) * 10000;

			//type = 0 : not repeat
			//type = 1 : repeat
			$type = $values['repeat_type'];

			$repeat_order = 0;
			if (is_array($arrStart))
			{
				foreach ($arrStart as $key => $value)
				{
					$repeat_order++;
					$values['repeat_order'] = $repeat_order;

					//check maxinstance
					if ($maxInstance >= $repeat_order)
					{
						$event = $table -> createRow();

						//Set viewer time zone
						$oldTz = date_default_timezone_get();
						date_default_timezone_set($viewer -> timezone);
						$start = strtotime($values['starttime']);
						$end = strtotime($values['endtime']);
						date_default_timezone_set($oldTz);
						$values['starttime'] = date('Y-m-d H:i:s', $start);
						$values['endtime'] = date('Y-m-d H:i:s', $end);

						//Repeat
						if ($type == 1)
						{
							if($values['repeat_frequency'] != 99)
							{
								$values['starttime'] = $value;
								$values['endtime'] = Engine_Api::_() -> ynevent() -> dateAddBySec($value, $duration);
							}	
							else {
								$values['starttime'] = $value;
								$values['endtime'] = $endStart[$key];
							}
							
							$oldTz = date_default_timezone_get();
							date_default_timezone_set($viewer -> timezone);
							if($values['repeat_frequency'] != 99)
								$repeat_end = strtotime($repeat_end);
							$start = strtotime($values['starttime']);
							$end = strtotime($values['endtime']);
							date_default_timezone_set($oldTz);
							$values['starttime'] = date('Y-m-d H:i:s', $start);
							$values['endtime'] = date('Y-m-d H:i:s', $end);
							if($values['repeat_frequency'] != 99)
							{
								$repeat_end = date('Y-m-d H:i:s', $repeat_end);
								$values['end_repeat'] = $repeat_end;
							}

							$values['repeat_type'] = $values['repeat_frequency'];
						}

						$event -> setFromArray($values);
						$event -> save();

						// Add owner as member
						$event -> membership() -> addMember($viewer) -> setUserApproved($viewer) -> setResourceApproved($viewer);

						// Add owner rsvp
						$event -> membership() -> getMemberInfo($viewer) -> setFromArray(array('rsvp' => 2)) -> save();

						// Add photo
						if (!empty($values['photo']))
						{
							$event -> setPhoto($form -> photo);
						}
						// Add Cover photo
						if (!empty($values['cover_thumb']))
						{
							$event -> setCoverPhoto($form -> cover_thumb);
						}
						
						// Add fields
				        $customfieldform = $form->getSubForm('fields');
				        $customfieldform->setItem($event);
				        $customfieldform->saveValues();
						
						// Add tags
				      	$tags = preg_split('/[,]+/', $values['tags']);
				      	$event->tags()->addTagMaps($viewer, $tags);
						
						//sendnotify host
						
						if (strpos($values['host'], 'younetco_event_key_') !== FALSE) 
						{
							$user_id = substr($values['host'], 19, strlen($values['host']));
							if ($user_id != $viewer -> getIdentity()) {
								$friend = Engine_Api::_() -> getItem('user', $user_id);
				
								$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
								$notifyApi -> addNotification($friend, $viewer, $event, 'friend_host');
							}
						}
						
						//Add owner follow
						Engine_Api::_() -> ynevent() -> setEventFollow($event, $viewer);

						// Set auth
						$auth = Engine_Api::_() -> authorization() -> context;

						$roles = array(
								'owner',
								'member',
								'owner_member',
								'owner_member_member',
								'owner_network',
								'registered',
								'everyone'
							);
							
						if (empty($values['auth_view']))
						{
							$values['auth_view'] = 'everyone';
						}

						if (empty($values['auth_comment']))
						{
							$values['auth_comment'] = 'everyone';
						}
						$viewMax = array_search($values['auth_view'], $roles);
						$commentMax = array_search($values['auth_comment'], $roles);
						$photoMax = array_search($values['auth_photo'], $roles);
						$videoMax = array_search($values['auth_video'], $roles);

						foreach ($roles as $i => $role)
						{
							$auth -> setAllowed($event, $role, 'view', ($i <= $viewMax));
							$auth -> setAllowed($event, $role, 'comment', ($i <= $commentMax));
							$auth -> setAllowed($event, $role, 'photo', ($i <= $photoMax));
							$auth -> setAllowed($event, $role, 'video', ($i <= $videoMax));
						}
						$auth -> setAllowed($event, 'member', 'invite', $values['auth_invite']);
						//Add activity only one
						if ($repeat_order <= 1)
						{
							// Add action
							$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
							if ($values['parent_type'] == 'group')
							{
								$action = $activityApi -> addActivity($viewer, $group, 'advgroup_event_create');
							}
							else
							{
								$action = $activityApi -> addActivity($viewer, $event, 'ynevent_create');
							}

							if ($action)
							{
								$activityApi -> attachActivity($action, $event);
							}
						}
					}//end check maxinstance
				}//End foreach
			}
			
			// Commit
			$db -> commit();

			// Redirect
			if ($event -> parent_type == 'group')
			{
				$group = $event -> getParent('group');
				$this -> _redirectCustom($group);
			}
			else
			{
				$this -> _redirectCustom(array(
					'route' => 'event_general',
					'action' => 'manage'
				));
			}
			//return $this->_helper->redirector->gotoRoute(array('id' => $event->getIdentity()), 'event_profile', true);
		}
		catch (Engine_Image_Exception $e)
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function remindAction()
	{
		$remain = $this -> _getParam('remain_time');
		$event_id = $this -> _getParam('event_id');
		$table = Engine_Api::_() -> getDbTable('remind', "ynevent");

		$event = Engine_Api::_() -> getItem('event', $event_id);

		$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
		//Get all events in group repeat
		$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();
		//Remove series events
		foreach ($event_list as $objevent)
		{
			$table -> setRemindTime($objevent -> event_id, $user_id, $remain);
		}

	}

	public function rateAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$user_id = $viewer -> getIdentity();

		$rating = $this -> _getParam('rating');
		$event_id = $this -> _getParam('event_id');

		$table = Engine_Api::_() -> getDbtable('ratings', 'ynevent');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			Engine_Api::_() -> ynevent() -> setRating($event_id, $user_id, $rating);

			$event = Engine_Api::_() -> getItem('event', $event_id);
			$event -> rating = Engine_Api::_() -> ynevent() -> getRating($event -> getIdentity());
			$event -> save();

			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$total = Engine_Api::_() -> ynevent() -> ratingCount($event -> getIdentity());

		$data = array();
		$data[] = array(
			'total' => $total,
			'rating' => $rating,
		);
		return $this -> _helper -> json($data);
		$data = Zend_Json::encode($data);
		$this -> getResponse() -> setBody($data);
	}

	public function draw_calendar($month, $year, $events = array())
	{
		/* draw table */
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

		/* table headings */

		$sun = $this -> view -> translate("Sunday");
		$mon = $this -> view -> translate("Monday");
		$tue = $this -> view -> translate("Tuesday");
		$wed = $this -> view -> translate("Wednesday");
		$thu = $this -> view -> translate("Thursday");
		$fri = $this -> view -> translate("Friday");
		$sat = $this -> view -> translate("Saturday");
		$headings = array(
			$mon,
			$tue,
			$wed,
			$thu,
			$fri,
			$sat,
			$sun
		);

		$calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';
		$to_day = date('d');
		$cur_month = date('m');
		$cur_year = date('Y');
		/* days and weeks vars now ... */
		$running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
		$days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();
		if ($running_day == 0)
		{
			$running_day = 6;
		}
		else
		{
			$running_day = $running_day - 1;
		}
		/* row for week one */
		$calendar .= '<tr class="calendar-row">';

		/* print "blank" days until the first of the current week */
		for ($x = 0; $x < $running_day; $x++)
		{
			$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
			$days_in_this_week++;
		}
		$session = new Zend_Session_Namespace('mobile');
		/* keep going with days.... */
		for ($list_day = 1; $list_day <= $days_in_month; $list_day++)
		{
			$month1 = $month;
			$list_day1 = $list_day;
			if ($month < 10)
			{
				$month1 = '0' . $month;
			}
			if ($list_day < 10)
			{
				$list_day1 = '0' . $list_day;
			}

			$event_day = $year . '-' . $month1 . '-' . $list_day1;

			$count = 0;
			$showedViewMoreButton = false;
			$oldDay = $event_day;
			
			$href = $this -> view -> url(array(
					'action' => 'view-more',
					'selected_day' => $oldDay
				), 'event_general');
			$class_today = "";
			if($month == $cur_month && $list_day == $to_day && $year == $cur_year)
			{
				$class_today = 'today';
			}
			if (!$session -> mobile)
			{
				
				$calendar .= '<td class="calendar-day '.$class_today.'"><div style="height:100px;">';
				/* add in the day number */
				$calendar .= '<div class="day-number"><a href = "' . $href . '">' . $list_day . '</a></div>';
			}
			else
			{
				$calendar .= '<td class="calendar-day"><a href = "' . $href . '">';
					/* add in the day number */
				$calendar .= '<div class="day-number">' . $list_day . '</div>';
			}

			if (count($events))
			{
				foreach ($events as $event)
				{
					$startDateObject = new Zend_Date(strtotime($event -> starttime));
					if ($viewer && $viewer -> getIdentity())
					{
						$tz = $viewer -> timezone;
						$startDateObject -> setTimezone($tz);
					}
					$startDate = $startDateObject -> toString('yyyy-MM-dd');
					$event_time = $this -> view -> locale() -> toTime($startDateObject);

					if (strcmp($startDate, $event_day) == 0)
					{
						$flag = true;
						$count++;
						if ($count <= 3)
						{
							$href = $event -> getHref();
							$id = $event -> getIdentity();
							$startDateObject = new Zend_Date(strtotime($event -> starttime));
							if (!$session -> mobile)
							{
								$calendar .= '<a id="ynevent_myevent_' . $id . '" href="' . $event -> getHref() . '" class="ynevent">' . $event_time . "-" . $this -> view -> string() -> truncate($event -> title, 20) . '</a>';
							}
							else
							{
								$calendar .= '<span id="ynevent_myevent_' . $id . '" href="' . $event -> getHref() . '" class="ynevent">' . $event_time . "-" . $this -> view -> string() -> truncate($event -> title, 20) . '</span>';
							}
							$divTooltip = $this -> view -> partial('_calendar_tooltip.tpl', array('event' => $event));
							if (!$session -> mobile)
							{
								$calendar .= $divTooltip;
							}
							$calendar .= '<br>';
						}
						else
						{
							if (!($showedViewMoreButton))
							{
								$showedViewMoreButton = true;
								$oldDay = $startDate;
							}
						}
					}
					else
					{
						$count = 0;
						if ($showedViewMoreButton)
						{
							$showedViewMoreButton = false;
							if (!$session -> mobile)
							{
								$calendar .= '</div>' . $this -> view -> htmlLink($this -> view -> url(array(
									'action' => 'view-more',
									'selected_day' => $oldDay
								), 'event_general'), $this -> view -> translate('View more'), array(
									'style' => 'font-weight: bold;'
								)) . '</td>';
							}
						}
					}
				}
				if ($session -> mobile)
				{
					$calendar .= '</a></td>';
				}
			}

			$calendar .= '</div></td>';

			if ($running_day == 6)
			{
				$calendar .= '</tr>';
				if (($day_counter + 1) != $days_in_month)
				{
					$calendar .= '<tr class="calendar-row">';
				}
				$running_day = -1;
				$days_in_this_week = 0;
			}
			$days_in_this_week++;
			$running_day++;
			$day_counter++;
		}

		/* finish the rest of the days in the week */
		if ($days_in_this_week < 8 && $days_in_this_week > 1)
		{
			for ($x = 1; $x <= (8 - $days_in_this_week); $x++)
			{
				$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
			}
		}

		/* final row */
		$calendar .= '</tr>';

		/* end the table */
		$calendar .= '</table>';

		/** DEBUG * */
		$calendar = str_replace('</td>', '</td>' . "\n", $calendar);
		$calendar = str_replace('</tr>', '</tr>' . "\n", $calendar);

		/* all done, return result */
		return $calendar;
	}

	public function calendarAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer -> timezone);
		$month = (int)(isset($_GET['month']) ? $_GET['month'] : date('m'));
		$year = (int)(isset($_GET['year']) ? $_GET['year'] : date('Y'));
		date_default_timezone_set($oldTz);

		$search = Engine_Api::_() -> ynevent() -> getDateSearch($month, $year);

		$this -> view -> month = $month;
		$this -> view -> year = $year;

		$eventTable = Engine_Api::_() -> getDbTable("events", "ynevent");
		$events = $eventTable -> getMyEventsInMonth($viewer -> getIdentity(), $search[0], $search[1]);

		if (count($events))
		{
			$this -> view -> events = $events;
		}
		$calendar = $this -> draw_calendar($month, $year, $events);
		$this -> view -> calendar = $calendar;

		$this -> view -> enableTooltip = $enableTooltip = !Engine_Api::_() -> hasModuleBootstrap('ynfbpp');

		// Render
		$this -> _helper -> content
		-> setEnabled();

	}

	public function viewMoreAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main',  array(), 'ynevent_main_manage');
		//request for selected day
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if($viewer -> getIdentity() && !$this -> _getParam('repeat_group'))
		{
			$values['user_id'] = $viewer -> getIdentity();
		}
		
		if ($selected_day = $this -> _getParam('selected_day'))
		{
			$this -> view -> selected_day = $values['selected_day'] = $selected_day;
		}
		$eventTbl = Engine_Api::_() -> getItemTable('event');
		$this -> view -> events = $events = $eventTbl -> fetchAll($eventTbl -> getEventSelect($values));
	}

	public function getcategoriesAction()
	{
		$parent_id = $this -> _getParam('parent_id', $this -> _getParam('subject_id'));
		$table = Engine_Api::_() -> getDbtable('categories', 'ynevent');
		$parentNode = $table -> getNode($parent_id);
		$childs = $parentNode -> getChilren();
		$categories = array();
		if ($childs)
		{
			foreach ($childs as $index => $child)
			{
				$categories[$index]['id'] = $child -> category_id;
				$categories[$index]['title'] = $child -> title;
			}
		}
		$this -> view -> categories = $categories;
	}

	/**
	 * Add location + check google map
	 */
	public function addLocationAction()
	{
		$this -> view -> form = $form = new Ynevent_Form_addLocation();
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
	}

	public function eventBadgeAction()
	{
		$this -> _helper -> layout -> setLayout('default-simple');
		$event_id = $this -> _getParam('event_id');
		$this -> view -> status = $status = $this -> _getParam('status');
		$aStatus = str_split($status);
		$name = 0;
		$attending = 0;
		$led = 0;
		if (count($aStatus) == 3)
		{
			if ($aStatus[0] == '1')
				$name = 1;
			if ($aStatus[1] == '1')
				$attending = 1;
			if ($aStatus[2] == '1')
				$led = 1;
		}
		$this -> view -> name = $name;
		$this -> view -> attending = $attending;
		$this -> view -> led = $led;

		$event = Engine_Api::_() -> getItem('event', $event_id);
		if (!$event)
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> event = $event;
	}

	public function promoteCalendarAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// process timezone
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();

		//user time zone
		date_default_timezone_set($user_tz);

		$month = $this -> _getParam('month', null);
		$year = $this -> _getParam('year', null);
		if ($month == null || $year == null)
		{
			$date = date('Y-m-d');
		}
		else
			$date = ("{$year}-{$month}-15");
		$arr = explode('-', $date);
		$day = 0;
		$month = 0;
		$year = 0;

		if (count($arr) == 3)
		{
			$day = $arr[2];
			$month = $arr[1];
			$year = $arr[0];
		}

		if ($day > 31 || $day < 1)
		{
			$day = date('d');
		}

		if ($month < 1 || $month > 12)
		{
			$month = date('m');
		}

		$thisYear = (int)date('Y');

		if ($year < $thisYear - 9 || $year > $thisYear + 9)
		{
			$year = date('Y');
		}

		$this -> view -> day = $day;
		$this -> view -> month = $month;
		$this -> view -> year = $year;

		date_default_timezone_set($oldTz);

		$search = Engine_Api::_() -> ynevent() -> getDateSearch($month, $year);
		$eventTable = Engine_Api::_() -> getItemTable('event');
		//Get first date and last day in month server time zone
		$events = $eventTable -> getAllEventsInMonth($search[0], $search[1]);

		$showedEvents = array();
		$auth = Engine_Api::_() -> authorization() -> context;
		foreach ($events as $event)
		{
			if ($auth -> isAllowed($event, $viewer, 'view'))
			{
				array_push($showedEvents, $event);
			}
		}

		$event_count = array();
		$i = 0;
		if (count($showedEvents))
		{
			$eventDates = array();
			foreach ($showedEvents as $event)
			{
				$t_day = strtotime($event -> starttime);
				$oldTz = date_default_timezone_get();
				date_default_timezone_set($user_tz);
				$dateObject = date('Y-n-j', $t_day);
				date_default_timezone_set($oldTz);
				$event_count[$dateObject][] = $event -> event_id;
			}
			// date_default_timezone_set($oldTz);
			foreach ($event_count as $index => $evt)
			{
				$eventDates[$i]['day'] = $index;
				$eventDates[$i]['event_count'] = count($evt);
				$i++;
			}
			$this -> view -> numberOfEvents = count($eventDates);
			$this -> view -> eventDates = json_encode($eventDates);
		}

	}

	public function calendarBadgeAction()
	{
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		// process timezone
		$user_tz = date_default_timezone_get();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($viewer -> getIdentity())
		{
			$user_tz = $viewer -> timezone;
		}
		$oldTz = date_default_timezone_get();

		//user time zone
		date_default_timezone_set($user_tz);

		$month = $this -> _getParam('month', null);
		$year = $this -> _getParam('year', null);
		if ($month == null || $year == null)
		{
			$date = date('Y-m-d');
		}
		else
		{
			$date = ("{$year}-{$month}-15");
			$this -> view -> future = true;
		}

		$arr = explode('-', $date);
		$day = 0;
		$month = 0;
		$year = 0;

		if (count($arr) == 3)
		{
			$day = $arr[2];
			$month = $arr[1];
			$year = $arr[0];
		}

		if ($day > 31 || $day < 1)
		{
			$day = date('d');
		}

		if ($month < 1 || $month > 12)
		{
			$month = date('m');
		}

		$thisYear = (int)date('Y');

		if ($year < $thisYear - 9 || $year > $thisYear + 9)
		{
			$year = date('Y');
		}

		$this -> view -> day = $day;
		$this -> view -> month = $month;
		$this -> view -> year = $year;

		date_default_timezone_set($oldTz);

		$search = Engine_Api::_() -> ynevent() -> getDateSearch($month, $year);
		$eventTable = Engine_Api::_() -> getItemTable('event');
		//Get first date and last day in month server time zone
		$events = $eventTable -> getAllEventsInMonth($search[0], $search[1]);

		$showedEvents = array();
		$auth = Engine_Api::_() -> authorization() -> context;
		foreach ($events as $event)
		{
			if ($auth -> isAllowed($event, $viewer, 'view'))
			{
				array_push($showedEvents, $event);
			}
		}

		$event_count = array();
		$i = 0;
		if (count($showedEvents))
		{
			$eventDates = array();
			foreach ($showedEvents as $event)
			{
				$t_day = strtotime($event -> starttime);
				$oldTz = date_default_timezone_get();
				date_default_timezone_set($user_tz);
				$dateObject = date('Y-n-j', $t_day);
				date_default_timezone_set($oldTz);
				$event_count[$dateObject][] = $event -> event_id;
			}
			// date_default_timezone_set($oldTz);
			foreach ($event_count as $index => $evt)
			{
				$eventDates[$i]['day'] = $index;
				$eventDates[$i]['event_count'] = count($evt);
				$i++;
			}
			$this -> view -> numberOfEvents = count($eventDates);
			$this -> view -> eventDates = json_encode($eventDates);
		}
	}

	//view map view
	public function displayMapViewAction()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$eventTable = Engine_Api::_()->getItemTable('event');
		$eventTableName = $eventTable -> info('name');
		
		$eventIds = $this->_getParam('ids', '');
	    if ($eventIds != '')
	    {
	    	$eventIds = explode("_", $eventIds);
	    }
	    
	    $select = $eventTable -> select();
		
		if (is_array($eventIds) && count($eventIds))
		{
			$select -> where ("$eventTableName.event_id IN (?)", $eventIds);
		}
		else 
		{
			$select -> where ("0 = 1");
		}
		$events = $eventTable->fetchAll($select);
		
		$datas = array();
		$contents = array();
		$http = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://'	;
		$icon_clock = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/ynevent-maps-time.png';
		$icon_persion = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/ynevent-maps-person.png';
		$icon_star = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/ynevent-maps-close-black.png';
		$icon_home = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/ynevent-maps-location.png';
		
		foreach($events as $event)
		{
			if($event -> latitude)	
			{
					
				$icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/maker.png';
				if($event->featured)
				{
					$icon = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/feature_maker.png';
				}
				if(Engine_Api::_()->ynevent()->checkRated($event -> getIdentity(), $viewer->getIdentity()))
				{
					$icon_star = $http.$_SERVER['SERVER_NAME'].$this->view->baseUrl().'/application/modules/Ynevent/externals/images/ynevent-maps-close.png';
				}
					
				$datas[] = array(	
						'event_id' => $event -> getIdentity(),				
						'latitude' => $event -> latitude,
						'longitude' => $event -> longitude,
						'icon' => $icon
					);
				$startDateObject = new Zend_Date(strtotime($event->starttime));
            	if( $viewer && $viewer->getIdentity() ) {
            		$tz = $viewer->timezone;
            		$startDateObject->setTimezone($tz);
            	}
				$contents[] = '
					<div class="ynevent-maps-main" style="width: auto; min-height: 150px; ">	
						<div class="ynevent-maps-left" style="float: left; width: 100px; ">
							<a href="'.$event->getHref().'" class="thumb" target="_parent" style="text-decoration: none;">		        			
			        			<div class="ynevent-maps-image" style="background-image: url('.$event->getPhotoUrl('thumb.profile').'); background-size: cover; background-position: center; width: 100px; height: 120px; margin-bottom; 5px;"></div>
			        			<span class="ynevent-maps-view-event" style="margin-top: 3px; background-color: #629dbe; width: 100%; line-height: 28px; text-transform: uppercase; display: block; text-align: center; color: #fff;">View Event</span>
			        		</a>
		      			</div>
	      				<div class="ynevent-maps-content" style="overflow: hidden; padding-left: 10px; line-height: 20px;">
					        <a href="'.$event->getHref().'" class="ynevent-maps-title" style="color: #679ac0; font-weight: bold; font-size: 15px; text-decoration: none;" target="_parent">
					         	'.$event->getTitle().'
					        </a>
					        <div class="ynevent-maps-info">
					        	<span style="margin-right: 10px;">
					        		<img src="'.$icon_clock.'" style="margin-right: 3px; vertical-align: -2px;">'.$this -> view ->translate('%1$s %2$s',
						            $this -> view ->locale()->toDate($startDateObject),
						            $this -> view ->locale()->toTime($startDateObject)
						          	).'
								</span>
								<span style="margin-right: 10px;">
									<img src="'.$icon_persion.'" style="margin-right: 3px; vertical-align: -2px;" />
									'.$this->view->translate(array('%s guest', '%s guests',$event->member_count), $event->member_count).'
								</span>
								<span style="margin-right: 10px;">
									<img src="'.$icon_star.'" style="margin-right: 3px; vertical-align: -2px;" />
									'.number_format($event->rating, 1).'
								</span>
					        </div>
					        <div class="ynevent-maps-location">
					            <img src="'.$icon_home.'" style="margin-right: 3px; vertical-align: -2px;">
					            <span>'.$event->getFullAddress().'</span>
					        </div>
	        				<div class="ynevent-maps-description" style="border-top: 1px solid #555; margin-top: 5px;">
					        	'.$this->view->string()->truncate($this->view->string()->stripTags($event->description), 300).'
					        </div>
			      		</div>
					</div>
				';
			}
		}
		echo $this ->view -> partial('_map_view.tpl', 'ynevent',array('datas'=>Zend_Json::encode($datas), 'contents' => Zend_Json::encode($contents)));
		exit();
	}
	
	public function getMyLocationAction()
	{
		$latitude = $this->_getParam('latitude');
		$longitude = $this->_getParam('longitude');
		$values  =  file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
		echo $values;die;
	}

}
