<?php
class Yncomment_Form_Comment_Create extends Engine_Form {

    protected $_textareaId;

    public function getTextareaId() {
        return $this->_textareaId;
    }

    public function setTextareaId($textarea_id) {
        $this->_textareaId = $textarea_id;
        return $this;
    }

    public function init() {
        $this->clearDecorators()
                ->addDecorator('FormElements')
                ->addDecorator('Form')
                ->setAttrib('class', null)
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        // Member Level specific 
        $viewer = Engine_Api::_()->user()->getViewer();
        $allowed_html = "";
        if ($viewer->getIdentity()) {
            $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
        }
        $this->addElement('Textarea', 'body', array(
            'id' => $this->getTextareaId(),
            'rows' => 1,
            'decorators' => array(
                'ViewHelper'
            ),
            'filters' => array(
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
            ),
        ));
        if($allowed_html)
        {
            $this -> body -> addFilter(new Engine_Filter_Html(array('AllowedTags' => $allowed_html)));
        }
        
        if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment) {
            $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
        }

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'label' => 'Post',
            'decorators' => array(
                'ViewHelper',
            )
        ));

        $this->addElement('Hidden', 'type', array(
            'order' => 990,
            'validators' => array(
            ),
        ));

        $this->addElement('Hidden', 'parent_comment_id', array(
            'order' => 992,
            'value' => 0
        ));

        $this->addElement('Hidden', 'comment_id', array(
            'order' => 993,
            'value' => 0
        ));

        $this->addElement('Hidden', 'identity', array(
            'order' => 991,
            'validators' => array(
                'Int'
            ),
        ));
    }

}