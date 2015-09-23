<?php
class User_Model_DbTable_Locations extends Engine_Db_Table {
    protected $_rowClass = 'User_Model_Location';
	
	public function getLocations($parent_id) {
		return $this->fetchAll($this->select()->where('parent_id = ?', $parent_id) -> order("title"));
	}
	
	public function getLocationsAssoc($parent_id) {
		$arr = array();
		$rows = $this->getLocations($parent_id);
		foreach ($rows as $row) {
			$arr[$row->getIdentity()] = $row->getTitle();
		}
		return $arr;
	}
	
	public function getCountriesByContinent($continent) {
		$select = $this->select()->where('continent = ?', $continent)->where('level = ?', 0) -> order("title");
		return $this->fetchAll($select);
	}
	
	public function getCountriesAssocByContinent($continent) {
		$arr = array();
		$rows = $this->getCountriesByContinent($continent);
		foreach ($rows as $row) {
			$arr[$row->getIdentity()] = $row->getTitle();
		}
		return $arr;
	}
	public function getConuntyIdByName($name = '')
	{
		$select = $this->select()->where('title LIKE ?', $name)->where('level = ?', 0)->limit(1);
		$country = $this -> fetchRow($select);
		if($country)
		{
			return $country -> location_id;
		}
		else {
			return 0;
		}
	}
	public function getCityByName($name = '')
	{
		$select = $this->select()->where('title LIKE ?', $name)->where('level = ?', 2)->limit(1);
		return $this -> fetchRow($select);
	}
}
