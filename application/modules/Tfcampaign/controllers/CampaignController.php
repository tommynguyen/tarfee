<?php
class Tfcampaign_CampaignController extends Core_Controller_Action_Standard
{
	public function init() {
		
		if (0 !== ($campaign_id = (int)$this -> _getParam('campaign_id')) && null !== ($campaign = Engine_Api::_() -> getItem('tfcampaign_campaign', $campaign_id)))
		{
			Engine_Api::_() -> core() -> setSubject($campaign);
		}
		$this -> _helper -> requireSubject('tfcampaign_campaign');
	}
	
	public function editSubmissionAction() {
		
		$this -> _helper -> layout -> setLayout('default-simple');
		
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$campaign = Engine_Api::_() -> core() -> getSubject();
		$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $this ->_getParam('submission_id'));
		$this -> view -> form = $form = new Tfcampaign_Form_EditSubmit(array('campaign' => $campaign));
		
		$form -> populate($submission -> toArray());
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		//check valid form
		if (!$form -> isValid($posts)) {
			return;
		}
		
		$values = $form -> getValues();
		$db = $campaign -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$submission -> setFromArray($values);
			$submission -> save();
			
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function listEditAction() {
		$campaign = Engine_Api::_() -> core() -> getSubject();
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> form = $form = new Tfcampaign_Form_EditList(array('campaign' => $campaign));
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		
		$values = $form -> getValues();
		$submission_id = $values['submission_id'];
					
		 return $this->_helper->redirector->gotoRoute(array('action' => 'edit-submission', 'campaign_id' => $campaign -> getIdentity(), 'submission_id' => $submission_id), 'tfcampaign_specific', true);
	}
	
	public function listWithdrawAction() {
		$campaign = Engine_Api::_() -> core() -> getSubject();
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$this -> view -> form = $form = new Tfcampaign_Form_WithDrawList(array('campaign' => $campaign));
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		
		$values = $form -> getValues();
		$submission_ids = $values['submission_ids'];
		foreach($submission_ids as $submission_id) {
			$submision = Engine_Api::_() -> getItem('tfcampaign_submission', $submission_id);
			if($submision) {
				$submision -> delete();
			}
		}
					
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function saveSuggestAction() {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$campaign = Engine_Api::_() -> core() -> getSubject();
		
		 // Check authorization to edit campaing.
        if (!$campaign->isEditable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
		
	
		$type = $this ->_getParam('type');
		$params = $this ->_getAllParams();
		
		$db = $campaign -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			switch ($type) {
			case 'age':
					$campaign -> from_age = $params['from_age'];
					$campaign -> to_age = $params['to_age'];
					$campaign -> save();
					break;
			case 'gender':
					$campaign -> gender = $params['gender'];
					$campaign -> save();
					break;	
			case 'country':
					$campaign -> country_id = $params['country_id'];
					$campaign -> province_id = $params['province_id'];
					$campaign -> city_id = $params['city_id'];
					$campaign -> save();
					break;
				default:
					
					break;
			}
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		echo Zend_Json::encode(array('error_code' => 0));
		exit ;
	}
	
	public function withdrawAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $this ->_getParam('id'));
		if(!$submission) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if(!$viewer -> isSelf($submission -> getOwner())) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> view -> form = $form = new Tfcampaign_Form_WithDraw();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		$submission -> delete();
		
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function unhideAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $this ->_getParam('id'));
		if(!$submission) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$campaign = $submission -> getCampaign();
		if(!$viewer -> isSelf($campaign -> getOwner())) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> view -> form = $form = new Tfcampaign_Form_UnHide();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		$submission -> reason_id = 0;
		$submission -> hided = false;
		$submission -> save(); 
		
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function hideAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $this ->_getParam('id'));
		if(!$submission) {
			return $this -> _helper -> requireSubject() -> forward();
		}
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$campaign = $submission -> getCampaign();
		if(!$viewer -> isSelf($campaign -> getOwner())) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		$this -> view -> form = $form = new Tfcampaign_Form_Hide();
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if (!$form -> isValid($posts)) {
			return;
		}
		$values = $form -> getValues();
		$reason_id = (isset($values['reason_id']))? $values['reason_id'] : 0;
		$submission -> reason_id = $reason_id;
		$submission -> hided = true;
		$submission -> save(); 
		
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function submitAction() {
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$campaign = Engine_Api::_() -> core() -> getSubject();
		if (!$campaign->isViewable()) {
            $this -> view -> errorMessage = $this -> view -> translate("You can not submit player to this campaign.");
			return;
        }
		$this -> view -> form = $form = new Tfcampaign_Form_Submit(array('campaign' => $campaign));
		
		if (!$this -> getRequest() -> isPost()) {
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		//check valid form
		if (!$form -> isValid($posts)) {
			return;
		}
		
		$values = $form -> getValues();
		$db = $campaign -> getTable() -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$values['user_id'] = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
			$values['campaign_id'] = $campaign -> getIdentity();
			$submissionTable = Engine_Api::_() -> getItemTable('tfcampaign_submission');
			$submission =  $submissionTable -> createRow();
			$submission -> setFromArray($values);
			$submission -> save();
			
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}
		
		// Redirect
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => true,
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
		));
	}
	
	public function deleteAction() {
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$campaign = Engine_Api::_() -> core() -> getSubject();
		
		//check authorization for deleting campaign.
        if (!$campaign->isDeletable()) {
            $this->view->error = true;
            $this->view->message = $this -> view -> translate('You don\'t have permission to delete this listing.');
            return;    
        }
		
		// In smoothbox
		$this -> _helper -> layout -> setLayout('default-simple');
		$this -> view -> form = $form = new Tfcampaign_Form_Delete();

		if (!$campaign)
		{
			$this -> view -> error = false;
			$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _("Campaign doesn't exists or not authorized to delete.");
			return;
		}

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		$db = $campaign -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$campaign -> deleted = true;
			$campaign -> save();
			
			$db -> commit();
		}
		catch (Exception $e)
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('This campaign has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'tfcampaign_general', true),
			'messages' => Array($this -> view -> message)
		));
	}
	
	public function editAction()
	{
		$this -> _helper -> content	-> setEnabled();
		// Return if guest try to access to create link.
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		
		$this -> _helper -> content -> setEnabled();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		//Get campaign
		$campaign = Engine_Api::_() -> core() -> getSubject();
        
        // Check authorization to edit campaing.
        if (!$campaign->isEditable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
		
		// Create form
		$this -> view -> form = $form = new Tfcampaign_Form_Edit();
		
		//toto check editable
		$allowPrivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('tfcampaign_private_allow', 1);
		if($allowPrivate) 
		{
			// authorization
		    $auth = Engine_Api::_()->authorization()->context;
		    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		    foreach( $roles as $role )
		    {
		      if( 1 === $auth->isAllowed($campaign, $role, 'view') && isset( $form->auth_view))
		      {
		        $form->auth_view->setValue($role);
		      }
		    }
		}
		//view for specific users
		$tableUserItemView = Engine_Api::_() -> getDbTable('userItemView', 'user');
		$this -> view -> userViewRows = $userViewRows = $tableUserItemView -> getUserByItem($campaign);
		
		if (!$this -> getRequest() -> isPost())
		{
			$arrCampaign = $campaign -> toArray();
			
			if ($arrCampaign['category_id'] == 2)
			{
				$this -> view -> showPreferredFoot = true;
			}
			else
			{
				$this -> view -> showPreferredFoot = false;
			}
			$category_id = $arrCampaign['category_id'];
			$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
			$node = $sportCattable -> getNode($category_id);
			$categories = $node -> getChilren();
			if (count($categories))
			{
				$position_options = array(0 => '');
				foreach ($categories as $category)
				{
					$position_options[$category -> getIdentity()] = $category -> title;
					$node = $sportCattable -> getNode($category -> getIdentity());
					$positons = $node -> getChilren();
					foreach ($positons as $positon)
					{
						$position_options[$positon -> getIdentity()] = '-- ' . $positon -> title;
					}
				}
				$form -> getElement('position_id') -> setMultiOptions($position_options);
				$this -> view -> showPosition = true;
			}
			else
			{
				$this -> view -> showPosition = false;
			}
			
			if (isset($arrCampaign['country_id']))
			{
				$provincesAssoc = array();
				$country_id = $arrCampaign['country_id'];
				if ($country_id) 
				{
					$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
					$provincesAssoc = array('0'=>'') + $provincesAssoc;
				}
				$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
			}
			
			if (isset($arrCampaign['province_id']))
			{
				$citiesAssoc = array();
				$province_id = $arrCampaign['province_id'];
				if ($province_id) {
					$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
					$citiesAssoc = array('0'=>'') + $citiesAssoc;
				}
				$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
			}
			$arrCampaign['languages'] = json_decode($arrCampaign['languages']);
			$start = strtotime($arrCampaign['start_date']);
			$end = strtotime($arrCampaign['end_date']);
		    $oldTz = date_default_timezone_get();
		    date_default_timezone_set($viewer->timezone);
		    $start = date('Y-m-d H:i:s', $start);
			$end = date('Y-m-d H:i:s', $end);
		    date_default_timezone_set($oldTz);
			
			$arrCampaign['start_date'] = $start;
			$arrCampaign['end_date'] = $end;
			
			$form -> populate($arrCampaign);
			return;
		}
		$posts = $this -> getRequest() -> getPost();
		if ($posts['category_id'] == 2)
		{
			$this -> view -> showPreferredFoot = true;
		}
		else
		{
			$this -> view -> showPreferredFoot = false;
		}
		$category_id = $posts['category_id'];
		$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
		$node = $sportCattable -> getNode($category_id);
		$categories = $node -> getChilren();
		if (count($categories))
		{
			$position_options = array(0 => '');
			foreach ($categories as $category)
			{
				$position_options[$category -> getIdentity()] = $category -> title;
				$node = $sportCattable -> getNode($category -> getIdentity());
				$positons = $node -> getChilren();
				foreach ($positons as $positon)
				{
					$position_options[$positon -> getIdentity()] = '-- ' . $positon -> title;
				}
			}
			$form -> getElement('position_id') -> setMultiOptions($position_options);
			$this -> view -> showPosition = true;
		}
		else
		{
			$this -> view -> showPosition = false;
		}
		
		$provincesAssoc = array();
		$country_id = $posts['country_id'];
		if ($country_id) 
		{
			$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
			$provincesAssoc = array('0'=>'') + $provincesAssoc;
		}
		$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
		
		$citiesAssoc = array();
		$province_id = $posts['province_id'];
		if ($province_id) {
			$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
			$citiesAssoc = array('0'=>'') + $citiesAssoc;
		}
		$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
		
		//check valid form
		if (!$form -> isValid($posts)) {
			$this -> view -> error = true;
			return;
		}
		
		// Process
		$values = $form -> getValues();
		$values['user_id'] = $viewer -> getIdentity();
		
		//check age params
		if(!empty($values['from_age']) && !empty($values['to_age'])) {
			if($values['from_age'] > $values['to_age']) {
				$form -> addError($this -> view -> translate('Invalid Age'));
				return;
			}
		}
		
		$db = Engine_Api::_() -> getItemTable('tfcampaign_campaign') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			
			//Set viewer time zone
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start = strtotime($values['start_date']);
			$end = strtotime($values['end_date']);
			$now = date('Y-m-d H:i:s');
			date_default_timezone_set($oldTz);
			if ($start < $now)
			{
				$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
				return;
			}
			if($start >= $end) {
				$form -> addError($this -> view -> translate("End Time must be greater than Start Time."));
				return;
			}
			$values['start_date'] = date('Y-m-d H:i:s', $start);
			$values['end_date'] = date('Y-m-d H:i:s', $end);
			
			$table = Engine_Api::_() -> getItemTable('tfcampaign_campaign');
			$values['languages'] = json_encode($values['languages']);
			$campaign -> setFromArray($values);
			$campaign -> save();
			
			if(!empty($values['languages']))
			{
				foreach(json_decode($values['languages']) as $langId)
				{
					 // save language map
					 $mappingTable = Engine_Api::_() -> getDbtable('languagemappings', 'user');
					 $mappingTable -> save($langId, $campaign);
				}
			}
			
			// Set photo
			if (!empty($values['photo']))
			{
				$campaign -> setPhoto($form -> photo);
			}

			//set allow view for specific users
			$user_ids = explode(",", $values['user_ids']);
			$userItemViewTable = Engine_Api::_() -> getDbTable('userItemView', 'user');
			//delete all before inserting
			$userItemViewTable -> deleteAllRows($campaign);
			foreach ($user_ids as $user_id)
			{
				$row = $userItemViewTable -> createRow();
				$row -> user_id = $user_id;
				$row -> item_id = $campaign -> getIdentity();
				$row -> item_type = $campaign -> getType();
				$row -> save();
			}

			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
			);
			if (isset($values['auth_view']))
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($campaign, $role, 'view', ($i <= $viewMax));
			}


			$db -> commit();

			// Redirect
			return $this -> _forward('success', 'utility', 'core', array(
				'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
					'id' => $campaign -> getIdentity(),
					'slug' => $campaign -> getSlug(),
				), 'tfcampaign_profile', true),
				'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))
			));
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}
}
