<?php
class User_Form_Admin_Settings_Section extends Authorization_Form_Admin_Level_Abstract {
  	public function init() {
    	$this
      	->setTitle('Section Settings for Member Levels')
      	->setDescription('Specify what profile section settings will be available to members in this level.');

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
            $this->addElement('Integer', 'bio_max', array(
                'label' => 'Maximum character of bio the user can add',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'archievement_max', array(
                'label' => 'Maximum trophies and archievements the user can add',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'archievement_descriptionmax', array(
                'label' => 'Maximum character of trophy/archievement Short description the user can add',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Radio', 'license', array(
				'label' => 'enable "Licenses & Certificates"?',
				'description' => 'enable section "Licenses & Certificates" on user Profile',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1 
			));
			
			$this->addElement('Radio', 'experience', array(
				'label' => 'Enable "Work Experience"?',
				'description' => 'Enable section "Work Experience" on user Profile',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1 
			));
			
			$this->addElement('Radio', 'education', array(
				'label' => 'enable "Education"?',
				'description' => 'enable section "Education" on user Profile',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1 
			));
			
			$this->addElement('Radio', 'recommendation', array(
				'label' => 'Enable "Recommendation"?',
				'description' => 'Enable section "Recommendation" on user Profile',
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => 1 
			));
			
			$this->addElement('Integer', 'max_sport', array(
                'label' => 'Maximum sports the user can add to their profile',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
			
			$this->addElement('Integer', 'max_club', array(
                'label' => 'Maximum club the user can add to their profile',
                'description' => 'Set 0 is unlimited',
                'required' =>true,
                'validators' => array(
                    new Engine_Validate_AtLeast(0),
                ),
                'value' => 0,
            ));
        }
        
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));  
        } 
}