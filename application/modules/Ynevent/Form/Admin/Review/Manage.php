<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Ynevent_Form_Admin_Review_Manage extends Engine_Form {

    public function init() {

        $this->addElement('Hidden', 'order', array('order' => 10004,));      
        // Element: direction
        $this->addElement('Hidden', 'direction', array('order' => 10005,));
       
    }

}

?>
