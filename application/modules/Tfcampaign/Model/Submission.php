<?php

class Tfcampaign_Model_Submission extends Core_Model_Item_Abstract {
	
	
	public function getCampaign() {
		return Engine_Api::_() -> getItem('tfcampaign_campaign', $this -> campaign_id);
	}
	
	public function getPlayer() {
		return Engine_Api::_() -> getItem('user_playercard', $this -> player_id);
	}
	
	public function getReason() {
		$reasonTable = Engine_Api::_() -> getDbTable('reasons', 'tfcampaign');
		return $reasonTable -> getReason($this -> reason_id);
	}
	
	public function countPercentMatching(){
		$campaign = $this -> getCampaign();
		$player = $this -> getPlayer();
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
			if($countMatchingTotal > 0)
			{
				return round(($countMatching/$countMatchingTotal), 2) * 100;
			}	
		}
		return "0";
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
			throw new Tfcampaign_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'tfcampaign_campagin',
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
	
}
