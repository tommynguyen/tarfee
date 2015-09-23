<?php

class Ynevent_Model_Rating extends Core_Model_Item_Abstract {

     protected $_type = 'event';
     protected $_owner_type = 'user';

     public function getTable() {
          if (is_null($this->_table)) {
               $this->_table = Engine_Api::_()->getDbtable('ratings', 'ynevent');
          }

          return $this->_table;
     }
}