<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Edit.php 10086 2013-09-16 19:27:24Z andres $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Authorization_Form_Admin_Level_Edit extends Authorization_Form_Admin_Level_Abstract
{
	public function init()
	{
		parent::init();

		// My stuff
		$this -> setTitle('Member Level Settings') -> setDescription("AUTHORIZATION_FORM_ADMIN_LEVEL_EDIT_DESCRIPTION");

		$this -> addElement('Text', 'title', array(
			'label' => 'Title',
			'allowEmpty' => false,
			'required' => true,
		));

		$this -> addElement('Textarea', 'description', array(
			'label' => 'Description',
			'allowEmpty' => true,
			'required' => false,
		));

		if (!$this -> isPublic())
		{

			// Show Professional Account Badge
			$this -> addElement('Radio', 'show_badge', array(
				'label' => 'Show Professional Account Badge?',
				'description' => 'Allow to see professional member badge on your profile picture',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				)
			));

			// Get available files
			$badgeOptions = array('' => '(No badge)');
			$imageExtensions = array(
				'gif',
				'jpg',
				'jpeg',
				'png'
			);

			$it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
			foreach ($it as $file)
			{
				if ($file -> isDot() || !$file -> isFile())
					continue;
				$basename = basename($file -> getFilename());
				if (!($pos = strrpos($basename, '.')))
					continue;
				$ext = strtolower(ltrim(substr($basename, $pos), '.'));
				if (!in_array($ext, $imageExtensions))
					continue;
				$badgeOptions['public/admin/' . $basename] = $basename;
			}

			$this -> addElement('Select', 'badge', array(
				'label' => 'Badge Icon',
				'description' => 'Badge icons are uploaded via the File Media Manager.',
				'multiOptions' => $badgeOptions,
			));
			$this->badge->getDecorator('Description')->setOption('placement', 'append');
			// Element: edit
			if ($this -> isModerator())
			{
				$this -> addElement('Radio', 'edit', array(
					'label' => 'Allow Profile Moderation',
					'required' => true,
					'multiOptions' => array(
						2 => 'Yes, allow members in this level to edit other profiles and settings.',
						1 => 'No, do not allow moderation.'
					),
					'value' => 0,
				));
			}

			// Element: style
			$this -> addElement('Radio', 'style', array(
				'label' => 'Allow Profile Style',
				'required' => true,
				'multiOptions' => array(
					2 => 'Yes, allow members in this level to edit other custom profile styles.',
					1 => 'Yes, allow custom profile styles.',
					0 => 'No, do not allow custom profile styles.'
				),
				'value' => 1,
			));
			if (!$this -> isModerator())
			{
				unset($this -> getElement('style') -> options[2]);
			}

			// Element: delete
			$this -> addElement('Radio', 'delete', array(
				'label' => 'Allow Account Deletion?',
				'multiOptions' => array(
					2 => 'Yes, allow members in this level to delete other users.',
					1 => 'Yes, allow members to delete their account.',
					0 => 'No, do not allow account deletion.',
				),
				'value' => 1,
			));
			if (!$this -> isModerator())
			{
				unset($this -> getElement('delete') -> options[2]);
			}
			$this -> delete -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: activity
			if ($this -> isModerator())
			{
				$this -> addElement('Radio', 'activity', array(
					'label' => 'Allow Activity Feed Moderation',
					'required' => true,
					'multiOptions' => array(
						1 => 'Yes, allow members in this level to delete any feed item.',
						0 => 'No, do not allow moderation.'
					),
					'value' => 0,
				));
			}

			// Element: block
			$this -> addElement('Radio', 'block', array(
				'label' => 'Allow Blocking?',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_BLOCK_DESCRIPTION',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				)
			));
			$this -> block -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: auth_view
			$this -> addElement('MultiCheckbox', 'auth_view', array(
				'label' => 'Profile Viewing Options',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_AUTHVIEW_DESCRIPTION',
				'multiOptions' => array(
					'everyone' => 'Everyone',
					'network' => 'My Network',
					'member' => 'My Followers',
					'owner' => 'Only Me',
				),
			));
			$this -> auth_view -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: auth_comment
			$this -> addElement('MultiCheckbox', 'auth_comment', array(
				'label' => 'Profile Commenting Options',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_AUTHCOMMENT_DESCRIPTION',
				'multiOptions' => array(
					'network' => 'My Network',
					'member' => 'My Followers',
					'owner' => 'Only Me',
				)
			));
			$this -> auth_comment -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: search
			$this -> addElement('Radio', 'search', array(
				'label' => 'Search Privacy Options',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_SEARCH_DESCRIPTION',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
			));
			$this -> search -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: status
			$this -> addElement('Radio', 'status', array(
				'label' => 'Allow status messages?',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_STATUS_DESCRIPTION',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				)
			));

			// Element: username
			$this -> addElement('Radio', 'username', array(
				'label' => 'Allow username changes?',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_USERNAME_DESCRIPTION',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				)
			));
			$this -> username -> getDecorator('Description') -> setOption('placement', 'PREPEND');

			// Element: quota
			$this -> addElement('Select', 'quota', array(
				'label' => 'Storage Quota',
				'required' => true,
				'multiOptions' => Engine_Api::_() -> getItemTable('storage_file') -> getStorageLimits(),
				'value' => 0, // unlimited
				'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_QUOTA_DESCRIPTION'
			));

			// Element: commenthtml
			$this -> addElement('Text', 'commenthtml', array(
				'label' => 'Allow HTML in Comments?',
				'description' => 'CORE_FORM_ADMIN_SETTINGS_GENERAL_COMMENTHTML_DESCRIPTION'
			));

			$this -> addElement('Radio', 'messages_auth', array(
 				'label' => 'Allow messaging?',
				'description' => 'Allow member to send message to friends',
 				'multiOptions' => array(
					'friends' => 'Yes',
					'none' => 'No',
 				)
 			));


			// Element: messages_editor
			$this -> addElement('Radio', 'messages_editor', array(
				'label' => 'Use editor for messaging?',
				'description' => 'USER_FORM_ADMIN_SETTINGS_LEVEL_MESSAGEEDITOR_DESCRIPTION',
				'multiOptions' => array(
					'editor' => 'Editor',
					'plaintext' => 'Plain Text',
				)
			));
			
			$this -> addElement('Radio', 'mail_auth', array(
				'label' => 'Allow send email?',
				'description' => 'Allow member to send email to non-friends',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No',
				),
				'value' => 1
			));
			
			$this->addElement('Integer', 'mess_day', array(
                'label' => 'Maximum messages user can send per day',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'mess_month', array(
                'label' => 'Maximum messages user can send per month',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'mail_day', array(
                'label' => 'Maximum emails user can send per day',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'mail_month', array(
                'label' => 'Maximum emails user can send per month',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));

			$this -> messages_auth -> getDecorator('Description') -> setOption('placement', 'PREPEND');
		}
	}

}
