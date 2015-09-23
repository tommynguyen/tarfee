<?php
class Tfcampaign_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract {

    public function init() {
        $this
          ->setTitle('Member Level Settings');
        
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
    
            $this->addElement('Radio', 'create', array(
                'label' => 'Allow Creation of Campaign',
                'multiOptions' => array(
                    1 => 'Yes, allow users to create new campaign.',
                    0 => 'No, do not allow users to create new campaign.'
                ),
                'value' => 1,
            ));
            
            $this->addElement('Radio', 'edit', array(
                'label' => 'Allow Editing of Campaign',
                'multiOptions' => array(
                    2 => 'Yes, allow users to edit all campaigns.',
                    1 => 'Yes, allow users to edit their own campaigns.',
                    0 => 'No, do not allow users to edit their own campaigns.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
			if( !$this->isModerator() ) {
		      unset($this->edit->options[2]);
		    }
            
            $this->addElement('Radio', 'delete', array(
                'label' => 'Allow Deletion of Campaign',
                'multiOptions' => array(
                    2 => 'Yes, allow users to delete all campaigns.',
                    1 => 'Yes, allow users to delete their own campaigns.',
                    0 => 'No, do not allow users to delete their own campaigns.'
                ),
                'value' => ( $this->isModerator() ? 2 : 1 ),
            ));
			if( !$this->isModerator() ) {
		      unset($this->delete->options[2]);
		    }
            
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Campaign',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view campaigns.',
                    0 => 'No, do not allow users to view campaigns.'
                ),
                'value' => 1,
            ));
            
            
            $roles = array(
                'everyone' => 'Everyone',
                'registered' => 'All Registered Members',
                'owner_network' => 'Followers and Networks',
                'owner_member_member' => 'Followers of Followers',
                'owner_member' => 'My Followers',
                'owner' => 'Only Me'
            );
            
            $roles_values = array('everyone', 'registered', 'owner_network', 'owner_member_member', 'owner_member', 'owner');
            $auths = array('view');
            foreach ($auths as $auth) {
                $this->addElement('MultiCheckbox', 'auth_'.$auth, array(
                    'label' => ucfirst($auth).' Privacy',
                    'multiOptions' => $roles,
                    'value' => $roles_values        
                ));
            }
        }
        else {
            $this->addElement('Radio', 'view', array(
                'label' => 'Allow Viewing Details of Campaign',
                'multiOptions' => array(
                    1 => 'Yes, allow users to view campaigns.',
                    0 => 'No, do not allow users to view campaigns.'
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