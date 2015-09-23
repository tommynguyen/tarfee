<?php
class Ynfeedback_AdminButtonController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynfeedback_admin_main', array(), 'ynfeedback_admin_main_button');
    }
        
    public function indexAction() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->form = $form = new Ynfeedback_Form_Admin_Button_Settings();
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $formValues  = $form->getValues();
            $validValues = $values;
            $arr = array('ynfeedback_button_textcolor', 'ynfeedback_button_buttoncolor', 'ynfeedback_button_hovertextcolor', 'ynfeedback_button_hoverbuttoncolor');
            foreach ($arr as $index) {
                $validValues[$index] = $formValues[$index];
            }
            if (!$form->isValid($validValues)){
                return;
            }
            
            $values['icon'] = $formValues['icon'];
            $this->view->position = $values['position'];
            $arr = array('textcolor', 'buttoncolor', 'hovertextcolor', 'hoverbuttoncolor', 'position');
            foreach ($arr as $index) {
                if (isset($values[$index])) {
                    $new_index = 'ynfeedback_button_'.$index;
                    $form->$new_index->setValue('<input value="'.$values[$index].'" type="color" id="'.$index.'" name="'.$index.'"/>');
                    $values[$new_index] = $values[$index];
                    unset($values[$index]);
                }
            }
            
            if ($values['ynfeedback_button_type'] == 1) {
                if (empty($values['ynfeedback_button_text'])) {
                    $form->addError('Button Text is required!');
                    return;
                }
            }
            else {
                if (empty($values['ynfeedback_button_hovertext'])) {
                    $form->addError('Button Hover Text is required!');
                    return;
                }
            }
            if (!empty($values['icon'])) {
                Engine_Api::_()->ynfeedback()->setIconPhoto($form->icon);
            }
            foreach ($values as $key => $value) {
                if (array_search('ynfeedback_button_', $key) !== false && !empty($value)) {
                    $settings->setSetting($key, $value);
                }
            }
            $form->addNotice('Your changes have been saved.');
        }
    }
}
