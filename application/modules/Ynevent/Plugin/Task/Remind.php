<?php

class Ynevent_Plugin_Task_Remind extends Core_Plugin_Task_Abstract {

     public function execute() 
     {
          $viewer = Engine_Api::_()->user()->getViewer();
          $table = Engine_Api::_()->getDbTable("remind", "ynevent");
          $reminds = $table->getRemindEvents($viewer->getIdentity());
          if (count($reminds)) 
          {
               $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
               foreach ($reminds as $remind) 
               {
                    $event = $remind;
                    $notifyApi->addNotification($viewer, $viewer, $event, 'ynevent_remind');
                    $remind->is_read = 1;
                    $remind->save();
               }
          }
     }

}

