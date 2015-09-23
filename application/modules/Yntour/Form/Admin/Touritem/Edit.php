<?php

class Yntour_Form_Admin_Touritem_Edit extends Yntour_Form_Admin_Touritem_Create{
    public function init(){
        parent::init();
        $this->setAttrib('title', 'Edit Tour Guide Step');
    }
}
