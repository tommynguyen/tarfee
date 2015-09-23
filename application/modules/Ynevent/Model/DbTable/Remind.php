<?php

class Ynevent_Model_DbTable_Remind extends Engine_Db_Table {

     protected $_type = 'event';
     protected $_name = 'event_remind';
	    private static $_log;
	public function getLog()
    {
        if (self::$_log == null)
        {
            self::$_log = new Zend_Log(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/_a.log'));
        }
        return self::$_log;
    }

    /**
     * write log to temporary/log/headscript.log
     * @param string $intro
     * @param string $message
     * @param string $type [Zend_Log::INFO]
     */
    public function log($intro = null, $message, $type)
    {
        return $this -> getLog() -> log(PHP_EOL . $intro . PHP_EOL . $message, $type);
    }
	
     public function getRemindRow($event_id, $user_id) {
          $select = $this->select();
          $select->where('resource_id=?', $event_id)
                  ->where('user_id=?', $user_id);


          return $this->fetchRow($select);
     }

     public function getRemindEvents($user_id) {
          $table = Engine_Api::_()->getDbTable('events', 'ynevent');
          $select = $table->select();
          $tableEventName = $table->info('name');

          $tableRemind = Engine_Api::_()->getDbTable('remind', 'ynevent');
          $tableRemindName = $tableRemind->info('name');
          $select->setIntegrityCheck(false)
                  ->from($tableEventName, array("$tableEventName.*"))
                  ->join($tableRemindName, "$tableRemindName.resource_id=$tableEventName.event_id", '')
                  ->where("$tableRemindName.user_id = ?", $user_id)
                  ->where("$tableRemindName.is_read =?", 0)
                  ->where("$tableEventName.starttime >= FROM_UNIXTIME(?)", time())
                  ->where("$tableRemindName.remind_time <= FROM_UNIXTIME(?)", time())

          ;     

          return $table->fetchAll($select);
     }

     public function setRemindTime($event_id, $user_id, $remain) {
          if ($remain < 0) {
               return;
          }
          $eventTable = Engine_Api::_()->getDbTable("events", "ynevent");
          $event = $eventTable->getEventById($event_id);
          if (is_object($event))  {
				$dayRemind = strtotime("-$remain minutes", strtotime($event->starttime));
	          	$dayRemind = date('Y-m-d H:i:s', $dayRemind);      
	          	$remind = $this->getRemindRow($event_id, $user_id);
	          	if (!count($remind)) {
	               $remind = $this->createRow();
	               $remind->resource_id = $event_id;
	               $remind->user_id = $user_id;
	          	}
	          	$remind->is_read = 0;
	          	$remind->remain_time = $remain;
	          	$remind->remind_time = $dayRemind;
	          	$remind->save();
          }
          
     }     

}