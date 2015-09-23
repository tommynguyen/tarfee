<?php
class User_Form_Library_MoveToSub extends Engine_Form
{
  protected $_library;
  protected $_subs;
  
  public function getLibrary(){
  	return $this ->_library;
  }
  
  public function setLibrary($library) {
  	$this ->_library = $library;
  }
  
  public function getSubs(){
  	return $this ->_subs;
  }
  
  public function setSubs($subs) {
  	$this ->_subs = $subs;
  }
  
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	
    $this->setTitle('Move Video');
    $this->setAttrib('class', 'global_form_popup');
    $this->setDescription('Are you sure that you want to move this video?');
	
	
	$arrValue = array();
	foreach($this ->_subs as $sub) {
		if($sub -> isSelf($this ->_library)) {
			continue;
		}
		$arrValue[$sub -> getIdentity()] = $view -> translate($sub -> getTitle());
	}
	
	$move_type = array('library' => 'Library');
	if (Engine_Api::_()->user()->canTransfer()) {
		$move_type['group'] = 'Club';
	}
	
	$this->addElement('Select', 'move_type', array(
		'label' => 'Move To',
		'multiOptions' => $move_type
	));
	
	 $this->addElement('Select', 'move_to', array(
	  'required' => true,
      'label' => 'Library',
      'multiOptions' => $arrValue,
    ));
		
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Move',
      'onclick' => 'removeSubmit()',
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
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    
	 $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
