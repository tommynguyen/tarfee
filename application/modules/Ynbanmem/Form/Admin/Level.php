<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynbanmem
 * @author     YouNet Company
 */
class Ynbanmem_Form_Admin_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        parent::init();

        // My stuff
        //$this->setTitle('Member Level Settings')->setDescription("BANMEMBERS_FORM_ADMIN_LEVEL_DESCRIPTION");

        if (!$this->isPublic()) {
        //  Element: Manage Banned
            $this->addElement('Radio', 'manage', array(
                'label' => 'Allow manage baned Users/IPs?',
                'description' => 'Do you want to let members view and manage banned Users/IPs?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
           
             //  Element: Ban 
            $this->addElement('Radio', 'add', array(
                'label' => 'Allow banning Users/Ip?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
            
            // Element: Extra information
            $this->addElement('Radio', 'view_extra', array(
                'label' => 'Allow viewing the extra profile information?',
                'description' => 'Do you want to let members view the extra information of user (user ID, email,... )?',
                'multiOptions' => array(
                    1 => 'Yes, allow viewing.',
                    0 => 'No, do not allow this information to be viewed.'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
            
            // Element: Delete
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow deleting user?',
                'description' => 'Do you want to let members delete the other user?.',
                'multiOptions' => array(
                    1 => 'Yes, allow deleting.',
                    0 => 'No, do not allow.'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
            
           

            // Element: login as another user
            $this->addElement('Radio', 'login', array(
                'label' => 'Allow the member to log in as another user?',
                'description' => 'Do you want to let members to log in as another user?.',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
            
           
            // Element: view user's notes
            $this->addElement('Radio', 'note', array(
                'label' => 'Allow viewing and adding notes for users when viewing their profiles?',
                'description' => 'Do you want to let members view and add notes for another users when viewing their profiles?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
            
			
			 // Element: action button
            $this->addElement('Radio', 'action', array(
                'label' => 'Allow issuing a "Notice", "Warning" or "Infraction" to the user.',
                'description' => 'Do you want to let members to issue a "Notice", "Warning" or "Infraction" to the user?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
			
			// Element: remove 
            $this->addElement('Radio', 'remove', array(
                'label' => 'Allow deleting a "Notice", "Warning" or "Infraction"',
                'description' => 'Do you want to let members delete a "Notice", "Warning" or "Infraction"?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
			
			$this->addElement('Radio', 'manage_user', array(
                'label' => 'Allow member manage users',
                'description' => 'Do you want to let members manage users?',
                'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
                ),
                'value' => ($this->isModerator() ? 1 : 0)
            ));
			
			
        }
    }

}