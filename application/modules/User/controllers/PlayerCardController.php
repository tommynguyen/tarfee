<?php
class User_PlayerCardController extends Core_Controller_Action_Standard
{
	public function init()
	{
		if (!$this -> _helper -> requireAuth() -> setAuthParams('user_playercard', null, 'view') -> isValid())
			return;

		$id = $this -> _getParam('player_id', $this -> _getParam('id', null));
		if ($id)
		{
			$playerCard = Engine_Api::_() -> getItem('user_playercard', $id);
			if ($playerCard)
			{
				Engine_Api::_() -> core() -> setSubject($playerCard);
			}
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams('user_playercard', null, 'view') -> isValid())
			return;
	}

	public function createAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Create form
		$this -> view -> form = $form = new User_Form_Playercard_Create();
		if (!$this -> getRequest() -> isPost())
		{
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
		if ($posts['relation_id'] == 0)
		{
			$this -> view -> showOther = true;
		}
		else
		{
			$this -> view -> showOther = false;
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
		
		// Location
		$provincesAssoc = array();
		$country_id = $posts['country_id'];
		if ($country_id) {
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

		if (!$form -> isValid($posts))
		{
			return;
		}

		// Process
		$values = $form -> getValues();
		if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('user.relation_require', 1) && $values['relation_id'] == 0 && empty($values['relation_other']))
		{
			$form -> getElement('relation_other') -> addError('Please complete this field - it is required.');
			return false;
		}

		$values['user_id'] = $viewer -> getIdentity();

		$db = Engine_Api::_() -> getDbtable('playercards', 'user') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create player
			$values['languages'] = json_encode($values['languages']);
			if($this -> _getParam('club_parent', 0))
			{
				$club = Engine_Api::_()->getItem('group', $this -> _getParam('club_parent', 0));
				$values['parent_type'] = $club -> getType();
				$values['parent_id'] = $club -> getIdentity();
			}
			$table = Engine_Api::_() -> getDbtable('playercards', 'user');
			$player_card = $table -> createRow();
			$player_card -> setFromArray($values);
			$player_card -> save();
			
			if(!empty($values['languages']))
			{
				foreach(json_decode($values['languages']) as $langId)
				{
					 // save language map
					 $mappingTable = Engine_Api::_() -> getDbtable('languagemappings', 'user');
					 $mappingTable -> save($langId, $player_card);
				}
			}

			// Set photo
			if (!empty($values['photo']))
			{
				$player_card -> setPhoto($form -> photo);
			}
			
			//set allow view for specific users
			$user_ids = explode(",", $values['user_ids']);
			$userItemViewTable = Engine_Api::_() -> getDbTable('userItemView', 'user');
			foreach($user_ids as $user_id) {
				$row = $userItemViewTable -> createRow();
				$row -> user_id = $user_id;
				$row -> item_id = $player_card -> getIdentity();
				$row -> item_type = $player_card -> getType();
				$row -> save();
			}
			
			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_network',
				'everyone'
			);
			if (isset($values['auth_view']))
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($player_card, $role, 'view', ($i <= $viewMax));
			}

			$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($player_card, $role, 'comment', ($i <= $commentMax));
			}

			$db -> commit();
			
			if ($player_card -> parent_type == 'group') {
				$club = Engine_Api::_()->getItem('group', $player_card -> parent_id);
				return $this -> _redirectCustom($club);
			}
			
			// Redirect
			$tab = $this -> _getParam('tab', '');
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			{
				$pageURL .= "s";
			}
			$pageURL .= "://";
			
			$url = $pageURL . $_SERVER['HTTP_HOST'] . $viewer -> getHref().'/view/tab/'.$tab;
			return $this -> _helper -> redirector -> gotoUrl($url);
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function editAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$id = $this -> _getParam('id', 0);
		$player_card = Engine_Api::_() -> getItem('user_playercard', $id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$player_card || $viewer -> getIdentity() != $player_card -> user_id)
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		$this -> view -> form = $form = new User_Form_Playercard_Edit();
		
		// authorization
	    $auth = Engine_Api::_()->authorization()->context;
	    $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
	    foreach( $roles as $role )
	    {
	      if( 1 === $auth->isAllowed($player_card, $role, 'view') )
	      {
	        $form->auth_view->setValue($role);
	      }
	    }
		
		//view for specific users
		$tableUserItemView = Engine_Api::_() -> getDbTable('userItemView', 'user');
		$this -> view -> userViewRows = $userViewRows = $tableUserItemView -> getUserByItem($player_card);
		
		if (!$this -> getRequest() -> isPost())
		{
			if ($player_card -> relation_id == 0)
			{
				$this -> view -> showOther = true;
			}
			$arr_player = $player_card -> toArray();
			
			if ($arr_player['category_id'] == 18)
			{
				$this -> view -> showPreferredFoot = true;
			}
			else
			{
				$this -> view -> showPreferredFoot = false;
			}
			$category_id = $arr_player['category_id'];
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
			
			if (isset($arr_player['country_id']))
			{
				$provincesAssoc = array();
				$country_id = $arr_player['country_id'];
				if ($country_id) 
				{
					$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
					$provincesAssoc = array('0'=>'') + $provincesAssoc;
				}
				$form -> getElement('province_id') -> setMultiOptions($provincesAssoc);
			}
			
			if (isset($arr_player['province_id']))
			{
				$citiesAssoc = array();
				$province_id = $arr_player['province_id'];
				if ($province_id) {
					$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
					$citiesAssoc = array('0'=>'') + $citiesAssoc;
				}
				$form -> getElement('city_id') -> setMultiOptions($citiesAssoc);
			}
			$arr_player['languages'] = json_decode($arr_player['languages']);
			$form -> populate($arr_player);
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
		if ($posts['relation_id'] == 0)
		{
			$this -> view -> showOther = true;
		}
		else
		{
			$this -> view -> showOther = false;
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
		
		if (!$form -> isValid($posts))
		{
			$this -> view -> error = true;
			return;
		}
		$values = $form -> getValues();
		if (Engine_Api::_() -> getApi('settings', 'core') -> getSetting('uaer.relation_require', 1) && $values['relation_id'] == 0 && empty($values['relation_other']))
		{
			$form -> getElement('relation_other') -> addError('Please complete this field - it is required.');
			return false;
		}

		// Process
		$db = Engine_Api::_() -> getItemTable('user_playercard') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Set player info
			$values['languages'] = json_encode($values['languages']);
			$player_card -> setFromArray($values);
			$player_card -> save();
			
			if(!empty($values['languages']))
			{
				foreach(json_decode($values['languages']) as $langId)
				{
					 // save language map
					 $mappingTable = Engine_Api::_() -> getDbtable('languagemappings', 'user');
					 $mappingTable -> save($langId, $player_card);
				}
			}

			if (!empty($values['photo']))
			{
				$player_card -> setPhoto($form -> photo);
			}
			
			//set allow view for specific users
			$user_ids = explode(",", $values['user_ids']);
			$userItemViewTable = Engine_Api::_() -> getDbTable('userItemView', 'user');
			//delete all before inserting
			$userItemViewTable -> deleteAllRows($player_card);
			foreach($user_ids as $user_id) {
				$row = $userItemViewTable -> createRow();
				$row -> user_id = $user_id;
				$row -> item_id = $player_card -> getIdentity();
				$row -> item_type = $player_card -> getType();
				$row -> save();
			}
			
			// CREATE AUTH STUFF HERE
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
				'owner_member',
				'owner_network',
				'everyone'
			);
			if ($values['auth_view'])
				$auth_view = $values['auth_view'];
			else
				$auth_view = "everyone";
			$viewMax = array_search($auth_view, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($player_card, $role, 'view', ($i <= $viewMax));
			}
			$auth_comment = "everyone";
			$commentMax = array_search($auth_comment, $roles);
			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($player_card, $role, 'comment', ($i <= $commentMax));
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($player_card) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			// Commit
			$db -> commit();
			
			if ($player_card -> parent_type == 'group') {
				$club = Engine_Api::_()->getItem('group', $player_card -> parent_id);
				return $this -> _redirectCustom($club);
			}
			// Redirect
			$tab = $this -> _getParam('tab', '');
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			{
				$pageURL .= "s";
			}
			$pageURL .= "://";
			return $this -> _helper -> redirector -> gotoUrl($pageURL . $_SERVER['HTTP_HOST'] . $viewer -> getHref().'/view/tab/'.$tab);
		}
		catch( Engine_Image_Exception $e )
		{
			$db -> rollBack();
			$form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function deleteAction()
	{
		if (!$this -> _helper -> requireUser -> isValid())
			return;
		$id = $this -> _getParam('id', 0);
		$player_card = Engine_Api::_() -> getItem('user_playercard', $id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$player_card || $viewer -> getIdentity() != $player_card -> user_id)
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}

		$this -> view -> form = $form = new User_Form_Playercard_Delete();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}

		$table = Engine_Api::_() -> getItemTable('user_playercard');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$player_card -> delete();
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _forward('success', 'utility', 'core', array(
			'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Player card deleted.')),
			'layout' => 'default-simple',
			'parentRefresh' => true,
		));
	}

	public function subcategoriesAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$cat_id = $this -> getRequest() -> getParam('cat_id');
		$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
		$node = $sportCattable -> getNode($cat_id);
		$categories = $node -> getChilren();
		$html = '';
		foreach ($categories as $category)
		{
			$html .= '<option value="' . $category -> getIdentity() . '" label="' . $category -> title . '" >' . $category -> title . '</option>';
			$node = $sportCattable -> getNode($category -> getIdentity());
			$positions = $node -> getChilren();
			foreach ($positions as $position)
			{
				$html .= '<option value="' . $position -> getIdentity() . '" label="-- ' . $position -> title . '" >' . '-- ' . $position -> title . '</option>';
			}
		}
		echo $html;
		return;
	}
	public function finalizeUrl($url)
	{
		if ($url)
		{
			if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
			{
				$pageURL = 'http';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
				{
					$pageURL .= "s";
				}
				$pageURL .= "://";
				$pageURL .= $_SERVER["SERVER_NAME"];
				$url = $pageURL . '/'. ltrim( $url, '/');
			}
		}
	
		return $url;
	}

	public function viewAction()
	{
		if (!$this -> _helper -> requireSubject() -> isValid())
			return;

		$playerCard = Engine_Api::_() -> core() -> getSubject('user_playercard');
		$viewer = Engine_Api::_() -> user() -> getViewer();
		
		if (Engine_Api::_()->user()->itemOfDeactiveUsers($playerCard)) {
			return $this->_helper->requireSubject()->forward();
		}
		
		//check view auth
		if(!$playerCard -> isViewable()) {
			return $this -> _helper -> requireAuth() -> forward();
		}
		
		$view = Zend_Registry::get('Zend_View');
    	$view->doctype('XHTML1_RDFA');
		if($playerCard->photo_id)
			$view->headMeta() -> setProperty('og:image', $this -> finalizeUrl($playerCard->getPhotoUrl()));
		
		// Check if edit/delete is allowed
		$this -> view -> can_edit = $can_edit = $this -> _helper -> requireAuth() -> setAuthParams($playerCard, null, 'edit') -> checkRequire();
		$this -> view -> can_delete = $can_delete = $this -> _helper -> requireAuth() -> setAuthParams($playerCard, null, 'delete') -> checkRequire();

		$this -> view -> viewer_id = $viewer -> getIdentity();
		$this -> view -> playerCard = $playerCard;

		// Render
		$this -> _helper -> content -> setEnabled();
	}

	public function cropPhotoAction()
	{
		$this -> view -> playerCard = $playerCard = Engine_Api::_() -> core() -> getSubject();
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();

		// Get form
		$this -> view -> form = $form = new User_Form_Edit_CropPhoto();

		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost()))
		{
			return;
		}
		// Resizing a photo
		if ($form -> getValue('coordinates') !== '')
		{
			$storage = Engine_Api::_() -> storage();

			$iMain = $storage -> get($playerCard -> photo_id, 'thumb.main');
			$iProfile = $storage -> get($playerCard -> photo_id, 'thumb.profile');

			// Read into tmp file
			$pName = $iMain -> getStorageService() -> temporary($iMain);
			$iName = dirname($pName) . '/nis_' . basename($pName);

			list($x, $y, $w, $h) = explode(':', $form -> getValue('coordinates'));

			$image = Engine_Image::factory();
			$image -> open($pName) -> resample($x + .1, $y + .1, $w - .1, $h - .1, 200, 200) -> write($iName) -> destroy();

			$iProfile -> store($iName);

			// Remove temp files
			@unlink($iName);
		}
		$this->_forward('success', 'utility', 'core', array(
	      'smoothboxClose' => true,
	      'parentRefresh' => true,
	      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'))
	    ));
	}

	public function addEyeOnAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $id = $this->_getParam('id', 0);
        $player = Engine_Api::_()->getItem('user_playercard', $id);
        if (!$player) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The player card can not be found.')));
        	return;
		}
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have permission to do this.')));
        	return;
		}
        
        if ($player->isEyeOn()) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The player card already in your eye on list.')));
        	return;
		}
        
        $table = Engine_Api::_()->getDbTable('eyeons', 'user');
        $eyeon = $table->createRow();
        $eyeon->setFromArray(array('user_id' => $viewer->getIdentity(), 'player_id'=>$id));
        $eyeon->save();
        echo Zend_Json::encode(array('status' => true));
    }
	
	public function removeEyeOnAction() {
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        $id = $this->_getParam('id', 0);
        $player = Engine_Api::_()->getItem('user_playercard', $id);
        if (!$player) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The player card can not be found.')));
        	return;
		}
        
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('You do not have permission to do this.')));
        	return;
		}
        
        if (!$player->isEyeOn()) {
            echo Zend_Json::encode(array('status' => false, 'message' => Zend_Registry::get('Zend_Translate') -> _('The player card not in your eye on list.')));
        	return;
		}
        
        $table = Engine_Api::_()->getDbTable('eyeons', 'user');
        $where = array(
            $table->getAdapter()->quoteInto('user_id = ?', $viewer->getIdentity()),
            $table->getAdapter()->quoteInto('player_id = ?', $id)
        );
        $table->delete($where);
        
        echo Zend_Json::encode(array('status' => true));
    }

	public function viewEyeOnAction() {
		$this -> _helper -> layout -> setLayout('default-simple');
		$this->view->player = $player = Engine_Api::_() -> core() -> getSubject('user_playercard');
		if (!$player) {
			return $this -> _helper -> requireSubject() -> forward();
		}
	}
}
?>
