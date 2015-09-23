<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Level.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynevent_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
        ->setTitle('Member Level Settings')
        ->setDescription('EVENT_FORM_ADMIN_LEVEL_DESCRIPTION');

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Events?',
      'description' => 'EVENT_FORM_ADMIN_LEVEL_VIEW_DESCRIPTION',
      'multiOptions' => array(
        2 => 'Yes, allow members to view all events, even private ones.',
        1 => 'Yes, allow viewing and subscription of photo events.',
        0 => 'No, do not allow photo events to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Events?',
        'description' => 'EVENT_FORM_ADMIN_LEVEL_CREATE_DESCRIPTION',
        'multiOptions' => array(
          1 => 'Yes, allow creation of events.',
          0 => 'No, do not allow events to be created.',
        ),
        'value' => 1,
      ));

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Events?',
        'description' => 'Do you want to let members edit and delete events?',
        'multiOptions' => array(
          2 => "Yes, allow members to edit everyone's events.",
          1 => "Yes, allow  members to edit their own events.",
          0 => "No, do not allow events to be edited.",
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Events?',
        'description' => 'Do you want to let members delete events? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all events.',
          1 => 'Yes, allow members to delete their own events.',
          0 => 'No, do not allow members to delete their events.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Events?',
        'description' => 'Do you want to let members of this level comment on events?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all events, including private ones.',
          1 => 'Yes, allow members to comment on events.',
          0 => 'No, do not allow members to comment on events.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Event Privacy',
        'description' => 'EVENT_FORM_ADMIN_LEVEL_AUTHVIEW_DESCRIPTION',
        'multiOptions' => array(
          'everyone' => 'Everyone',
          'registered' => 'Registered Members',
          'owner_network' => 'Followers and Networks (user events only)',
          'owner_member_member' => 'Followers of Followers (user events only)',
          'owner_member' => 'Followers Only (user events only)',
          'parent_member' => 'Club Members (club events only)',
          'member' => "Event Guests Only",
          //'owner' => 'Just Me'
        )
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Event Posting Options',
        'description' => 'EVENT_FORM_ADMIN_LEVEL_AUTHCOMMENT_DESCRIPTION',
        'multiOptions' => array(
          'registered' => 'Registered Members',
          'owner_network' => 'Followers and Networks (user events only)',
          'owner_member_member' => 'Followers of Followers (user events only)',
          'owner_member' => 'Followers Only (user events only)',
          'parent_member' => 'Club Members (club events only)',
          'member' => "Event Guests Only",
          'leader' => "Owner and Leader",
          'owner' => 'Just Me'
          
        )
      ));
		/*
      $this->addElement('Radio', 'style', array(
        'label' => 'Allow Profile Style',
        'required' => true,
        'multiOptions' => array(
          1 => 'Yes, allow custom profile styles.',
          0 => 'No, do not allow custom profile styles.'
        ),
        'value' => 1
      ));
      */
      
    }
  }

}