<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Rating extends Core_Model_Item_Abstract {

    public function getTable() {
        if (is_null($this->_table)) {
            $this->_table = Engine_Api::_()->getDbtable('ratings', 'video');
        }

        return $this->_table;
    }

    public function getOwner() {
        return parent::getOwner();
        // ?
        //return $this;
    }

}