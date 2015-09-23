<?php

class Slprofileverify_Form_Custom_Fields extends Fields_Form_Standard {

    public $_error = array();
    protected $_name = 'fields';
    protected $_elementsBelongTo = 'fields';

    public function init() {
        parent::init();
        $this->removeElement('submit');
    }

    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }
        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements');
        }
    }

}