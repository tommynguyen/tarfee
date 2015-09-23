<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynevent
 * @author     YouNet Company
 */

class Ynevent_Model_DbTable_Countries extends Engine_Db_Table {
	protected $_rowClass = 'Ynevent_Model_Country';
	protected $_name = 'event_countries';

	public static function getMultiOptions() {
		$t = new self;
		$select = $t -> select() -> where('status= 1') -> order('name');
		$result = array();
		$result[''] = '';
		foreach($t->fetchAll($select) as $item) {
			$result[$item -> iso_code_3] = $item -> name;
		}
		return $result;
	}
	/**
	 *
	 * get multi Options country for google map
	 */
	public static function getMapMultiOptions() {
		$t = new self;
		$select = $t -> select() -> where('status= 1') -> order('name');
		$result = array();
		$result[''] = '';
		foreach($t->fetchAll($select) as $item) {
			$result[$item -> name] = $item -> name;
		}
		return $result;
	}

	/**
	 *
	 * @param text $name
	 * @return Country model
	 *
	 */
	public function getCountry($name = NULL) {
		if($name == NULL) {
			$name = Engine_Api::_() -> getApi('core','ynevent') -> getDefaultCountry();
		}
		$item = $this -> find($name) -> current();
		if(!is_object($item)) {
			$name = Engine_Api::_() -> getApi('core','ynevent') -> getDefaultCountry();
			$item = $this -> find($name) -> current();
		}
		return $item;

	}

	public function getCountryName($code = NULL) {

		$table = new self;
	    $rName = $table->info('name');
	    $select = $table->select()->from($rName)  ;
	    $select->where('iso_code_3 = ?', $code);
	    $item = $table->fetchRow($select);

		return $item->name;

	}


}