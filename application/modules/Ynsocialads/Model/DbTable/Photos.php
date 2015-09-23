<?php

class Ynsocialads_Model_DbTable_Photos extends Engine_Db_Table
{
  protected $_rowClass = 'Ynsocialads_Model_Photo';
   protected $_name = 'ynsocialads_photos';
   
   public function getPhotosAd($ad_id) {
   	 $select = $this -> select() -> where('ad_id = ?', $ad_id);
	 return $this -> fetchAll($select);
   }
}
