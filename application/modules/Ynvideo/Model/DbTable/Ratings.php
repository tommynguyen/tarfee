<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_DbTable_Ratings extends Engine_Db_Table {
    protected $_rowClass = "Ynvideo_Model_Rating";
    protected $_name = 'video_ratings';
}