<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: User.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Model_User extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array('search', 'displayname', 'username');
  /**
   * Gets the title of the user (their username)
   *
   * @return string
   */
  public function getTitle()
  {
    // This will cause various problems
    if( isset($this->displayname) && '' !== trim($this->displayname) ) {
      return $this->displayname;
    } else if( isset($this->username) && '' !== trim($this->username) ) {
      return $this->username;
    } else if( isset($this->email) && '' !== trim($this->email) ) {
      $tmp = explode('@', $this->email);
      return $tmp[0];
    } else {
      return Zend_Registry::get('Zend_Translate')->_("Deactivated Member");
    }
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $profileAddress = null;
    if( isset($this->username) && '' != trim($this->username) ) {
      $profileAddress = $this->username;
    } else if( isset($this->user_id) && $this->user_id > 0 ) {
      $profileAddress = $this->user_id;
    } else {
      return 'javascript:void(0);';
    }
    return Zend_Controller_Front::getInstance()->getBaseUrl().'/'.$profileAddress;
  }


  public function setDisplayName($displayName)
  {
    if( is_string($displayName) )
    {
      $this->displayname = $displayName;
    }

    else if( is_array($displayName) )
    {
      // Has both names
      if( !empty($displayName['first_name']) && !empty($displayName['last_name']) )
      {
        $displayName = $displayName['first_name'].' '.$displayName['last_name'];
      }
      // Has full name
      else if( !empty($displayName['full_name']) )
      {
        $displayName = $displayName['full_name'];
      }
      // Has only first
      else if( !empty($displayName['first_name']) )
      {
        $displayName = $displayName['first_name'];
      }
      // Has only last
      else if( !empty($displayName['last_name']) )
      {
        $displayName = $displayName['last_name'];
      }
      // Has neither (use username)
      else
      {
        $displayName = $this->username;
      }
      
      $this->displayname = $displayName;
    }
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
      $fileName = $file;
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
      $fileName = $photo->name;
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
      $file = $tmpRow->temporary();
      $fileName = $tmpRow->name;
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
      $fileName = $photo['name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
      $fileName = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    if( !$fileName ) {
      $fileName = $file;
    }

    $name = basename($file);
    $extension = ltrim(strrchr(basename($fileName), '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity(),
      'user_id' => $this->getIdentity(),
      'name' => basename($fileName),
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

    // Resize image (main)
    $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($mainPath)
      ->destroy();

    // Resize image (profile)
    $profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($profilePath)
      ->destroy();

    // Resize image (normal)
    $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($normalPath)
      ->destroy();

    // Resize image (icon)
    $squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($squarePath)
      ->destroy();

    // Store
    $iMain = $filesTable->createFile($mainPath, $params);
    $iProfile = $filesTable->createFile($profilePath, $params);
    $iIconNormal = $filesTable->createFile($normalPath, $params);
    $iSquare = $filesTable->createFile($squarePath, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($mainPath);
    @unlink($profilePath);
    @unlink($normalPath);
    @unlink($squarePath);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;
  }

  public function isEnabled()
  {
    return ( $this->enabled );
  }

  public function isAdmin()
  {
    // Not logged in, not an admin
    if( !$this->getIdentity() || empty($this->level_id) ) {
      return false;
    }
    
    // Check level
    //return (bool) Engine_Registry::get('database-default')
    // return (bool) Zend_Registry::get('Zend_Db')
    return $this->getTable()->getAdapter()
        ->select()
        ->from('engine4_authorization_levels', new Zend_Db_Expr('TRUE'))
        ->where('level_id = ?', $this->level_id)
        ->where('type IN(?)', array('admin', 'moderator'))
        ->limit(1)
        ->query()
        ->fetchColumn();
  }
  
  public function isAdminOnly()
  {
    // Not logged in, not an admin
    if( !$this->getIdentity() || empty($this->level_id) ) {
      return false;
    }
    
    // Check level
    //return (bool) Engine_Registry::get('database-default')
    // return (bool) Zend_Registry::get('Zend_Db')
    return $this->getTable()->getAdapter()
        ->select()
        ->from('engine4_authorization_levels', new Zend_Db_Expr('TRUE'))
        ->where('level_id = ?', $this->level_id)
        ->where('type IN(?)', array('admin'))
        ->limit(1)
        ->query()
        ->fetchColumn();
  }

  // Internal hooks

  protected function _insert()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    // These need to be done first so the hook can see them
    $this->level_id = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel()->level_id;
    $this->approved = (int) ($settings->getSetting('user.signup.approve', 1) == 1);
    $this->verified = (int) ($settings->getSetting('user.signup.verifyemail', 1) < 2);
    $this->enabled  = ( $this->approved && $this->verified );
    $this->search   = true;

    if( empty($this->_modifiedFields['timezone']) ) {
      $this->timezone = $settings->getSetting('core.locale.timezone', 'America/Los_Angeles');
    }
    if( empty($this->_modifiedFields['locale']) ) {
      $this->locale = $settings->getSetting('core.locale.locale', 'auto');
    }
    if( empty($this->_modifiedFields['language']) ) {
      $this->language = $settings->getSetting('core.locale.language', 'en_US');
    }
    
    if( 'cli' !== PHP_SAPI ) { // No CLI
      // Get ip address
      $db = $this->getTable()->getAdapter();
      $ipObj = new Engine_IP();
      $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));
      $this->creation_ip = $ipExpr;
    }

    // Set defaults, process etc
    $this->salt = (string) rand(1000000, 9999999);
    if( !empty($this->password) ) {
      $this->password = md5( $settings->getSetting('core.secret', 'staticSalt')
                          . $this->password
                          . $this->salt );
    } else {
      $this->password = '';
    }

    // The hook will be called here
    parent::_insert();
  }

  protected function _postInsert()
  {
    parent::_postInsert();
    
    // Create auth stuff
    $context = Engine_Api::_()->authorization()->context;
    
    // View
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $this, 'auth_view');
    if( empty($view_options) || !is_array($view_options) ) {
      $view_options = array('member', 'network', 'registered', 'everyone');
    }
    foreach( $view_options as $role ) {
      $context->setAllowed($this, $role, 'view', true);
    }
    
    // Comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $this, 'auth_comment');
    if( empty($comment_options) || !is_array($comment_options) ) {
      $comment_options = array('member', 'network', 'registered', 'everyone');
    }
    foreach( $comment_options as $role ) {
      $context->setAllowed($this, $role, 'comment', true);
    }
  }

  protected function _update()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    // Hash password if being updated
    if( !empty($this->_modifiedFields['password']) ) {
      if( empty($this->salt) ) {
        $this->salt = (string) rand(1000000, 9999999);
      }
      $this->password = md5( $settings->getSetting('core.secret', 'staticSalt')
        . $this->password
        . $this->salt );
    }

    // Update enabled, hook will set to false if necessary
    if( !empty($this->_modifiedFields['approved']) ||
        !empty($this->_modifiedFields['verified']) ||
        !empty($this->_modifiedFields['enabled']) ) {
      if( 2 === (int) $settings->getSetting('user.signup.verifyemail', 0) ) {
        $this->enabled = ( $this->approved && $this->verified );
      } else {
        $this->enabled = (bool) $this->approved;
      }
    }

    // Call parent
    parent::_update();
  }

  protected function _delete()
  {
    // Check level
    $level = Engine_Api::_()->getItem('authorization_level', $this->level_id);
    if( $level->flag == 'superadmin' ) {
      throw new User_Model_Exception('Cannot delete superadmins.');
    }
    
    // Remove from online users
    $table = Engine_Api::_()->getDbtable('online', 'user');
    $table->delete(array('user_id = ?' => $this->getIdentity()));

    // Remove from verify users
    $verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
    $verifyTable->delete(array('user_id = ?' => $this->getIdentity()));
    
    // Remove fields values
    Engine_Api::_()->fields()->removeItemValues($this);

    // Call parent
    parent::_delete();
  }



  // Ownership

  public function isOwner(Core_Model_Item_Abstract $owner)
  {
    // A user only can be owned by self
    return ( $owner->getGuid(false) === $this->getGuid(false) );
  }

  public function getOwner($recurseType = null)
  {
    // A user only can be owned by self
    return $this;
  }

  public function getParent($recurseType = null)
  {
    // A user can only belong to self
    return $this;
  }



  // Blocking
  
  public function isBlocked($user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return false;
    }

    $table = Engine_Api::_()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $this->getIdentity())
      ->where('blocked_user_id = ?', $user->getIdentity())
      ->limit(1);
    $row = $table->fetchRow($select);
    return ( null !== $row );
  }

  public function isBlockedBy($user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return false;
    }
    
    $table = Engine_Api::_()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('blocked_user_id = ?', $this->getIdentity())
      ->limit(1);
    $row = $table->fetchRow($select);
    return ( null !== $row );
  }

  public function getBlockedUsers()
  {
    $user = Engine_Api::_()->user()->getViewer();
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return array();
    }
    
    $table = Engine_Api::_()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $this->getIdentity());
    
    $ids = array();
    foreach( $table->fetchAll($select) as $row )
    {
      $ids[] = $row->blocked_user_id;
    }

    return $ids;
  }

  public function addBlock(User_Model_User $user)
  {
    // Check auth?
    //die(Engine_Api::_()->authorization()->isAllowed($user, $this, 'block'));
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return $this;
    }
    
    if( !$this->isBlocked($user) && $user->getGuid(false) != $this->getGuid(false) )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        Engine_Api::_()->getDbtable('block', 'user')
          ->insert(array(
            'user_id' => $this->getIdentity(),
            'blocked_user_id' => $user->getIdentity()
          ));
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }

    return $this;
  }

  public function removeBlock(User_Model_User $user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return $this;
    }
    
    Engine_Api::_()->getDbtable('block', 'user')
      ->delete(array(
        'user_id = ?' => $this->getIdentity(),
        'blocked_user_id = ?' => $user->getIdentity()
      ));
      
    return $this;
  }



  // Interfaces

  /**
   * Gets a proxy object for the likes handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the fields handler
   *
   * @return Engine_ProxyObject
   */
  public function fields()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getApi('core', 'fields'));
  }

  /**
   * Gets a proxy object for the membership handler
   * 
   * @return Engine_ProxyObject
   */
  public function membership()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('membership', 'user'));
  }

  public function lists()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('lists', 'user'));
  }


  /**
   * Gets a proxy object for the fields handler
   *
   * @return Engine_ProxyObject
   */
  public function status()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('status', 'core'));
  }



  // Utility
  
  protected function _readData($spec)
  {
    if( is_scalar($spec) )
    {
      // Identity
      if( is_numeric($spec) )
      {
        // Can't use find because it won't return a row class
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("user_id = ?", $spec));
      }

      // By email
      else if( is_string($spec) && strpos($spec, '@') !== false )
      {
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("email = ?", $spec));
      }

      // By username
      else if( is_string($spec) )
      {
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("username = ?", $spec));
      }
    }

    parent::_readData($spec);
  }
	
	//HOANGND fuction for add section to user
	public function addSection($section, $params) {
        if (!$section || !$params) {
            return false;
        }
        switch ($section) {
        	case 'contact':
				$stripTagKeys = array('contact_num', 'email1', 'email2', 'skype');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $this->$key = strip_tags($params[$key]);
				}
				$this -> save();
                break;
			case 'bio':
                $this -> bio = $params['bio'];
				$this -> save();
                break;
				
			case 'offerservice':
                $table = Engine_Api::_()->getDbTable('offerservices', 'user');
				$stripTagKeys = array('location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
			
			case 'archievement':
                $table = Engine_Api::_()->getDbTable('archievements', 'user');
				$stripTagKeys = array('title', 'short_description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
			
			case 'license':
                $table = Engine_Api::_()->getDbTable('licenses', 'user');
				$stripTagKeys = array('title', 'number');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
				
			case 'experience':
                $table = Engine_Api::_()->getDbTable('experiences', 'user');
                if (isset($params['current']) && $params['current']) {
                    $params['end_year'] = null;
                    $params['end_month'] = null;
                }
				$stripTagKeys = array('title', 'company', 'description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
				
			case 'education':
                $table = Engine_Api::_()->getDbTable('educations', 'user');
				$stripTagKeys = array('degree', 'institute', 'location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
        };
		
		if ($table) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $item = $table->createRow();
                $item->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $item->setFromArray($params);
                $item->save();
                
                $db->commit();
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
	
	//HOANGND fuction for edit section of user
	public function editSection($section, $params) {
        if (!$section || !$params || !isset($params['item_id'])) {
            return false;
        }
        switch ($section) {
            case 'offerservice':
                $item = Engine_Api::_()->getItem('user_offerservice', $params['item_id']);
				$stripTagKeys = array('location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
			
			case 'archievement':
                $item = Engine_Api::_()->getItem('user_archievement', $params['item_id']);
				$stripTagKeys = array('title', 'short_description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
				if (!empty($params['short_description'])) {
					$permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
					$max_character = $permissionsTable->getAllowed('user', $this->level_id, 'archievement_descriptionmax');
				    if ($max_character == null) {
				        $row = $permissionsTable->fetchRow($permissionsTable->select()
				        ->where('level_id = ?', $this->level_id)
				        ->where('type = ?', 'user')
				        ->where('name = ?', 'archievement_descriptionmax'));
				        if ($row) {
				            $max_character = $row->value;
				        }
				    }
					if ($max_character) {
						$params['short_description'] = substr($params['short_description'], intval($max_character));
					}
				}
                break;
			
			case 'license':
                $item = Engine_Api::_()->getItem('user_license', $params['item_id']);
				$stripTagKeys = array('title', 'number');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
					
			case 'experience':
                $item = Engine_Api::_()->getItem('ynresume_experience', $params['item_id']);
                if (isset($params['current']) && $params['current']) {
                    $params['end_year'] = null;
                    $params['end_month'] = null;
                }
				$stripTagKeys = array('title', 'company', 'description');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
				
			case 'education':
                $table = Engine_Api::_()->getDbTable('educations', 'user');
				$stripTagKeys = array('degree', 'institute', 'location');
				foreach ($stripTagKeys as $key) {
					if (!empty($params[$key])) $params[$key] = strip_tags($params[$key]);
				}
                break;
        };
        if ($item) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $item->setFromArray($params);
                $item->save();
                $db->commit();
            }
            catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }
	//HOANGND function for remove section of user
	public function removeSection($section, $params) {
        if (!$section || !$params) {
            return false;
        }
        switch ($section) {
        	case 'contact':
				$elements = array('contact_num', 'email1', 'email2', 'skype');
				foreach ($elements as $key) {
					$this->$key = '';
				}
				$this -> save();
			case 'bio':
                $this -> bio = "";
				$this -> save();
                break;
			
			case 'archievement':
                $item = Engine_Api::_()->getItem('user_archievement', $params['item_id']);
                break;
				
			case 'license':
                $item = Engine_Api::_()->getItem('user_license', $params['item_id']);
                break;
				
			case 'offerservice':
                $item = Engine_Api::_()->getItem('user_offerservice', $params['item_id']);
                break;
				
			case 'experience':
                $item = Engine_Api::_()->getItem('user_experience', $params['item_id']);
                break;
				
			case 'education':
                $item = Engine_Api::_()->getItem('user_education', $params['item_id']);
                break;
		}
		if ($item) {
            $item->delete();
        }
    }
	
	//HOANGND function for get all offer services of user
	public function getAllOfferServices() {
		return Engine_Api::_()->getDbTable('offerservices', 'user')->getAllOfferServicesOfUser($this->getIdentity());
	}
	
	public function getAllArchievements() {
		return Engine_Api::_()->getDbTable('archievements', 'user')->getAllArchievementsOfUser($this->getIdentity(), 'archievement');
	}
	
	public function getAllTrophies() {
		return Engine_Api::_()->getDbTable('archievements', 'user')->getAllArchievementsOfUser($this->getIdentity(), 'trophy');
	}
	
	public function getAllLicenses() {
		return Engine_Api::_()->getDbTable('licenses', 'user')->getAllLicensesOfUser($this->getIdentity(), 'license');
	}
	
	public function getAllCertificates() {
		return Engine_Api::_()->getDbTable('licenses', 'user')->getAllLicensesOfUser($this->getIdentity(), 'certificate');
	}
	
	//HOANGND function for get all experiences of user
	public function getAllExperiences() {
		return Engine_Api::_()->getDbTable('experiences', 'user')->getAllExperiencesOfUser($this->getIdentity());
	}
	
	//HOANGND function for get all educations of user
	public function getAllEducations() {
		return Engine_Api::_()->getDbTable('educations', 'user')->getAllEducationsOfUser($this->getIdentity());
	}
	
	//HOANGND function for get all receiver recommendations
	public function getReceivedRecommendations() {
		return Engine_Api::_()->getDbTable('recommendations', 'user')->getReceivedRecommendations($this->getIdentity());
	} 

	public function getShowRecommendations() {
		return Engine_Api::_()->getDbTable('recommendations', 'user')->getShowRecommendations($this->getIdentity());
	} 

	//HOANGND function for check user can ask recommendation
	public function canAskRecommendation() {
		$friendslist = $this->getFriendsList();
		foreach ($friendslist as $friend) {
			$recommendation = Engine_Api::_()->getDbTable('recommendations', 'user')->getRecommendation($this->getIdentity(), $friend->getIdentity());
			if (!$recommendation && $friend->membership()->isMember($this, 1)) return true;
		}
		return false;
	}
	
	public function getRequestRecommendations() {
		return Engine_Api::_()->getDbTable('recommendations', 'user')->getRequestRecommendations($this->getIdentity());
	}
	
	public function getPendingRecommendations() {
		return Engine_Api::_()->getDbTable('recommendations', 'user')->getPendingRecommendations($this->getIdentity());
	}
	
	public function getFriendsList() {
		$table = Engine_Api::_()->getItemTable('user');
		$select = $this->membership()->getMembershipsOfSelect($this);
		return $table->fetchAll($select);
	}
	
	public function getRecommendation($giver_id) {
		return Engine_Api::_()->getDbTable('recommendations', 'user')->getRecommendation($this->getIdentity(), $giver_id);
	}

	public function isFriend($user_id) {
		$friendslist = $this->getFriendsList();
		foreach ($friendslist as $friend) {
			if ($friend->getIdentity() == $user_id) {
				return true;
			}
		}
		return false;
	}	
	public function getMainLibrary() {
		$table = Engine_Api::_() -> getItemTable('user_library');
		$select = $table -> select() -> where('user_id = ?', $this -> getIdentity()) -> limit(1);

		$library = $table -> fetchRow($select);

		if (null === $library)
		{
			$library = $table -> createRow();
			$library -> setFromArray(array(
				'user_id' => $this -> getIdentity(),
				'title' => 'Main Library',
			));
			$library -> save();
			
			 // CREATE AUTH STUFF HERE
		      $auth = Engine_Api::_()->authorization()->context;
		      $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
		      if(isset($values['auth_view'])) $auth_view =$values['auth_view'];
		      else $auth_view = "everyone";
		      $viewMax = array_search($auth_view, $roles);
		      foreach( $roles as $i=>$role )
		      {
		        $auth->setAllowed($library, $role, 'view', ($i <= $viewMax));
		      }
			
		}

		return $library;
	}
	
	public function getSports() {
		return Engine_Api::_()->getDbTable('sportmaps', 'user')->getSportsOfUser($this->getIdentity());
	}
	
	public function getSportsAssoc() {
		$arr = array();
		$sports = $this->getSports();
		foreach ($sports as $sport) {
			$arr[$sport->getIdentity()] = $sport->getTitle();
		}
		return $arr;
	}
	
	public function getSportId() {
		$arr = $this->getSportsAssoc();
		return array_keys($arr);
	}
	
	public function setCoverPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_id' => $this -> getIdentity(),
			'parent_type' => 'user'
		);

		// Save
		$storage = Engine_Api::_() -> storage();
		$angle = 0;
		if (function_exists('exif_read_data')) 
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
		$image -> open($file) ;
		if ($angle != 0)
			$image -> rotate($angle);
		$image -> resize(1200, 1200) -> write($path . '/m_' . $name) -> destroy();

		$iMain = $storage -> create($path . '/m_' . $name, $params);
		
		// Remove temp files
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> cover_photo = $iMain -> file_id;
		
		$this -> save();

		return $this;
	}

	public function getCountry() {
		if(!empty($this->country_id))
			return Engine_Api::_()->getItem('user_location', $this->country_id);
		else {
			return null;
		}
	}
	
	public function getProvince() {
		if(!empty($this->province_id))
			return Engine_Api::_()->getItem('user_location', $this->province_id);
			else {
			return null;
		}
	}
	
	public function getCity() {
		if(!empty($this->city_id))
			return Engine_Api::_()->getItem('user_location', $this->city_id);
		else {
			return null;
		}
	}
	
	public function getLocation() {
		$location = array();
		if ($this->getCity()) $location[] = $this->getCity()->getTitle();
		if ($this->getProvince()) $location[] = $this->getProvince()->getTitle();
		if ($this->getCountry()) $location[] = $this->getCountry()->getTitle();
		return $location;
	}
	public function getDescription()
	{
		if($this -> getLocation())
			return implode(', ', $this -> getLocation());
		return null;
	}
	
	public function getEyeOns() {
		return Engine_Api::_()->getDbTable('eyeons', 'user')->getUserEyeOns($this->getIdentity());
	}
	
	public function sendInMail($email, $message) {
        // Check message
        $message = trim($message);
        $photo_url = ($this->getPhotoUrl('thumb.profile')) ? $this->getPhotoUrl('thumb.profile') : 'application/modules/User/externals/images/nophoto_user_thumb_profile.png';
        $mailType = 'user_send_inmail';
        $mailParams = array(
          	'host' => $_SERVER['HTTP_HOST'],
          	'email' => $email,
          	'date' => time(),
          	'sender_email' => $this->email,
          	'sender_title' => $this->getTitle(),
          	'sender_link' => $this->getHref(),
          	'sender_photo' => $photo_url,
          	'message' => $message,
        );
        
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          	$email,
          	$mailType,
          	$mailParams
        );
		
		$table = Engine_Api::_()->getDbTable('mails', 'user');
		$row = $table->createRow();
		$row->user_id = $this->getIdentity();
		$row->creation_date = date('Y-m-d H:i:s');
		$row->save();
	}
	
	public function getClub() {
		return Engine_Api::_()->advgroup()->getGroupUser($this);
	}
	
	public function sendEmailToFriend($recipient, $message) 
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
    
        // Check message
        $message = trim($message);
        $mailType = 'user_email_to_friend';
		
        $mailParams = array(
          'host' => $_SERVER['HTTP_HOST'],
          'email' => $recipient,
          'date' => time(),
          'sender_email' => $this->email,
          'sender_title' => $this->getTitle(),
          'sender_link' => $this->getHref(),
          'sender_photo' => $this->getPhotoUrl('thumb.icon'),
          'message' => $message,
        );
        
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          $recipient,
          $mailType,
          $mailParams
        );
    }
}
