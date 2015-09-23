<?php
class Yncomment_Form_Feed_Comment extends Engine_Form
{
  public function init()
  {
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
    if($viewer->getIdentity()){
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

    if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ) {
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
    }
    
    $this->addElement('Hidden', 'show_all_comments', array(
        'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('show_comments'),
    ));

    $this->addElement('Button', 'submit', array(
      'type' => 'submit',
      'ignore' => true,
      'label' => 'Post',
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

    $this->addElement('Hidden', 'return_url', array(
      'order' => 991,
      'value' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array())
    ));    
    
 }

  public function setActionIdentity($action_id)
  {
    if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ) {
    $this
      ->setAttrib('style', 'display: none;');
    } else {
    $this
      ->setAttrib('id', 'activity-comment-form-'.$action_id)
      ->setAttrib('class', 'activity-comment-form')
      ->setAttrib('style', 'display: none;');
    }
    $this->action_id
      ->setValue($action_id)
      ->setAttrib('id', 'activity-comment-id-'.$action_id);
    $this->submit
      ->setAttrib('id', 'activity-comment-submit-'.$action_id)
      ;

    $this->body
      ->setAttrib('id', 'activity-comment-body-'.$action_id);

    return $this;
  }

  public function renderFor($action_id)
  {
    return $this->setActionIdentity($action_id)->render();
  }
}