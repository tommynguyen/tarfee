<?php
class Ynfeedback_Widget_FeedbackButtonController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        //get all button Settings
        $isMobile = Engine_Api::_()->ynfeedback()->isMobile();
        if ($isMobile) {
            $this->setNoRender();
            return;
        }
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $this->view->buttoncolor = $buttoncolor = $settings->getSetting('ynfeedback_button_buttoncolor', '#2BA8E2');
        $this->view->hoverbuttoncolor = $hoverbuttoncolor = $settings->getSetting('ynfeedback_button_hoverbuttoncolor', '#2BA8E2');
        $this->view->hovertext = $hovertext = $settings->getSetting('ynfeedback_button_hovertext', 'Feedback');
        $this->view->hovertextcolor = $hovertextcolor = $settings->getSetting('ynfeedback_button_hovertextcolor', '#FFFFFF');
        $this->view->icon = $icon = $settings->getSetting('ynfeedback_button_icon', '');
        $this->view->left = $left = $settings->getSetting('ynfeedback_button_left', 1);
        $this->view->position = $position = $settings->getSetting('ynfeedback_button_position', 50);
        $this->view->text = $text = $settings->getSetting('ynfeedback_button_text', 'Feedback');
        $this->view->textcolor = $textcolor = $settings->getSetting('ynfeedback_button_textcolor', '#FFFFFF');
        $this->view->type = $type = $settings->getSetting('ynfeedback_button_type', 1);
    }
}
