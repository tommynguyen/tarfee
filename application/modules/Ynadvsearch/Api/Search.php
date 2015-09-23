<?php
class Ynadvsearch_Api_Search extends Core_Api_Abstract {

    public function getPaginator($text, $types = array(), $from = null, $limit = null) {
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $select = $this->getSelect($text, $types, $from, $limit);
        $results = $table->fetchAll ( $select );
        $results = $this->_checkPermission($results);
        return Zend_Paginator::factory($results);
    }

	public function getSelect($text = array(), $types = array(), $sports = array(), $from = null, $limit = null, $params = array())
    {
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $table_name = $table->info ( 'name' );
		$sportmapTbl = Engine_Api::_()->getDbTable('sportmaps', 'ynadvsearch');
		$sportmapTblName = $sportmapTbl->info('name');
        $db = $table->getAdapter ();
        $select = $table->select ();
		$select->from($table_name);
		$select->setIntegrityCheck(false);
		$select->joinLeft($sportmapTblName, "$sportmapTblName.item_id = $table_name.id and $sportmapTblName.item_type = $table_name.type", "");
        if (!in_array('all', $types))
        	$select->where ( "$table_name.type IN (?)",  $types );
        if (!in_array('all', $sports))
			$select->where ( "$sportmapTblName.sport_id IN (?)",  $sports);
		$select->group("$table_name.id");
		foreach ($text as $str) {
			$search = '%' . preg_replace ( '/\s+/', '%', $str ) . '%';
			$select->where ( "$table_name.title like '$search' OR $table_name.description like '$search'" );
		}
		if(isset($params['parent_type']) && isset($params['parent_id']))
		{
			$parent_type = $params['parent_type'];
			$parent_id = $params['parent_id'];
			if(in_array('video', $types))
			{
				if(isset($params['video_type']) && $params['video_type'] == 'fan' && $parent_type == 'group')
				{
					$club = Engine_Api::_() -> getItem($parent_type, $parent_id);
					// Get videos of fans
					$ids = array();
				    $members = $club->membership()->getMembersInfo();
				    foreach( $members as $member ) {
				    	if ($member->user_id != $club->user_id)
				      		$ids[] = $member->user_id;
				    }
					
					if (empty($ids)) $ids = array(0);
					$tagTbl = Engine_Api::_()->getDbTable('tags', 'core');
					$tagTblName = $tagTbl->info('name');
					$tagmapTbl = Engine_Api::_()->getDbTable('tagMaps', 'core');
					$tagmapTblName = $tagmapTbl->info('name');
					$tagSelect = $tagTbl -> select() -> from($tagTblName) -> setIntegrityCheck(false);
					$tagSelect->joinLeft($tagmapTblName, "$tagmapTblName.tag_id = $tagTblName.tag_id","$tagmapTblName.resource_id as resource_id");
					$tagSelect
						->where("$tagmapTblName.resource_type = ?", 'video')
						->where("$tagmapTblName.tagger_type = ?", 'user')
						->where("$tagmapTblName.tagger_id IN (?)", $ids)
						->where("$tagTblName.text LIKE ?", $club->getTitle());
					$video_ids = array();
					$tags = $tagTbl->fetchAll($tagSelect); 
					$video_ids = array();
					foreach ($tags as $tag) {
						$video_ids[] = $tag->resource_id;
					}
					if (empty($video_ids)) $video_ids = array(0);
					$video_ids = array_unique($video_ids);
					
					$videoTable = Engine_Api::_() -> getItemTable('video');
					$videoTbName = $videoTable -> info('name');
					$select -> joinLeft($videoTbName, "$videoTbName.video_id = $table_name.id", "")
							-> where("$videoTbName.video_id IN (?)", $video_ids)
							-> where("$videoTbName.owner_id IN (?)", $ids)
							-> where("$videoTbName.search = ?", 1) 
							-> where("$videoTbName.status = ?", 1);
				}
				else
				{
					// Get all video of parent
					$videoTable = Engine_Api::_() -> getItemTable('video');
					$videoTbName = $videoTable -> info('name');
					$select -> joinLeft($videoTbName, "$videoTbName.video_id = $table_name.id", "")
							-> where("$videoTbName.parent_type = ?", $parent_type)
							-> where("$videoTbName.parent_id = ?", $parent_id)
							-> where("$videoTbName.search = ?", 1) 
							-> where("$videoTbName.status = ?", 1);
				}
			}
			else if(in_array('event', $types))
			{
				// Get all events of parent
				$eventTable = Engine_Api::_() -> getItemTable('event');
				$eventTbName = $eventTable -> info('name');
				$select -> joinLeft($eventTbName, "$eventTbName.event_id = $table_name.id", "")
						-> where("$eventTbName.parent_type = ?", $parent_type)
						-> where("$eventTbName.parent_id = ?", $parent_id);
			}
			else if(in_array('user_playercard', $types))
			{
				// Get all players of parent
				$playerTable = Engine_Api::_() -> getItemTable('user_playercard');
				$playerTbName = $playerTable -> info('name');
				$select -> joinLeft($playerTbName, "$playerTbName.playercard_id = $table_name.id", "")
						-> where("$playerTbName.parent_type = ?", $parent_type)
						-> where("$playerTbName.parent_id = ?", $parent_id);
			}
		}
        
        // for search result page
        if ($limit && $from) {
            $select->limit ( $limit+1, $from );
        }
        
        else if ($limit) {
            $select->limit ( $limit+1 );
        }
		$select -> order("$table_name.title");
        return $select;
       
    }

	public function getBasicResults($text = array(), $types = array(), $sports = array(), $from, $limit = null, $params = array()) {
        $table = Engine_Api::_ ()->getDbtable ( 'search', 'core' );
        $select = $this->getSelect($text, $types, $sports, $from, $limit, $params);
        $results = $table->fetchAll ( $select );
        return $results;
    }
    
	public function getAdvsearchResults($type, $params = array(), $from, $limit = null) {
		$results = array();
		switch ($type) {
			case 'player':
				$table = Engine_Api::_()->getItemTable('user_playercard');
				$select = $table->select();
				if (!empty($params['keyword'])) {
					$select->where("CONCAT(first_name, ' ', last_name) LIKE ? or description LIKE ?", '%'.$params['keyword'].'%');
				}
				if (!empty($params['sport'])) {
					$select->where('category_id = ?', $params['sport']);
				}
				if (!empty($params['gender'])) {
					$select->where('gender = ?', $params['gender']);
				}
				if (!empty($params['relation_id'])) {
					$select->where('relation_id = ?', $params['relation_id']);
				}
				if (!empty($params['position_id'])) {
					$select->where('position_id = ?', $params['position_id']);
				}				
				if (!empty($params['continent'])) {
					$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesAssocByContinent($params['continent']);
					if (!empty($countries)) {
						$select->where('country_id IN (?)', array_keys($countries));
					}
					else {
						$select->where('country_id IN (?)', array(0));
					}
				}
				
				if (!empty($params['age_from'])) {
					$searchStr = '-'.$params['age_from'].' years';
					$select->where('birth_date <= ?', date('Y-m-d H:i:s', strtotime($searchStr)));
				}

				if (!empty($params['age_to'])) {
					$searchStr = '-'.$params['age_to'].' years';
					$select->where('birth_date >= ?', date('Y-m-d H:i:s', strtotime($searchStr)));
				}
				
				if (isset($params['rating_from']) && is_numeric($params['rating_from'])) {
					$select->where('rating >= ?', $params['rating_from']);
				}

				if (isset($params['rating_to']) && is_numeric($params['rating_to'])) {
					$select->where('rating <= ?', $params['rating_to']);
				}

				if (!empty($params['country_id'])) {
					$select->where('country_id = ?', $params['country_id']);
				}
				
				if (!empty($params['province_id'])) {
					$select->where('province_id = ?', $params['province_id']);
				}
				
				if (!empty($params['city_id'])) {
					$select->where('city_id = ?', $params['city_id']);
				}
				$select -> order('first_name');
				break;
				
			case 'professional':
				$table = Engine_Api::_()->getItemTable('user');
				$select = $table->select();
				
				$select->where('level_id = ?', '6');
				
				if (!empty($params['keyword'])) {
					$select->where('username LIKE ? or displayname LIKE ?', '%'.$params['keyword'].'%');
				}
				if (isset($params['displayname'])) {
					$select->where('displayname LIKE ?', '%'.$params['displayname'].'%');
				}
				if (!empty($params['service'])) {
					$user_ids = Engine_Api::_()->getDbTable('offerservices', 'user')->getAllUserHaveService($params['service']);
					if (empty($user_ids)) $user_ids = array(0);
					$select->where('user_id IN (?)', $user_ids);
				}
				if (!empty($params['continent'])) {
					$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesAssocByContinent($params['continent']);
					if (!empty($countries)) {
						$select->where('country_id IN (?)', array_keys($countries));
					}
					else {
						$select->where('country_id IN (?)', array(0));
					}
				}
				if (!empty($params['country_id'])) {
					$select->where('country_id = ?', $params['country_id']);
				}
				
				if (!empty($params['province_id'])) {
					$select->where('province_id = ?', $params['province_id']);
				}
				
				if (!empty($params['city_id'])) {
					$select->where('city_id = ?', $params['city_id']);
				}
				$select -> order('displayname');
				break;
				
			case 'organization':
				$table = Engine_Api::_()->getItemTable('group');
				$select = $table->select();
				if (!empty($params['keyword'])) {
					$select->where('title LIKE ? or description LIKE ?', '%'.$params['keyword'].'%');
				}
				if (isset($params['displayname'])) {
					$select->where('title LIKE ?', '%'.$params['displayname'].'%');
				}
				if (!empty($params['sport'])) {
					$select->where('sportcategory_id = ?', $params['sport']);
				}
				if (!empty($params['continent'])) {
					$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesAssocByContinent($params['continent']);
					if (!empty($countries)) {
						$select->where('country_id IN (?)', array_keys($countries));
					}
					else {
						$select->where('country_id IN (?)', array(0));
					}
				}
				if (!empty($params['country_id'])) {
					$select->where('country_id = ?', $params['country_id']);
				}
				
				if (!empty($params['province_id'])) {
					$select->where('province_id = ?', $params['province_id']);
				}
				
				if (!empty($params['city_id'])) {
					$select->where('city_id = ?', $params['city_id']);
				}
				$select -> order('title');
				break;
				
			case 'event':
				$table = Engine_Api::_()->getItemTable('event');
				$select = $table->select();
				if (!empty($params['keyword'])) {
					$select->where('title LIKE ? or description LIKE ?', '%'.$params['keyword'].'%');
				}
				if (isset($params['event_type']) && $params['event_type'] != "") {
					$select->where('type_id = ?', $params['event_type']);
				}
				if (!empty($params['sport'])) {
					$select->where('category_id = ?', $params['sport']);
				}
				if (!empty($params['continent'])) {
					$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesAssocByContinent($params['continent']);
					if (!empty($countries)) {
						$select->where('country_id IN (?)', array_keys($countries));
					}
					else {
						$select->where('country_id IN (?)', array(0));
					}
				}
				if (!empty($params['country_id'])) {
					$select->where('country_id = ?', $params['country_id']);
				}
				
				if (!empty($params['province_id'])) {
					$select->where('province_id = ?', $params['province_id']);
				}
				
				if (!empty($params['city_id'])) {
					$select->where('city_id = ?', $params['city_id']);
				}
				$select -> order('title');
				break;
				
			case 'campaign':
				$table = Engine_Api::_()->getItemTable('tfcampaign_campaign');
				$select = $table->select();
				if (!empty($params['keyword'])) {
					$select->where('title LIKE ? or description LIKE ?', '%'.$params['keyword'].'%');
				}
				if (!empty($params['sport'])) {
					$select->where('category_id = ?', $params['sport']);
				}
				if (!empty($params['continent'])) {
					$countries = Engine_Api::_() -> getDbTable('locations', 'user') -> getCountriesAssocByContinent($params['continent']);
					if (!empty($countries)) {
						$select->where('country_id IN (?)', array_keys($countries));
					}
					else {
						$select->where('country_id IN (?)', array(0));
					}
				}
				if (!empty($params['country_id'])) {
					$select->where('country_id = ?', $params['country_id']);
				}
				
				if (!empty($params['province_id'])) {
					$select->where('province_id = ?', $params['province_id']);
				}
				
				if (!empty($params['city_id'])) {
					$select->where('city_id = ?', $params['city_id']);
				}
				$select -> order('title');
				break;
		}
		
		if ($select) {
			if ($limit && $from) {
	            $select->limit ( $limit+1, $from );
	        }
	        
	        else if ($limit) {
	            $select->limit ( $limit+1 );
	        }
		}
		
		if ($table && $select) {
			$results = $table->fetchAll($select);
		}
		
		return $results;
	}
    private function _checkPermission($search) {
        //check permissions
        $search_results = array();
        foreach ($search as $res_item) {
            $type = $res_item->type;
            $item = Engine_Api::_()->getItem($res_item->type, $res_item->id);
            $permission = true;
            $viewer = Engine_Api::_()->user()->getViewer();
            
            if (!$item) continue;
            if (!$item->getIdentity()) continue;
            if ($type == 'user' && ($item->verified != 1 || $item->enabled != 1 || $item->approved != 1)) {
               continue;
            }
   
            switch ($type) {
               case 'file':
                   $parent = Engine_Api::_()->getItem('folder', $item->folder_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'mp3music_album_song':
                   $parent = Engine_Api::_()->getItem('mp3music_album', $item->album_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'mp3music_playlist_song':
                   $parent = Engine_Api::_()->getItem('mp3music_playlist', $item->playlist_id);
                   if ($parent) {
                       $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
                   }
                   else {
                       $permission = false;
                   }
                   break;
               
               case 'user':
               case 'blog':
               case 'classified':
               case 'poll':
               case 'ynauction_product':
               case 'contest':
               case 'forum_topic':
               case 'group':
               case 'ynwiki_page':
               case 'event':
               case 'album':
               case 'advalbum_album':
               case 'advalbum_photo':
               case 'music_playlist':
               case 'music_playlist_song':
               case 'ynfundraising_campaign':
               case 'mp3music_playlist':
               case 'mp3music_album':
               case 'groupbuy_deal':
               case 'folder':
               case 'video':           
                   $permission = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($item, $viewer, 'view') -> checkRequire(); 
                   break;
               
               case 'ynlistings_listing':
                    $permission = true;        
               default:
                   break;
            }
            if ($permission) {
                array_push($search_results, $res_item);
            }
        }
        return $search_results;
    }

    public function checkItemPermission($res_item) {
        $type = $res_item->type;
        $item = Engine_Api::_()->getItem($res_item->type, $res_item->id);
        $permission = true;
        $viewer = Engine_Api::_()->user()->getViewer();
        
        if (!$item) return false;
        if (!$item->getIdentity()) return false;
        if ($type == 'user' && ($item->verified != 1 || $item->enabled != 1 || $item->approved != 1)) 
        {
             if($viewer -> isAdminOnly())
			 {
			 	return true;
			 }
			 else {
				 return false;
			 }
        }
   
            switch ($type) {
               case 'file':
               $parent = Engine_Api::_()->getItem('folder', $item->folder_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'mp3music_album_song':
               $parent = Engine_Api::_()->getItem('mp3music_album', $item->album_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'mp3music_playlist_song':
               $parent = Engine_Api::_()->getItem('mp3music_playlist', $item->playlist_id);
               if ($parent) {
                   $permission =  Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($parent, $viewer, 'view') -> checkRequire();
               }
               else {
                   $permission = false;
               }
               break;
           
           case 'user':
           case 'blog':
           case 'classified':
           case 'poll':
           case 'ynauction_product':
           case 'contest':
           case 'forum_topic':
           case 'group':
           case 'ynwiki_page':
           case 'event':
           case 'album':
           case 'advalbum_album':
           case 'advalbum_photo':
           case 'music_playlist':
           case 'music_playlist_song':
           case 'ynfundraising_campaign':
           case 'mp3music_playlist':
           case 'mp3music_album':
           case 'groupbuy_deal':
           case 'folder':
           case 'video':           
               $permission = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($item, $viewer, 'view') -> checkRequire(); 
               break;
           
           case 'ynlistings_listing':
                $permission = true;        
           default:
               break;
        }
        return $permission;
    }
}