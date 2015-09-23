<?php
class Ynfeedback_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings')
          ->setDescription('YNFEEDBACK_SETTINGS_LEVEL_DESCRIPTION');
        
        $levels = array();
        $table  = Engine_Api::_()->getDbtable('levels', 'authorization');
        foreach ($table->fetchAll($table->select()) as $row) {
            $levels[$row['level_id']] = $row['title'];
		}
		
        $this->addElement('Select', 'level_id', array(
            'label' => 'Member Level',
            'multiOptions' => $levels,
            'ignore' => true
        ));
        if( !$this->isPublic() ) {
    		if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                
            	$this->addElement('Integer', 'first_amount', array(
                    'label' => 'Credit for creating feedback',
                    'description' => 'No of First Actions',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'first_credit', array(
                    'description' => 'Credit/Action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                
                $this->addElement('Integer', 'credit', array(
                    'description' => 'Credit for next action',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'max_credit', array(
                    'description' => 'Max Credit/Period',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(0),
                    ),
                    'value' => 0,
                ));
                $this->addElement('Integer', 'period', array(
                    'description' => 'Period (days)',
                    'required' =>true,
                    'validators' => array(
                        new Engine_Validate_AtLeast(1),
                    ),
                    'value' => 1,
                ));
            }
            
            $this->addElement('Integer', 'max_screenshot', array(
                'label' => 'Maximum screenshots this member can add for each feedback',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 5,
            ));
            
            $this->addElement('Integer', 'max_screenshotsize', array(
                'label' => 'Maximum screenshot files size (KB)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => 1000,
            ));
            
            $this->addElement('Integer', 'max_file', array(
                'label' => 'Maximum files this member can add for each feedback',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 5,
            ));
            
            $this->addElement('Integer', 'max_filesize', array(
                'label' => 'Maximum files size (KB)',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(1),
                ),
                'value' => 1000,
            ));
            
            $this->addElement('Text', 'file_ext', array(
                'label' => 'File extension',
                'description' => 'If you want to allow specific file extensions, you can enter them below (separated by commas). Example: txt, pdf, ppt, doc. Leave blank for any file type.',
                'value' => '',
            ));
            
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow viewing of feedback',
                'description' => 'Do you want to let members of this level view feedbacks?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to view feedbacks.',
                    0 => 'No, do not allow members to view feedbacks.'
                ),
                'value' => 1,
            ));
                
			$this->addElement('Radio', 'create', array(
                'label' => 'Allow creation of feedback',
                'description' => 'Do you want to let members of this level create new feedback?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to create new feedback.',
                    0 => 'No, do not allow members to create new feedback.'
                ),
                'value' => 1,
            ));
			
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow editing of feedback',
                'description' => 'Do you want to let members of this level delete feedbacks?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to edit all feedbacks.',
                    1 => 'Yes, allow members to edit their own feedbacks.',
                    0 => 'No, do not allow members to edit their own feedbacks.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete->options[2]);
            }
            
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow deletion of feedback',
                'description' => 'Do you want to let members of this level edit feedbacks?',
                'multiOptions' => array(
                    2 => 'Yes, allow members to delete all feedbacks.',
                    1 => 'Yes, allow members to delete their own feedbacks.',
                    0 => 'No, do not allow members to delete their own feedbacks.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
            if( !$this->isModerator() ) {
                unset($this->delete->options[2]);
            }
            
            $this->addElement('Radio', 'comment', array(
                'label' => 'Allow commenting on feedback',
                'description' => 'Do you want to let members of this level comment on feedbacks?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to comment on feedbacks.',
                    0 => 'No, do not allow members to comment on feedbacks.'
                ),
                'value' => 1,
            ));
            
            $roles = array(
                'everyone' => 'Everyone',
                'owner_network' => 'Friends and Networks',
                'owner_member_member' => 'Friends of Friends',
                'owner_member' => 'Friends Only',
                'owner' => 'Just Me'
            );
            
            $roles_values = array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
            
            $this->addElement('MultiCheckbox', 'auth_view', array(
                'label' => 'Feedback Privacy',
                'description' => 'YNFEEDBACK_AUTH_VIEW_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values        
            ));
            
            $this->addElement('MultiCheckbox', 'auth_comment', array(
                'label' => 'Feedback Privacy',
                'description' => 'YNFEEDBACK_AUTH_COMMENT_DESCRIPTION',
                'multiOptions' => $roles,
                'value' => $roles_values        
            ));
        }
        else {
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow viewing of feedback',
                'description' => 'Do you want to let members of this level view feedbacks?',
                'multiOptions' => array(
                    1 => 'Yes, allow members to view feedbacks.',
                    0 => 'No, do not allow members to view feedbacks.'
                ),
                'value' => 1,
            ));
        } 
        $this->addElement('Button', 'submit_btn', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));        
    }
}