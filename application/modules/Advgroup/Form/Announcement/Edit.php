<?php
class Advgroup_Form_Announcement_Edit extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Edit Announcement')
      ->setDescription('Please modifiy your announcement below.');

    // Add title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'required' => true,
      'allowEmpty' => false,
    ));

     $this->addElement('TinyMce', 'body', array(
      'editorOptions' => array(
          'bbcode' => 1,
          'html' => 1,
          'theme_advanced_buttons1' => array(
              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
              'media', 'image', 'link', 'unlink','fullscreen', 'preview', 'emotions'
          ),
          'theme_advanced_buttons2' => array(
              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
          ),
          'width' => '100%',
      ),
      'label' => 'Body',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Announcement',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'ignore' => true,
      'link' => true,
      'href' => '',
      'onclick' => 'javascript:history.go(-1);',
      'prependText' => Zend_Registry::get('Zend_Translate')->_(' or '),
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}