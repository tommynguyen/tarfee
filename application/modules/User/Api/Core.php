<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Api_Core extends Core_Api_Abstract
{
  /**
   * @var array User objects by id
   */
  protected $_users = array();

  /**
   * @var arraay User ids by email or id or username
   */
  protected $_indexes = array();

  /**
   * @var User_Model_User Contains the current viewer instance
   */
  protected $_viewer;

  /**
   * @var Zend_Auth Authentication object
   */
  protected $_auth;

  /**
   * @var Zend_Auth_Adapter_Interface Authentication adapter object
   */
  protected $_authAdapter;

	//HOANGND sections of profile
	protected $_sections = array(
		'basic' => 'Basic Information',
		'contact' => 'Contact Information',
		'bio' => 'Biography',
		'offerservice' => 'Service(s) I Offer',
		'archievement' => 'Trophies & Archievements',
		'license' => 'Licenses & Certificates',
		'experience' => 'Work Experience',
		'education' => 'Education',
	//	'recommendation' => 'Recommendations'
    );
  
  //@TODO get default package
  public function getDefaultPackageId(){
  	return "2";
  }
  
  //@TODO get default profile type
  public function getDefaultProfileTypeId(){
  	return "1";
  }
  
  //@TODO change profile base on level user
  public function getProfileTypeBaseOnLevel($level_id) {
  		switch ($level_id) {
			  case '4':
				  return "1";
				  break;
			  case '6':
				  return "12";
				  break;
			  case '7':
				  return "6";
				  break;	  
			  default:
				  
				  break;
		  }
  }
  
  public function sendEmail($user, $mailType, $params = array()) 
    {
        $superAdmins = $this -> getSuperAdmins();
		$sender = $superAdmins[0];
    	$receiverEmail = $user -> email;
		
        // Check message
        $message = trim($message);
        $sentEmails = 0;
        $mailParams = array(
          'host' => $_SERVER['HTTP_HOST'],
          'email' => $receiverEmail,
          'date' => time(),
          'sender_email' => $sender->email,
          'sender_title' => $sender->getTitle(),
          'sender_link' => $sender->getHref(),
          'sender_photo' => $sender->getPhotoUrl('thumb.icon'),
          'object_link' => $user->getHref(),
          'object_title' => $user->getTitle(),
          'object_photo' => $user->getPhotoUrl('thumb.icon'),
          'object_description' => "trial plan", 
        );
        
		//merge if have extra params
		$mailParams = array_merge($mailParams, $params);
		
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $receiverEmail,
          $mailType,
          $mailParams
        );
    }
  
  public function getLevelBaseOnProfileType($profileType_id) {
  		switch ($profileType_id) {
			  case '1':
				  return "4";
				  break;
			  case '4':
				  return "6";
				  break;
			  case '5':
				  return "7";
				  break;	  
			  default:
				  
				  break;
		  }
  }
  
  // Users

  /**
   * Gets an instance of a user
   *
   * @param mixed $identity An id, username, or email
   * @return User_Model_User
   */
  public function getUser($identity)
  {
    // Identity is already a user!
    if( $identity instanceof User_Model_User ) {
      return $identity;
    }

    // Lookup in index
    $user = $this->_lookupUser($identity);
    if( $user instanceof User_Model_User ) {
      return $user;
    }
    
    // Create new instance
    $user = $this->_getUser($identity);
    if( null === $user ) {
      $user = new User_Model_User(array());
    } else {
      $this->_indexUser($user);
    }

    return $user;
  }
  
  /**
   * Gets an instance of multiple users
   *
   * @param array $ids
   * @return array An array of Core_Model_Item_Abstract
   */
  public function getUserMulti(array $ids)
  {
    // Remove any non-numeric values and already retv rows
    $getIds = array();
    foreach( $ids as $index => $value ) {
      if( !is_numeric($value) ) {
        unset($ids[$index]);
      } else if( !isset($this->_users[$value]) ) {
        $getIds[] = $value;
      }
    }

    // Now get any remaining rows, if necessary
    if( !empty($getIds) ) {
      foreach( Engine_Api::_()->getItemTable('user')->find($getIds) as $row ) {
        $this->_indexUser($this->_getUser($row));
      }
    }

    // Now build the return data
    $users = array();
    foreach( $ids as $id ) {
      if( isset($this->_users[$id]) ) {
        $users[] = $this->_users[$id];
      }
    }
    
    return $users;
  }



  // Viewer

  /**
   * Gets the current viewer instance using the authentication storage
   *
   * @return User_Model_User
   */
  public function getViewer()
  {
    if( null === $this->_viewer ){
      $identity = $this->getAuth()->getIdentity();
      $this->_viewer = $this->_getUser($identity);
    }

    return $this->_viewer;
  }

  public function setViewer(User_Model_User $viewer = null)
  {
    $this->_viewer = $viewer;
    return $this;
  }



  // Authentication

  /**
   * Authenticate user
   *
   * @param string $identity Email
   * @param string $credential Password
   * @return Zend_Auth_Result
   */
  public function authenticate($identity, $credential)
  {
    // Translate email
    $userTable = Engine_Api::_()->getItemTable('user');
    $userIdentity = $userTable->select()
      ->from($userTable, 'user_id')
      ->where('`email` = ?', $identity)
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    $authAdapter = $this->getAuthAdapter()
      ->setIdentity($userIdentity)
      ->setCredential($credential);

    return $this->getAuth()->authenticate($authAdapter);
  }

  /**
   * Get the authentication object
   *
   * @return Zend_Auth
   */
  public function getAuth()
  {
    if( null === $this->_auth ) {
      $this->_auth = Zend_Auth::getInstance();
      if( _ENGINE_NO_AUTH && !$this->_auth->getIdentity() ) {
        $this->_auth->getStorage()->write(1);
      }
    }
    return $this->_auth;
  }

  /**
   * Set the authentication object
   *
   * @param Zend_Auth $auth
   * @return User_Api_Core
   */
  public function setAuth(Zend_Auth $auth)
  {
    $this->_auth = $auth;
    return $this;
  }

  /**
   * Get the authentication adapter
   *
   * @return Zend_Auth_Adapter_Interface
   */
  public function getAuthAdapter()
  {
    if( null === $this->_authAdapter ) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $tablePrefix = Engine_Db_Table::getTablePrefix();
      $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');

      $this->_authAdapter = new Zend_Auth_Adapter_DbTable(
        $db,
        Engine_Api::_()->getItemTable('user')->info('name'),
        'user_id',
        'password',
        "MD5(CONCAT('".$salt."', ?, salt))"
      );
    }
    return $this->_authAdapter;
  }

  /**
   * Set the authentication adapter object
   *
   * @param Zend_Auth_Adapter_Interface $authAdapter
   * @return Zend_Auth
   */
  public function setAuthAdapter(Zend_Auth_Adapter_Interface $authAdapter)
  {
    $this->_authAdapter = $authAdapter;
    return $this;
  }



  /* Utility */

  /**
   * Indexes a user object internally by id, username, email
   *
   * @param User_Model_User $user A user object
   * @return void
   */
  protected function _indexUser(User_Model_User $user)
  {
    // Ignore if not an actual user or user is already set
    if( !empty($user->user_id) && !isset($this->_users[$user->user_id]) ) {
      $this->_indexes[$user->user_id] = $user->user_id;

      if ( !empty($user->email)){
        $this->_indexes[$user->email] = $user->user_id;
      }

      if ( !empty($user->username)){
        $this->_indexes[$user->username] = $user->user_id;
      }
      
      $this->_users[$user->user_id] = $user;
    }
  }

  /**
   * Looks up a user by id, username, email
   *
    * @param mixed $identity
   * @return integer|false
   */
  protected function _lookupUser($identity)
  {
    $index = null;
    if( is_scalar($identity) && isset($this->_indexes[$identity]) ) {
      $index = $identity;
    } else if( $identity instanceof Zend_Db_Table_Row_Abstract && isset($identity->user_id) ) {
      $index = $identity->user_id;
    } else if( is_array($identity) && is_string($identity[0]) && is_numeric($identity[1]) ) {
      $index = $identity[1];
    }

    if( isset($this->_indexes[$index]) && isset($this->_users[$this->_indexes[$index]]) ) {
      return $this->_users[$this->_indexes[$index]];
    }

    return null;
  }

  protected function _getUser($identity)
  {
    if( !$identity ) {
      $user = new User_Model_User(array(
        'table' => Engine_Api::_()->getItemTable('user'),
      ));
    } else if( $identity instanceof User_Model_User ) {
      $user = $identity;
    } else if( is_numeric($identity) ) {
      $user = Engine_Api::_()->getItemTable('user')->find($identity)->current();
    } else if( is_string($identity) && strpos($identity, '@') !== false ) {
      $user = Engine_Api::_()->getItemTable('user')->fetchRow(array(
        'email = ?' => $identity,
      ));
    } else /* if( is_string($identity) ) */ {
      $user = Engine_Api::_()->getItemTable('user')->fetchRow(array(
        'username = ?' => $identity,
      ));
    }

    // Empty user?
    if( null === $user ) {
      return new User_Model_User(array());
    }
    
    return $user;
  }

  public function randomPass($len)
  {
    return substr(md5(rand().rand()), 0, $len);
  }

  public function getSuperAdmins()
  {
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $table->select()
      ->where('level_id = ?', 1);

    $superadmins = $table->fetchAll($select);
    return $superadmins;
  }
  
  	//HOANGND get all profile sections
  	public function getAllSections($user = null) 
  	{
        $sections = $this->_sections;
		if($user)
		{
			// check private contact
			$private_contact = $user -> private_contact;
			$viewer = Engine_Api::_()->user()->getViewer();
			if($private_contact == 1)
			{
				if($viewer -> getIdentity())
				{
					$subjectRow = $user->membership()->getRow($viewer);
					if($subjectRow === null && $viewer -> level_id != 6)
					{
						unset($sections['contact']);
					}
				}
				else {
					unset($sections['contact']);
				}
			}
			else if($private_contact == 2)
			{
				if($viewer -> getIdentity())
				{
					if($viewer -> level_id != 6)
					{
						unset($sections['contact']);
					}
				}
				else {
					unset($sections['contact']);
				}
			}
		}
		return $sections;
    }
	
	//HOANGND render profile sections
	function renderSection($section, $user, $params = array()) {
        $view = Zend_Registry::get('Zend_View');
		$viewer = Engine_Api::_()->user()->getViewer();
        $sections = $this->_sections;
        if (array_key_exists($section, $sections)) {
            if (isset($params['save']) && $params['save'] && ($viewer->getIdentity() == $user->getIdentity())) {
            	if ($section == 'recommendation') {
            		$render = (isset($params['render'])) ? $params['render'] : 'show';
					switch ($render) {
						case 'received':
							$show_ids = $params['show_checkbox'];
							$deleted_ids = $params['delete_checkbox'];
					        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
					        $table->showRecommendations($viewer->getIdentity(), $show_ids);
							if (count($deleted_ids)) {
					        	$table->deleteRecommendations($viewer->getIdentity(), $deleted_ids);
					        }
							break;
							
						case 'pending':
							$approved_ids = $params['approve_checkbox'];
							$deleted_ids = $params['delete_checkbox'];
					        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
					        if (count($approved_ids)) {
					        	$table->approveRecommendations($viewer->getIdentity(), $approved_ids);
					        }
							
							if (count($deleted_ids)) {
					        	$table->deleteRecommendations($viewer->getIdentity(), $deleted_ids);
					        }
							break;
									
						case 'request':
							$ignore_ids = $params['ignore_checkbox'];
					        $table = Engine_Api::_()->getDbtable('recommendations', 'user');
							if (count($ignore_ids)) {
					        	$table->ignoreRecommendations($viewer->getIdentity(), $ignore_ids);
					        }
							break;
							
					} 	
            	}
				
				else if (isset($params['item_id']) && $params['item_id']) {
                    $user->editSection($section, $params);
                }
                else {
                    $user->addSection($section, $params);
                }
            }
			 
            if (isset($params['remove']) && $params['remove'] && ($viewer->getIdentity() == $user->getIdentity())) {
                $user->removeSection($section, $params);
            }
            
            $view->section = $section;
            $view->user = $user;
            $view->params = $params;
            return $view -> render('_section_'.$section.'.tpl');
        }
        else return '';
    }
	
	//HOANGND get section label
	public function getSectionLabel($key) {
        $sections = $this->_sections;
        if (isset($sections[$key])) {
            return $sections[$key];
        }
        return '';
    }
	
	//HOANGND check permission of section
	public function checkSectionEnable($user, $section) {
		$level_id = 5;
		if ($user->getIdentity()) {
			$level_id = $user->level_id;
		}
		return Engine_Api::_()->authorization()->getPermission($level_id, 'user', $section);
	}
	
	public function setPhoto($photo, $params) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
            $name = basename($file);
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $name = $photo['name'];
        }
        else
        if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $name = basename($file);
        }
        else {
            throw new Ynfeedback_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
        }

        
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        if (empty($params)) {
            $params = array(
                'parent_type' => 'user',
                'parent_id' => Engine_Api::_()->user()->getViewer() -> getIdentity()
            );
        }
        // Save
        $storage = Engine_Api::_() -> storage();
        $angle = 0;
        if(function_exists('exif_read_data'))
        {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation']))
            {
                switch($exif['Orientation'])
                {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }   
        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        @$image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(140, 105) -> write($path . '/in_' . $name) -> destroy();

        // Resize image (icon)
       $image = Engine_Image::factory();
       $image->open($file);
       
       $size = min($image->height, $image->width);
       $x = ($image->width - $size) / 2;
       $y = ($image->height - $size) / 2;

       $image->resample($x, $y, $size, $size, 48, 48)
         ->write($path.'/is_'.$name)
         ->destroy();

        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        $iProfile = $storage -> create($path . '/p_' . $name, $params);
        $iIconNormal = $storage -> create($path . '/in_' . $name, $params);
        $iSquare = $storage->create($path.'/is_'.$name, $params);

        $iMain -> bridge($iProfile, 'thumb.profile');
        $iMain -> bridge($iIconNormal, 'thumb.normal');
        $iMain -> bridge($iSquare, 'thumb.icon');
        
        // Remove temp files
        @unlink($path . '/p_' . $name);
        @unlink($path . '/m_' . $name);
        @unlink($path . '/in_' . $name);
        @unlink($path . '/is_' . $name);
        // Update row
        return $iMain -> getIdentity();
    }
	public function getProfessionalUsers()
	{
		$table = Engine_Api::_() -> getItemTable('user');
		$select = $table -> select() -> where('level_id = 6');
		$rows = $table -> fetchAll($select);
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row -> getIdentity();
		}
		return $ids;
	}
	
	public function addDeactivateAccountPage()
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'user_settings_deactivate')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'user_settings_deactivate',
        'displayname' => 'User Deactivate Account Settings Page',
        'title' => 'Deactivate Account',
        'description' => 'This page is the deactivate account page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'user.settings-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }

	public function addActivateAccountPage()
  {
    $db = Engine_Db_Table::getDefaultAdapter();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'user_settings_activate')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'user_settings_activate',
        'displayname' => 'User Activate Account Settings Page',
        'title' => 'Active Account',
        'description' => 'This page is the activate account page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
	
	public function canTransfer($item = null) {
		$viewer = Engine_Api::_()->user()->getViewer();
		if (is_null($item)) {
			if (!$viewer->getIdentity()) return false;
			$club = $viewer->getClub();
			if ($club) return true;
			else return false;
		}
		else {
			if (!$item->isOwner($viewer)) {
				return false;
			}
		}
		if (!empty($item->parent_type)) {
			$parentType = $item->parent_type;
			$user_id = $item->getOwner()->getIdentity();
			$user = Engine_Api::_()->getItem('user', $user_id);
			if ($parentType == 'group') {
				if ($user) {
					return true;
				}
			}
			else {
				$club = $user->getClub();
				if ($club) return true;
			}
		}
		return false;
	}
	
	public function transfer($item) {
		if (!empty($item->parent_type)) {
			$parentType = $item->parent_type;
			$user_id = $item->getOwner()->getIdentity();
			$user = Engine_Api::_()->getItem('user', $user_id);
			if ($parentType == 'group') {
				if ($user) {
					$item->parent_type = 'user';
					$item->parent_id = $user->getIdentity();
					
					if ($item->getType() == 'video') {
						$lib = $user->getMainLibrary();
						$item->parent_type = $lib->getType();
						$item->parent_id = $lib->getIdentity();	
					}
					
					$item->save();
					return true;
				}
			}
			else {
				$club = $user->getClub();
				if ($club) {
					$item->parent_type = 'group';
					$item->parent_id = $club->getIdentity();
					$item->save();
					return true;
				}
			}
		}
		return false;
	}
	
	public function getDeactiveUserIds() {
		$table = Engine_Api::_()->getItemTable('user');
		$select = $table->select()->where('deactive <> ?', 0);
		$rows = $table->fetchAll($select);
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = $row->deactive;
		}
		return $ids;
	}
	
	public function itemOfDeactiveUsers($item) {
		$result = false;
		$ids = $this->getDeactiveUserIds();
		if (empty($ids)) return $result;
		
		switch ($item->getType()) {
			case 'user_playercard':
			case 'event':
			case 'group':
			case 'tfcampaign_campaign':
			case 'user':
				if (isset($item->user_id) && in_array($item->user_id, $ids)) $result = true;
				break;
				
			case 'video':
			case 'blog':
				if (isset($item->owner_id) && in_array($item->owner_id, $ids)) $result = true;
				break;
		}
		
		return $result;
	}
}
