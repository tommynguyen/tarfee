<?php

class Ynevent_Form_InviteGroups extends Engine_Form {

     public function init() {
          $settings = Engine_Api::_()->getApi('settings', 'core');
          $this->setTitle('Invite Members')
                  ->setDescription('Choose the people you want to invite to this event.')
                  ->setAttrib('id', 'ynevent_form_invite_groups');

          $this->addElement('Checkbox', 'all', array(
              'id' => 'selectall',
              'label' => 'Choose All Groups',
              'ignore' => true
          ));

          $this->addElement('MultiCheckbox', 'users', array(
              'label' => 'Members',
              'allowEmpty' => 'false',
          ));

          $this->addElement('Button', 'submit', array(
              'label' => 'Send Invites',
              'type' => 'submit',
              'ignore' => true,
              'decorators' => array(
                  'ViewHelper',
              ),
          ));

          $this->addElement('Cancel', 'cancel', array(
              'label' => 'cancel',
              'link' => true,
              'prependText' => ' or ',
              'onclick' => 'parent.Smoothbox.close();',
              'decorators' => array(
                  'ViewHelper',
              ),
          ));

          $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
     }     
}