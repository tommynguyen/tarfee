<?php
class Ynfeedback_Form_Admin_Status_Edit extends Ynfeedback_Form_Admin_Status_Create {  
    public function init() {
        parent::init();
        $this -> setTitle('Edit Status');
        $this -> submit_btn -> setLabel('Edit');
    }
}
