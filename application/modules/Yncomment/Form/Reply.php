<?php
class Yncomment_Form_Reply extends Engine_Form {

    public function init() {
        $this->clearDecorators()
                ->addDecorator('FormElements')
                ->addDecorator('Form')
                ->setAttrib('class', null)
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'comment',
                                ), 'default'));

        $viewer = Engine_Api::_()->user()->getViewer();
        $allowed_html = "";
        if ($viewer->getIdentity()) {
            $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
        }
        $this->addElement('Textarea', 'body', array(
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
        $this->addElement('Hidden', 'show_all_replies', array(
            'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('show_replies'),
        ));

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'ignore' => true,
            'label' => 'Post',
						'class' => 'mtop5 mbot5',
            'decorators' => array(
                'ViewHelper',
            )
        ));

        $this->addElement('Hidden', 'action_id', array(
            'order' => 990,
            'filters' => array(
                'Int'
            ),
        ));

        $this->addElement('Hidden', 'comment_id', array(
            'order' => 993,
            'filters' => array(
                'Int'
            ),
        ));

        $this->addElement('Hidden', 'return_url', array(
            'order' => 991,
            'value' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array())
        ));
    }

    public function setActionIdentity($comment_id) {

        $this
                ->setAttrib('id', 'activity-reply-form-' . $comment_id)
                ->setAttrib('class', 'activity-reply-form')
                ->setAttrib('style', 'display: none;');
        $this->comment_id
                ->setValue($comment_id)
                ->setAttrib('id', 'activity-reply-id-' . $comment_id);
        $this->submit //->getDecorator('HtmlTag')
                ->setAttrib('id', 'activity-reply-submit-' . $comment_id)
        ;

        $this->body
                ->setAttrib('id', 'activity-reply-body-' . $comment_id);
        return $this;
    }

    public function renderFor($comment_id) {
        return $this->setActionIdentity($comment_id)->render();
    }

}