<?php
class Yncomment_Form_Admin_Global extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Global Settings')
                ->setName('yncomment_global_settings')
                ->setDescription('These settings affect all members in your community.');

        $coreSettingsApi = Engine_Api::_()->getApi('settings', 'core');

        $this->addElement('Radio', 'yncomment_comment_pressenter', array(
            'label' => 'Posting Comments on Enter',
            'description' => 'Do you want to post the comments by pressing “Enter” key?',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $coreSettingsApi->getSetting('yncomment.comment.pressenter', 1),
        ));
        
        $this->addElement('Text', 'yncomment_comment_per_page', array(
            'label' => 'Comments per page',
            'description' => 'How many comments do you want to show per content page?(“View more comments” option is available)',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettingsApi->getSetting('yncomment.comment.per.page', 10),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        
        $this->addElement('Text', 'yncomment_reply_per_page', array(
            'label' => 'Replies per comment',
            'description' => 'How many replies do you want to show on each comment per content page?(“View more replies” option is available)',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                'StripTags',
                new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $coreSettingsApi->getSetting('yncomment.reply.per.page', 4),
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
        ));
        
        $this->addElement('Radio', 'yncomment_reply_link', array(
          'label' => 'Reply Link',
          'description' => 'Where do you want to show “Reply” link?',
          'multiOptions' => array( 
              1 => 'Both comments and replies',
              0 => 'Comments only'
          ),
          'value' => $coreSettingsApi->getSetting('yncomment.reply.link', 1)
        ));
          
        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}