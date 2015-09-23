<?php
class User_Form_Library_Delete extends Engine_Form
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
	
    $this->setTitle('Delete Sub Library');
    $this->setAttrib('class', 'global_form_popup');
    $this->setDescription('Are you sure that you want to delete this library? It will not be recoverable after being deleted.');
	
	
	//get table 
	$mappingTable = Engine_Api::_()->getDbTable('mappings', 'user');
	
	//get videos mapping of library
	$params = array();
    $params['owner_type'] = $this ->_library -> getType();
	$params['owner_id'] = $this ->_library -> getIdentity();
	$videoMappings = $mappingTable -> getItemsMapping('video', $params);
	
	if(count($this ->_subs) && count($videoMappings)) {
		
		//get main Library
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$mainLibrary = $viewer -> getMainLibrary();
		$arrValue = array();
		$arrValue[0] = $view -> translate('None');
		$arrValue[$mainLibrary -> getIdentity()] = $view -> translate($mainLibrary -> getTitle());
		foreach($this ->_subs as $sub) {
			if($sub -> isSelf($this ->_library)) {
				continue;
			}
			$arrValue[$sub -> getIdentity()] = $view -> translate($sub -> getTitle());
		}
		
		 $this->addElement('Select', 'move_to', array(
	      'label' => 'Move to Library?',
	      'description' => 'If you delete this library, all existing content will be moved to another one.',
	      'multiOptions' => $arrValue,
	    ));
		
	}
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Delete',
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
