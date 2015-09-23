<?php

class Ynevent_RemindController extends Core_Controller_Action_Standard {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getDbTable("remind", "ynevent");
        $reminds = $table->getRemindEvents($viewer->getIdentity());
        if (count($reminds)) {
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

            foreach ($reminds as $event) {
               
                $date = $this->view->locale()->toDateTime($event->starttime);
                $params = array("label" => $date);
                $notifyApi->addNotification($viewer, $viewer, $event, 'ynevent_remind', $params);
                //set remind is read
                $remind = $table->getRemindRow($event->event_id, $viewer->getIdentity());
                $remind->is_read = 1;
                $remind->save();
            }
        }
        $this->_helper->layout->disableLayout();
    }

}

?>
