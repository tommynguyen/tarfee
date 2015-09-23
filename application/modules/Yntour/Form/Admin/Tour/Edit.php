<?php

class Yntour_Form_Admin_Tour_Edit extends Yntour_Form_Admin_Tour_Create
{
    public function init()
    {
        parent::init();
        $this -> setAttrib('title', 'Edit Tour Guide');
    }

}
