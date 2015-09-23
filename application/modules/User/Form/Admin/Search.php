<?php
class User_Form_Admin_Search extends Authorization_Form_Admin_Level_Abstract {
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
		
		$this->addElement('Integer', 'max_result', array(
            'label' => 'Maximum search results will appear for the user',
            'description' => 'Set 0 is unlimited',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 0,
        ));
		
		$this->addElement('Integer', 'max_keyword', array(
            'label' => 'Maximum search keywords can be conducted for the user',
            'description' => 'Set 0 is unlimited',
            'required' =>true,
            'validators' => array(
                new Engine_Validate_AtLeast(0),
            ),
            'value' => 0,
        ));
			
        $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
        ));  
        } 
}