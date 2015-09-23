<?php
class Ynevent_EventController extends Core_Controller_Action_Standard 
{
	public function init() 
	{
		$id = $this -> _getParam('event_id', $this -> _getParam('id', null));
		if ($id) {
			$event = Engine_Api::_() -> getItem('event', $id);
			if ($event) {
				Engine_Api::_() -> core() -> setSubject($event);
			}
		}
	}

	private function setNotify(Ynevent_Model_Event $event, $viewer, $type = '', $event_next = NULL) 
	{
		try 
		{
			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($event) as $action) {
				$actionTable -> resetActivityBindings($action);
			}

			//$db->commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		//send notify for users following this event
		$followTable = Engine_Api::_() -> getDbtable('follow', 'ynevent');
		$follows = $followTable -> getUserFollow($event -> event_id);
		if (count($follows) > 0) {

			$friends = array();
			foreach ($follows as $follow) {
				if ($follow -> user_id != $viewer -> user_id) {
					$friends[] = Engine_Api::_() -> getItem('user', $follow -> user_id);
				}
			}
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($friends as $friend) {
				if ($type == 'ynevent_delete') {
					$notifyApi -> addNotification($friend, $viewer, $friend, $type, array("ynevent_title" => $event -> getTitle()));

				} else if ($type == 'ynevent_edit_delete') {
					$notifyApi -> addNotification($friend, $viewer, $event_next, $type, array("ynevent_title" => $event -> getTitle()));

				} else {
					$notifyApi -> addNotification($friend, $viewer, $event, $type);
					//$type = 'ynevent_change_details'
				}

			}
		}
	}

	private function setAuth(Ynevent_Model_Event $event, $values) {
		// Process privacy
		$auth = Engine_Api::_() -> authorization() -> context;

		if ($event -> parent_type == 'group') {
			$roles = array('owner', 'member', 'parent_member', 'registered', 'everyone');
		} else {
			$roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		}

		$viewMax = array_search($values['auth_view'], $roles);
		$commentMax = array_search($values['auth_comment'], $roles);
		$photoMax = array_search($values['auth_photo'], $roles);

		foreach ($roles as $i => $role) {
			$auth -> setAllowed($event, $role, 'view', ($i <= $viewMax));
			$auth -> setAllowed($event, $role, 'comment', ($i <= $commentMax));
			$auth -> setAllowed($event, $role, 'photo', ($i <= $photoMax));
		}

		$rolesVideo = array('owner', 'member', 'parent_member', 'registered', 'everyone');
		$videoMax = array_search($values['auth_video'], $rolesVideo);
		foreach ($rolesVideo as $i => $r) {
			$auth -> setAllowed($event, $r, 'video', ($i <= $videoMax));
		}
		$auth -> setAllowed($event, 'member', 'invite', $values['auth_invite']);
	}

	public function inviteAction() {

		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('event') -> isValid())
			return;
		// @todo auth
		// Prepare data
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> event = $event = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> friends = $friends = $viewer -> membership() -> getMembers();

		// Prepare form
		$this -> view -> form = $form = new Ynevent_Form_Invite();

		$count = 0;
		foreach ($friends as $friend) {
			if ($event -> membership() -> isMember($friend, null))
				continue;
			$form -> users -> addMultiOption($friend -> getIdentity(), $friend -> getTitle());
			$count++;
		}
		$this -> view -> count = $count;
		// Not posting
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		$values = $form -> getValues();
		// Process
		$table = $event -> getTable();
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try {
			$usersIds = $form -> getValue('users');
			if ($form -> getElement('message')) {
				$message = $form -> getElement('message') -> getValue();
			}
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			foreach ($friends as $friend) {
				if (!in_array($friend -> getIdentity(), $usersIds)) {
					continue;
				}

				$event -> membership() -> addMember($friend) -> setResourceApproved($friend);

				if (isset($message) && !empty($message)) {
					$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite_message', array('message' => $message));
				} else {
					$notifyApi -> addNotification($friend, $viewer, $event, 'ynevent_invite');
				}
			}

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		return $this -> _forward('success', 'utility', 'core', array('messages' => array(Zend_Registry::get('Zend_Translate') -> _('Members invited')), 'layout' => 'default-simple', 'parentRefresh' => true, ));
	}

	public function styleAction() {
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'style') -> isValid())
			return;

		$user = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> core() -> getSubject('event');

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Style();

		// Get current row
		$table = Engine_Api::_() -> getDbtable('styles', 'core');
		$select = $table -> select() -> where('type = ?', 'event') -> where('id = ?', $event -> getIdentity()) -> limit(1);

		$row = $table -> fetchRow($select);

		// Check post
		if (!$this -> getRequest() -> isPost()) {
			$form -> populate(array('style' => (null === $row ? '' : $row -> style)));
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}

		// Cool! Process
		$style = $form -> getValue('style');

		// Save
		if (null == $row) {
			$row = $table -> createRow();
			$row -> type = 'event';
			$row -> id = $event -> getIdentity();
		}

		$row -> style = $style;
		$row -> save();

		$this -> view -> draft = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.');
		$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRefresh' => false, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))));
	}

	public function deleteAction() {

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('event', $this -> getRequest() -> getParam('event_id'));

		if (!$this -> _helper -> requireAuth() -> setAuthParams($event, null, 'delete') -> isValid())
			return;

		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');

		// Make form
		$this -> view -> form = $form = new Ynevent_Form_Delete();

		if (!$event) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Event doesn't exists or not authorized to delete");
			return;
		}
		$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
		//Get all events in group repeat
		$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);
		if ($event -> repeat_type == 0 || count($event_list) < 2) {
			$form -> setDescription('Are you sure you want to delete this event?');
			$form -> removeElement('apply_for');
		}

		if (!$this -> getRequest() -> isPost()) {
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$values = $this -> getRequest() -> getPost();

		$db = $event -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try {

			$apply_for = $values['apply_for'];

			//Only current event
			if ($apply_for == 0)
			{
				// Add notify
				$this -> setNotify($event, $viewer, 'ynevent_delete');
				//Delete Current Event
				$event -> delete();
			}

			//For all repeating events
			if ($apply_for == 1)
			{
				//Get DbTable Events
				$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
				//Get all events in group repeat
				$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);

				//Remove series events
				foreach ($event_list as $objevent) {

					// Add notify
					$this -> setNotify($objevent, $viewer, 'ynevent_delete');
					$objevent -> delete();
				}
			}

			//For following events
			if ($apply_for == 2) 
			{
				//Get DbTable Events
				$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
				//Get all events in group repeat
				$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);
				//Remove series events
				foreach ($event_list as $objevent) {
					if ($objevent -> repeat_order >= $event -> repeat_order) {

						// Add notify
						$this -> setNotify($objevent, $viewer, 'ynevent_delete');

						$objevent -> delete();
					}
				}
			}

			$db -> commit();
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The selected event has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'event_general', true), 'messages' => Array($this -> view -> message)));
	}

	public function promoteAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$event = Engine_Api::_() -> getItem('event', $this -> getRequest() -> getParam('event_id'));
		if (!$event) {
			return $this -> _helper -> requireAuth -> forward();
		}
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		// Make form
		$this -> view -> event = $event;
	}

	public function editAction() 
	{
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynevent_main');
		$event_id = $this -> getRequest() -> getParam('event_id');
		$event = Engine_Api::_() -> getItem('event', $event_id);

		//Keep info to check changing
		if (is_object($event)) 
		{
			$event_temp = clone $event;
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!($this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid() || $event -> isOwner($viewer))) {
			return;
		}

		// Create form
		$this -> view -> gEndDate = Engine_Api::_() -> getApi('settings', 'core') -> getSetting("ynevent.day", "");

		$this -> view -> form = $form = new Ynevent_Form_Edit( array('parent_type' => $event -> parent_type, 'parent_id' => $event -> parent_id, 'item' => $event));

		//Populate Tag
		$tagStr = '';
		foreach ($event->tags()->getTagMaps() as $tagMap) {
			$tag = $tagMap -> getTag();
			if (!isset($tag -> text))
				continue;
			if ('' !== $tagStr)
				$tagStr .= ', ';
			$tagStr .= $tag -> text;
		}
		$form -> populate(array('tags' => $tagStr, ));
		
		$this -> view -> confirm_apply_for = false;
		if(isset($_POST['apply_for_action']))
		{
			$this -> view -> confirm_apply_for = true;
			$form -> populate(array('apply_for_action' => $_POST['apply_for_action']));
		}
		// Repeat event can not change repeat type
		if ($event -> repeat_type) 
		{
			$form -> repeat_type -> setAttrib('disable', true);
			$form -> repeat_frequency -> setAttrib('disable', true);
			$form -> removeElement('repeatstarttime');
			$form -> removeElement('repeatendtime');
			$form -> removeElement('repeatstartdate');
			$form -> removeElement('repeatenddate');
			
			if($event -> repeat_type == 99)
			{
				$apply_for = $_POST['apply_for_action'];
				if($apply_for > 0)
				{
					$form -> removeElement('starttime');
					$form -> removeElement('endtime');
					//Get DBTable Events
					$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
					//Get all events in group repeat
					$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);
					$order = 6;
					foreach($event_list as $objEvent)
					{
						if($apply_for == 2 && $objEvent -> repeat_order < $event -> repeat_order)
						{
							continue;
						}
						$event_id = $objEvent -> getIdentity();
						$form ->addElement('Heading', 'starttime_heading_'. $event_id, array(       
				        	'order' => $order + 1,
				            'label' => $this -> view -> translate("Event "). $objEvent -> repeat_order,
				            'decorators' => array(
				                'ViewHelper',
				                array('Label', array('tag' => 'span')),
				                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
				            ),
				        ));
						$start_time = strtotime($objEvent -> starttime);
						$end_time = strtotime($objEvent -> endtime);
						$oldTz = date_default_timezone_get();
						date_default_timezone_set($viewer -> timezone);
						$start_time = date('Y-m-d H:i:s', $start_time);
						$end_time = date('Y-m-d H:i:s', $end_time);
						date_default_timezone_set($oldTz);
						
						// Start time
					    $start = new Engine_Form_Element_CalendarDateTime('starttime_'.$event_id);
					    $start->setAllowEmpty(false);
						$start->setLabel("Start Date");
						$start -> setOrder($order + 2);
						$start -> setValue($start_time);
					    $form->addElement($start);
						
					    // End time
					    $end = new Engine_Form_Element_CalendarDateTime('endtime_'.$event_id);
					    $end->setAllowEmpty(false);
						$end->setLabel("End Date");
						$end -> setOrder($order + 3);
						$end -> setValue($end_time);
					    $form->addElement($end);
						$order = $order + 3;
					}
					$form ->addElement('Heading', 'starttime_heading_addmore', array(       
				        	'order' => $order + 1,
				            'label' => "Add More specify",
				            'decorators' => array(
				                'ViewHelper',
				                array('Label', array('tag' => 'span')),
				                array('HtmlTag2', array('class' => 'form-wrapper-heading'))
				            ),
			        ));
				}
				else 
				{
					$form -> removeElement('spec_start_date');
					$form -> removeElement('spec_end_date');
					$form -> removeElement('specify_repeat');
				}
			}
			else 
			{
				$form -> removeElement('spec_start_date');
				$form -> removeElement('spec_end_date');
				$form -> removeElement('specify_repeat');
			}
			
			// Add check form when save 1 repeat event
			$this -> view -> formcheck = $formcheck = new Ynevent_Form_Check();
		}

		// Populate auth
		$auth = Engine_Api::_() -> authorization() -> context;

		$roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

		foreach ($roles as $role) 
		{
			if (isset($form -> auth_view -> options[$role]) && $auth -> isAllowed($event, $role, 'view')) 
			{
				$form -> auth_view -> setValue($role);
			}
			if (isset($form -> auth_comment -> options[$role]) && $auth -> isAllowed($event, $role, 'comment')) {
				$form -> auth_comment -> setValue($role);
			}
		}
		$form -> auth_invite -> setValue($auth -> isAllowed($event, 'member', 'invite'));

		// Sub category
		$eventArray = $event -> toArray();
		$st_address = "";
		if ($eventArray['address'] != '')
			$st_address .= $eventArray['address'];

		if ($eventArray['city'] != '')
			$st_address .= "," . $eventArray['city'];

		if ($eventArray['country'] != '')
			$st_address .= "," . $eventArray['country'];

		if ($eventArray['zip_code'] != '')
			$st_address .= "," . $eventArray['zip_code'];
		$pos = strpos($st_address, ",");
		if ($pos === 0)
			$st_address = substr($st_address, 1);

		$eventArray['full_address'] = $st_address;
		$form -> populate($eventArray);

		// Convert and re-populate times
		$start = strtotime($event -> starttime);
		$end = strtotime($event -> endtime);
		$oldTz = date_default_timezone_get();
		date_default_timezone_set($viewer -> timezone);
		$start = date('Y-m-d H:i:s', $start);
		$end = date('Y-m-d H:i:s', $end);
		date_default_timezone_set($oldTz);

		$form -> populate(array('starttime' => $start, 'endtime' => $end));
		$form -> populate(array('f_repeat_type' => $event -> repeat_type, 'g_repeat_type' => $event -> repeat_type));

		if ($event -> repeat_type == 0) 
		{
			$rp_type = 0;
			$req = 0;
		} 
		else 
		{
			$rp_type = 1;
			switch($event->repeat_type) 
			{
				case 1 :
					$req = 1;
					break;
				case 7 :
					$req = 7;
					break;
				case 30 :
					$req = 30;
					break;
				case 99 :
					$req = 99;
					break;
				default :
					$req = 1;
					break;
			}
		}

		$form -> populate(array('repeat_type' => $rp_type, 'repeat_frequency' => $req));
		$this -> view -> repeat_type = $event -> repeat_type;
		
		if (isset($eventArray['country_id']))
		{
			$provincesAssoc = array();
			$country_id = $eventArray['country_id'];
			if ($country_id) 
			{
				$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
				$provincesAssoc = array('0'=>'') + $provincesAssoc;
			}
			$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
		}
		
		if (isset($eventArray['province_id']))
		{
			$citiesAssoc = array();
			$province_id = $eventArray['province_id'];
			if ($province_id) {
				$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
				$citiesAssoc = array('0'=>'') + $citiesAssoc;
			}
			$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
		}
		
		if (!isset($_POST['save_change']))
		{
			//Keep info to check changing
			return;
		}
		$_post = $this -> getRequest() -> getPost();
		
		$provincesAssoc = array();
		$country_id = $_post['country_id'];
		if ($country_id) 
		{
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
		
		if (!isset($_post['repeat_type'])) 
		{
			$_post['repeat_type'] = $rp_type;
		}
		if (!isset($_post['repeat_frequency'])) 
		{
			$_post['repeat_frequency'] = $req;
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
			if ($event_temp -> repeat_type == 0) 
			{
				$_post['starttime']['date'] = $date_default;
				$_post['endtime']['date'] = $date_default;
				$_post['starttime']['hour'] = 1;
				$_post['starttime']['minute'] = 10;
				$_post['starttime']['ampm'] = 'AM';
				$_post['endtime']['hour'] = 1;
				$_post['endtime']['minute'] = 20;
				$_post['endtime']['ampm'] = 'AM';
			}
			
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
		if (!$form -> isValid($_post)) 
		{
			return;
		}
		
		// Process
		$values = $form -> getValues();
		if ($values['repeat_type'] == "") 
		{
			$values['repeat_type'] = $event -> repeat_type;
		}
		
		if ($_post['input_start_specifys']) 
		{
			$values['input_start_specifys'] = $_post['input_start_specifys'];
			$values['input_end_specifys'] = $_post['input_end_specifys'];
			unset($_post);
		}

		if (!empty($values['sub_category_id']) && $values['sub_category_id'] > 0) 
		{
			$values["category_id"] = $values['sub_category_id'];
		}

		if (strpos($values['host'], 'younetco_event_key_') !== FALSE) 
		{
			$user_id = substr($values['host'], 19, strlen($values['host']));
			$owner = $event -> getOwner();
			if ($user_id != $owner -> getIdentity() && $user_id != substr($event -> host, 19, strlen($event -> host))) {
				$friend = Engine_Api::_() -> getItem('user', $user_id);

				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				$notifyApi -> addNotification($friend, $owner, $event, 'friend_host');
			}
		}

		// Check parent
		if (!isset($values['host']) && $event -> parent_type == 'group' && Engine_Api::_() -> hasItemType('group'))
		{
			$group = Engine_Api::_() -> getItem('group', $event -> parent_id);
			$values['host'] = $group -> getTitle();
		}

		// Process
		$db = Engine_Api::_() -> getItemTable('event') -> getAdapter();
		$db -> beginTransaction();

		try 
		{
			$values['parent_type'] = $event -> parent_type;
			$values['parent_id'] = $event -> parent_id;
			$values['end_repeat'] = ($values['repeatend'] == "0000-00-00") ? "" : $values['repeatend'];
			$values['user_id'] = $event -> user_id;
			// Set event info
			if ($values['repeat_type'] == 1) 
			{
				$values['repeat_type'] = $values['repeat_frequency'];
			}
			$copyvalues = $values;

			// Convert times to server time
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start1 = strtotime($copyvalues['starttime']);
			$end1 = strtotime($copyvalues['endtime']);
			date_default_timezone_set($oldTz);
			$copyvalues['starttime'] = date('Y-m-d H:i:s', $start1);
			$copyvalues['endtime'] = date('Y-m-d H:i:s', $end1);
			$event -> setFromArray($copyvalues);
			
			// 2: Apply for following repeating events
			// 1: Apply for all repeating events
			// 0: Apply for only current event
			// not care with case following events , just only or all
			// if change basic info (for only and all)
			// if change start_date, end_date (for only and all events)
			// if change repeat information (not for only and only for all)
			$apply_for = ($values['apply_for_action'] == "") ? 0 : $values['apply_for_action'];
			
			//Have change all
			$is_change = false;
			// Change relate repeat
			$is_repeat_change = false;

			//Start time of event
			$first_date = $values['starttime'];
			//End time of event
			$first_end_date = $values['endtime'];
			
			//Maxinstance
			$maxInstance = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynevent.instance', 50);
			if ($maxInstance == '')
				$maxInstance = 50;

			//Redirect Event
			$redirectEvent = NULL;

			if ($event_temp != $event) 
			{
				$is_change = true;
				
				//check basic or repeating change
				if ($values['repeat_type'] != 0 && $event_temp -> repeat_type == 0) 
				{
					$is_repeat_change = true;
				}
			}
			if ($is_change) 
			{
				//Up to repeat event
				if ($is_repeat_change)
				{
					$_start_temp = null;
					if($values['repeat_frequency'] != 99)
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
						$_start_temp = $repeat_start;
						
						$oldTz = date_default_timezone_get();
						date_default_timezone_set($viewer -> timezone);
						$start_update = strtotime($repeat_start);
						$end_update = strtotime(Engine_Api::_() -> ynevent() -> dateAddBySec($repeat_start, $duration));
						date_default_timezone_set($oldTz);
						$event -> starttime = date('Y-m-d H:i:s', $start_update);
						$event -> endtime = date('Y-m-d H:i:s', $end_update);
						$event -> save();
						
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
								$i++;
							}
						}
					}
					else 
					{
						$event -> starttime = $event_temp -> starttime;
						$event -> endtime = $event_temp -> endtime;
						$event -> save();
						
						foreach($values['input_start_specifys'] as $id => $value)
						{
							$arrStart[] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $value)));
							$endStart[] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $values['input_end_specifys'][$id])));
						}
					}
					if ($maxInstance <= count($arrStart)) 
					{
						$str = "You are allowed creating only {$maxInstance} in the repeat event chain.";
						$form -> addError($str);
						return;
					}

					// Create event
					$table = Engine_Api::_() -> getDbtable('events', 'ynevent');

					//Set repeat group
					$values['repeat_group'] = $event_temp -> repeat_group;
					$repeat_order = 1;
					
					if (is_array($arrStart)) 
					{
						foreach ($arrStart as $key => $value) 
						{
							if($_start_temp != $value)
							{
								$repeat_order ++;
								$values['repeat_order'] = $repeat_order;
	
								//create new row
								$new_event = $table -> createRow();
	
								if($values['repeat_frequency'] != 99)
								{
									$values['starttime'] = $value;
									$values['endtime'] = Engine_Api::_() -> ynevent() -> dateAddBySec($value, $duration);
								}	
								else 
								{
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
								
								$new_event -> setFromArray($values);
								$new_event -> save();
	
								if ($redirectEvent == NULL)
									$redirectEvent = $new_event;
	
								// Add owner as member
								$new_event -> membership() -> addMember($viewer) -> setUserApproved($viewer) -> setResourceApproved($viewer);
	
								// Add owner rsvp
								$new_event -> membership() -> getMemberInfo($viewer) -> setFromArray(array('rsvp' => 2)) -> save();
	
								// Add photo
								if (!empty($values['photo'])) {
									$new_event -> setPhoto($form -> photo);
								}
								// Add Cover photo
								if (!empty($values['cover_thumb'])) {
									$new_event -> setCoverPhoto($form -> cover_thumb);
								}
								//Set custom fields
								$customfieldform = $form -> getSubForm('fields');
								$customfieldform -> setItem($new_event);
								$customfieldform -> saveValues();
								
								// Add tags
								$tags = preg_split('/[,]+/', $values['tags']);
								$new_event -> tags() -> setTagMaps($viewer, $tags);
								//Add owner follow
								Engine_Api::_() -> ynevent() -> setEventFollow($new_event, $viewer);
	
								// Process privacy
								$this -> setAuth($new_event, $values);
	
								//Add activity only one
								if ($repeat_order <= 1) {
									// Add action
									$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
	
									$action = $activityApi -> addActivity($viewer, $new_event, 'ynevent_create');
	
									if ($action) 
									{
										$activityApi -> attachActivity($action, $new_event);
									}
								}
							}
						}//End foreach create events

					}//End is_array($arrStart)
				}
				//Basic info change
				else 
				{
					//Only current event
					if ($apply_for == 0) 
					{
						if ($first_date > $first_end_date)
						{
							$form -> addError("Start time of the event must be less than the end time.");
							return;
						}
									
						// change basic information for currents event
						if ($values['repeat_type'] == 0)
							$event -> repeat_group = microtime(true) * 10000;
						$event -> save();

						//Set redirect event
						if ($redirectEvent == NULL)
							$redirectEvent = $event;

						// Edit photo
						if (!empty($values['photo'])) 
						{
							$event -> setPhoto($form -> photo);
						}
						// Add Cover photo
						if (!empty($values['cover_thumb'])) 
						{
							$event -> setCoverPhoto($form -> cover_thumb);
						}
						//Set custom fields
						$customfieldform = $form -> getSubForm('fields');
						$customfieldform -> setItem($event);
						$customfieldform -> saveValues();
						
						// Add tags
						$tags = preg_split('/[,]+/', $values['tags']);
						$event -> tags() -> setTagMaps($viewer, $tags);
						// Process privacy
						$this -> setAuth($event, $values);
						// Add notify
						$this -> setNotify($event, $viewer, 'ynevent_change_details');
					}
					//For all repeating events
					if ($apply_for == 1) 
					{
						//Get DBTable Events
						$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
						//Get all events in group repeat
						$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);
						
						if($values['repeat_frequency'] != 99)
						{
							$arr_temp = explode(' ', $values['starttime']);
							$repeat_start_time = $arr_temp['1'];
							
							$arr_temp = explode(' ', $values['endtime']);
							$repeat_end_time = $arr_temp['1'];
							if ($repeat_start_time > $repeat_end_time)
							{
								$form -> addError("Start time of the event must be less than the end time.");
								return;
							}
							foreach ($event_list as $objevent) 
							{
								$repeat_start = strtotime($objevent -> starttime);
								$values['starttime'] = date('Y-m-d '.$repeat_start_time, $repeat_start);
								
								$repeat_end = strtotime($objevent -> endtime);
								$values['endtime'] = date('Y-m-d '.$repeat_end_time, $repeat_end);
								
								$oldTz = date_default_timezone_get();
								date_default_timezone_set($viewer -> timezone);
								$start = strtotime($values['starttime']);
								$end = strtotime($values['endtime']);
								date_default_timezone_set($oldTz);
								$values['starttime'] = date('Y-m-d H:i:s', $start);
								$values['endtime'] = date('Y-m-d H:i:s', $end);
								
								$objevent -> setFromArray($values);
								$objevent -> save();
	
								// Edit photo
								if (!empty($values['photo']))
								{
									$objevent -> setPhoto($form -> photo);
								}
								// Add Cover photo
								if (!empty($values['cover_thumb'])) 
								{
									$objevent -> setCoverPhoto($form -> cover_thumb);
								}
								//Set custom fields
								$customfieldform = $form -> getSubForm('fields');
								$customfieldform -> setItem($objevent);
								$customfieldform -> saveValues();
								
								// Add tags
								$tags = preg_split('/[,]+/', $values['tags']);
								$objevent -> tags() -> setTagMaps($viewer, $tags);
								
								// Process privacy
								$this -> setAuth($objevent, $values);
	
								// Add notify
								$this -> setNotify($objevent, $viewer, 'ynevent_change_details');
							}
						}
						else
						{
							foreach ($event_list as $objevent) 
							{
								//Set viewer time zone
								$oldTz = date_default_timezone_get();
								date_default_timezone_set($viewer -> timezone);
								$start = strtotime($values['starttime_'.$objevent->getIdentity()]);
								$end = strtotime($values['endtime_'.$objevent->getIdentity()]);
								date_default_timezone_set($oldTz);
								$values['starttime'] = date('Y-m-d H:i:s', $start);
								$values['endtime'] = date('Y-m-d H:i:s', $end);
								
								if ($start > $end)
								{
									$element = $form -> getElement("starttime_heading_".$objevent->getIdentity()) -> addError("Start date of the event must be less than the end date.");
									return;
								}
								
								$objevent -> setFromArray($values);
								$objevent -> save();
	
								// Edit photo
								if (!empty($values['photo']))
								{
									$objevent -> setPhoto($form -> photo);
								}
								// Add Cover photo
								if (!empty($values['cover_thumb'])) 
								{
									$objevent -> setCoverPhoto($form -> cover_thumb);
								}
								//Set custom fields
								$customfieldform = $form -> getSubForm('fields');
								$customfieldform -> setItem($objevent);
								$customfieldform -> saveValues();
								
								// Add tags
								$tags = preg_split('/[,]+/', $values['tags']);
								$objevent -> tags() -> setTagMaps($viewer, $tags);
								// Process privacy
								$this -> setAuth($objevent, $values);
	
								// Add notify
								$this -> setNotify($objevent, $viewer, 'ynevent_change_details');
							}
						}
					}
					//For following events
					if ($apply_for == 2) 
					{
						//Get DBTable Events
						$tblEvents = Engine_Api::_() -> getDbTable('events', 'ynevent');
						//Get all events in group repeat
						$event_list = $tblEvents -> getRepeatEvent($event -> repeat_group);
						
						if($values['repeat_frequency'] != 99)
						{
							$arr_temp = explode(' ', $values['starttime']);
							$repeat_start_time = $arr_temp['1'];
							
							$arr_temp = explode(' ', $values['endtime']);
							$repeat_end_time = $arr_temp['1'];
							if ($repeat_start_time > $repeat_end_time)
							{
								$form -> addError("Start time of the event must be less than the end time.");
								return;
							}
							foreach ($event_list as $objevent) 
							{
								if ($objevent -> repeat_order >= $event -> repeat_order) 
								{
									$repeat_start = strtotime($objevent -> starttime);
									$values['starttime'] = date('Y-m-d '.$repeat_start_time, $repeat_start);
									
									$repeat_end = strtotime($objevent -> endtime);
									$values['endtime'] = date('Y-m-d '.$repeat_end_time, $repeat_end);
									$objevent -> setFromArray($values);
									$objevent -> save();
									// Edit photo
									if (!empty($values['photo'])) {
										$objevent -> setPhoto($form -> photo);
									}
									// Add Cover photo
									if (!empty($values['cover_thumb'])) {
										$objevent -> setCoverPhoto($form -> cover_thumb);
									}
									//Set custom fields
									$customfieldform = $form -> getSubForm('fields');
									$customfieldform -> setItem($objevent);
									$customfieldform -> saveValues();
									
									// Add tags
									$tags = preg_split('/[,]+/', $values['tags']);
									$objevent -> tags() -> setTagMaps($viewer, $tags);
									// Process privacy
									$this -> setAuth($objevent, $values);
	
									// Add notify
									$this -> setNotify($objevent, $viewer, 'ynevent_change_details');
								}
							}//End Event list
						}
						else
						{
							foreach ($event_list as $objevent) 
							{
								if ($objevent -> repeat_order >= $event -> repeat_order) 
								{
									//Set viewer time zone
									$oldTz = date_default_timezone_get();
									date_default_timezone_set($viewer -> timezone);
									$start = strtotime($values['starttime_'.$objevent->getIdentity()]);
									$end = strtotime($values['endtime_'.$objevent->getIdentity()]);
									date_default_timezone_set($oldTz);
									$values['starttime'] = date('Y-m-d H:i:s', $start);
									$values['endtime'] = date('Y-m-d H:i:s', $end);
									
									if ($start > $end)
									{
										$element = $form -> getElement("starttime_heading_".$objevent->getIdentity()) -> addError("Start date of the event must be less than the end date.");
										return;
									}
									
									$objevent -> setFromArray($values);
									$objevent -> save();
		
									// Edit photo
									if (!empty($values['photo']))
									{
										$objevent -> setPhoto($form -> photo);
									}
									// Add Cover photo
									if (!empty($values['cover_thumb'])) 
									{
										$objevent -> setCoverPhoto($form -> cover_thumb);
									}
									//Set custom fields
									$customfieldform = $form -> getSubForm('fields');
									$customfieldform -> setItem($objevent);
									$customfieldform -> saveValues();
									
									// Add tags
									$tags = preg_split('/[,]+/', $values['tags']);
									$objevent -> tags() -> setTagMaps($viewer, $tags);
									// Process privacy
									$this -> setAuth($objevent, $values);
		
									// Add notify
									$this -> setNotify($objevent, $viewer, 'ynevent_change_details');
								}
							}
						}
					}//End apply_for == 2

				}//Basic info change

			}//End is_change

			// Commit
			$db -> commit();

		} catch (Engine_Image_Exception $e) {
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		} catch (Exception $e) {
			$db -> rollBack();
			throw $e;
		}

		// Redirect
		if ($this -> _getParam('ref') === 'profile' && $redirectEvent != NULL) {
			$this -> _redirectCustom($redirectEvent);

		}
		if ($event -> parent_type == 'group')
		{
			$group = $event -> getParent('group');
			$this -> _redirectCustom($group);
		} 
		else {
			$this -> _redirectCustom(array('route' => 'event_general', 'action' => 'manage'));
		}
	}

	public function transferAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this -> view -> event_id = $event_id = $this -> _getParam('event_id');
		$event = Engine_Api::_() -> getItem('event', $event_id);

		if (!$event) {
			return $this -> _helper -> requireSubject -> forward();
		}

		if (!$viewer -> isAdmin() && !$event -> isOwner($viewer)) {
			return $this -> _helper -> requireAuth -> forward();
		}

		$this -> view -> form = $form = new Ynevent_Form_Transfer;

		if (!$this -> getRequest() -> getPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		//Process
		$values = $form -> getValues();
		$db = Engine_Api::_() -> getDbtable('events', 'ynevent') -> getAdapter();
		$db -> beginTransaction();
		$member = Engine_Api::_() -> user() -> getUser($values['toValues']);
		if (!$event -> membership() -> isMember($member)) {
			return $this -> _forward('success', 'utility', 'core', array('closeSmoothbox' => true, 'parentRefresh' => true, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('That user is not a guest of this event.')), ));
		}

		try {
			$event -> user_id = $values['toValues'];
			$event -> parent_type = 'user';
			$event -> parent_id = $values['toValues'];
			$event -> save();

			// Add action
			$activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
			$action = $activityApi -> addActivity($member, $event, 'ynevent_transfer');

			//Add notification
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			$notifyApi -> addNotification($member, $viewer, $event, 'ynevent_transfer');

			$db -> commit();
		} catch(Exception $e) {
			$db -> rollback();
			throw $e;
		}
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile) {
			$callbackUrl = $this -> view -> url(array('id' => $event -> getIdentity()), 'event_profile', true);
			$this -> _forward('success', 'utility', 'core', array('smoothboxClose' => true, 'parentRedirect' => $callbackUrl, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new event owner had been set.'))));
		} else {
			return $this -> _forward('success', 'utility', 'core', array('closeSmoothbox' => true, 'parentRefresh' => true, 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new event owner had been set.')), ));
		}
	}

	public function friendsAction() {
		$viewer = Engine_Api::_() -> user() -> getViewer();

		if (!$viewer -> getIdentity()) {
			$data = null;

		} else {
			$text = $this -> _getParam('value', '');

			$friends = $viewer -> membership() -> getMembersInfo();

			//owner default
			$data[] = array('type' => 'user', 'id' => 'younetco_event_key_' . $viewer -> getIdentity(), 'guid' => $viewer -> getGuid(), 'label' => $viewer -> getTitle(), 'photo' => $this -> view -> itemPhoto($viewer, 'thumb.icon'), 'url' => $viewer -> getHref(), );

			foreach ($viewer -> membership() -> getMembersInfo() as $friend) {
				$friend = Engine_Api::_() -> getItem('user', $friend -> user_id);
				if (is_null($text) || strpos($friend -> getTitle(), $text) !== false) {
					$data[] = array('type' => 'user', 'id' => 'younetco_event_key_' . $friend -> getIdentity(), 'guid' => $friend -> getGuid(), 'label' => $friend -> getTitle(), 'photo' => $this -> view -> itemPhoto($friend, 'thumb.icon'), 'url' => $friend -> getHref(), );
				}

			}
		}
		return $this -> _helper -> json($data);
	}
	
	public function directionAction()
	{
		$eventId = $this -> _getParam('event_id', 0);
		if (!$eventId)
		{
			return $this->_helper->requireAuth()->forward();
		}
		
		$this->view->event = $event = Engine_Api::_()->getItem('event', $eventId);
		if (is_null($event))
		{
			return $this->_helper->requireAuth()->forward();
		}
		
		$st_address = "";
		if ($event -> address != '')
			$st_address .= $event -> address;
		
		if ($event -> city != '')
			$st_address .= ", " . $event -> city;
		
		if ($event -> country != '')
			$st_address .= ", " . $event -> country;
		
		if ($event -> zip_code != '')
			$st_address .= ", " . $event -> zip_code;
		
		$pos = strpos($st_address, ",");
		if ($pos === 0)
			$st_address = substr($st_address, 1);
		
		$this -> view -> fullAddress = $st_address;
		
	}
}
