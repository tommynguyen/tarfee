<?php
class User_Model_Playercard extends Core_Model_Item_Abstract
{
	public function countPercentMatching($campaign){
		$campaign = $campaign;
		$player = $this;
		if($campaign && $player) {
			$countMatching = 0;
			$countMatchingTotal = 0;
			//age matching
			if($campaign -> from_age != 0 || $campaign -> to_age != 0) {
				$countMatchingTotal++;
				$today = new DateTime();
				$birthdate = new DateTime($player -> birth_date);
				$interval = $today->diff($birthdate);
				$player_age =  $interval->format('%y');
				if($campaign -> from_age == 0 && $campaign -> to_age != 0) {
					//max age
					if($player_age <= $campaign -> to_age) {
						$countMatching++;
					}
				} else if($campaign -> from_age != 0 && $campaign -> to_age == 0) {
					//min age
					if($player_age >= $campaign -> from_age) {
						$countMatching++;
					}
				} else if($campaign -> from_age != 0 && $campaign -> to_age != 0) {
					//between
					if($player_age >= $campaign -> from_age && $player_age <= $campaign -> to_age) {
						$countMatching++;
					}
				}
			}
			//gender matching
			if($campaign -> gender != 0) {
				$countMatchingTotal++;
				if($player -> gender == $campaign -> gender) {
					$countMatching++;
				}
			}
			//category matching
			if($campaign -> category_id != 0) {
				$countMatchingTotal++;
				if($player -> category_id == $campaign -> category_id) {
					$countMatching++;
				}
				//position matching
				if($campaign -> position_id != 0) {
					$countMatchingTotal++;
					if($player -> position_id == $campaign -> position_id) {
						$countMatching++;
					}
				}
				//referred foot matching (for category id = 2)
				if($campaign -> category_id == 2) {
					$countMatchingTotal++;
					if($player -> referred_foot == $campaign -> referred_foot) {
						$countMatching++;
					}
				}
			}
			//country matching
			if($campaign -> country_id != 0) {
				$countMatchingTotal++;
				if($player -> country_id == $campaign -> country_id) {
					$countMatching++;
				}
				//province matching
				if(isset($campaign -> province_id)) {
					$countMatchingTotal++;
					if($player -> province_id == $campaign -> province_id) {
						$countMatching++;
					}
					//city matching
					if(isset($campaign -> city_id)) {
						$countMatchingTotal++;
						if($player -> city_id == $campaign -> city_id) {
							$countMatching++;
						}
					}
				}
			}
			//language matching
			$player_languages = json_decode($player->languages);
			$campaign_languages = json_decode($campaign->languages);
			if(!empty($campaign_languages)) 
			{
				$countMatchingTotal++;
				$countMLanguage = 0;
				foreach ($campaign_languages as $value) {
					if(in_array($value, $player_languages))
					{
						$countMLanguage ++;
					}
				}
				if($countMLanguage == count($campaign_languages))
				{
					$countMatching++;
				}
			}
			if($countMatchingTotal > 0){
				return round(($countMatching/$countMatchingTotal), 2) * 100;
			}	
		}
		return "0";
	}
	
	public function getTitle()
	{
		return $this -> first_name . ' ' . $this -> last_name;
	}

	public function getHref($params = array())
	{
		$params = array_merge(array(
			'route' => 'playercard_profile',
			'reset' => true,
			'id' => $this -> getIdentity(),
			'slug' => $this -> getSlug(),
		), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function setPhoto($photo)
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
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'player_card',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iMain -> bridge($iProfile, 'thumb.profile');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}

	public function getOverallRating()
	{
		$mappingTable = Engine_Api::_() -> getDbTable('mappings', 'user');
		$ratingTable = Engine_Api::_() -> getDbTable('reviewRatings', 'ynvideo');
		$params = array(
			'owner_id' => $this -> getIdentity(),
			'owner_type' => $this -> getType(),
		);
		$type = 'video';
		$videoIds = $mappingTable -> getItemIdsMapping($type, $params);
		$totalOverallRating = 0;
		$totalOverallRatingReview = 0;
		
		//get all videos of player
		foreach ($videoIds as $video_id)
		{
			//loop for each video
			$video = Engine_Api::_() -> getItem('video', $video_id);
			//check video exist
			if ($video)
			{
				//get all user add ratings for this video
				$userIds = $ratingTable -> getUserRatingByResource($video_id);
				$videoOverrallRating = $video -> getRating(true);
				if($videoOverrallRating != 0){
					$totalOverallRating += $videoOverrallRating;
					$totalOverallRatingReview += count($userIds);
				}
			}
		}
		if ($totalOverallRatingReview != 0)
		{
			$total = round(($totalOverallRating / $totalOverallRatingReview), 2);
			return $total;
		}
		else
			return "0";
	}
	
	function isViewable() {
		//get viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (Engine_Api::_()->user()->itemOfDeactiveUsers($this)) {
			return false;
		}
		//view for specific users
		$tableUserItemView = Engine_Api::_() -> getDbTable('userItemView', 'user');
		$userViewRows = $tableUserItemView -> getUserByItem($this);
		foreach($userViewRows as $userViewRow) {
			$user = Engine_Api::_() -> getItem('user', $userViewRow -> user_id);
			if($user -> getIdentity() && $viewer -> isSelf($user)) {
				return true;
			}
		}
        return $this->authorization()->isAllowed(null, 'view'); 
    }
	
	function isEyeOn($user_id = null) {
		if (!$user_id) {
            $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        }
        return Engine_Api::_()->getDbTable('eyeons', 'user')->isEyeOn($user_id, $this->getIdentity());
	}
	
	function getEyeOns() {
		return Engine_Api::_()->getDbTable('eyeons', 'user')->getPlayerEyeOns($this->getIdentity());
	}
	
	public function getPhotosTotal() {
		$photoTable = Engine_Api::_() -> getItemTable('user_photo');
		$select = $photoTable -> select();
    	$select -> from($photoTable->info('name'), 'COUNT(*) AS count')
				-> where('item_id = ?', $this -> getIdentity())
				-> where('item_type = ?', $this -> getType());
    	return $select->query()->fetchColumn(0);
	}
	public function getSport()
	{
		return Engine_Api::_() -> getItem('user_sportcategory', $this -> category_id);
	}
	
	public function getPosition()
	{
		return Engine_Api::_() -> getItem('user_sportcategory', $this -> position_id);
	}
	
	public function getSportId() {
		return $this->category_id;
	}
	public function getTotalVideo()
	{
		$params = array();
		$params['owner_type'] = $this -> getType();
		$params['owner_id'] = $this -> getIdentity();
		$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
		return $mappingTable -> getTotalVideo($params);
	}
}
