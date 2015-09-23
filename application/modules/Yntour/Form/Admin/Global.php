<?php

class Yntour_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        $this -> setTitle('Global Settings') -> setDescription('These settings affect to adminnistrator member only.');

        $this -> addElement('Radio', 'yntourmode', array(
            'label' => 'Enable Edit Mode',
            'multiOptions' => array(
                'enabled' => 'Yes, Enable Edit Mode',
                'disabled' => 'No, Disable Edit Mode',
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yntourmode', 'disabled')
        ));

        $this -> addElement('Radio', 'yntourclean', array(
            'label' => 'Keep Edit Mode When Admin Logout',
            'multiOptions' => array(
                'enabled' => 'Yes, Keep edit mode when admin log out, and clear status when browser closed.',
                'disabled' => 'No, Clean edit mode when admin log out.',
            ),
            'value' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yntourclean', 'disabled')
        ));

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

    public function saveValues()
    {
        $values = $this -> getValues();
        $view = Zend_Registry::get('Zend_View');
        setcookie('yntourSecret', $values['yntourmode'], null,'/');
        Engine_Api::_() -> getApi('settings', 'core') -> setSetting('yntourclean', $values['yntourclean']);
        Engine_Api::_() -> getApi('settings', 'core') -> setSetting('yntourmode', $values['yntourmode']);
    }

}
