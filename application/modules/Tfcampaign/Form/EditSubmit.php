<?php
class Tfcampaign_Form_EditSubmit extends Engine_Form
{
  protected $_campaign;
  
  public function setCampaign($campaign) {
    $this ->_campaign = $campaign;
  }
  
  public function getCampaign() {
  	return $this ->_campaign;
  }
  
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	
    $this->setTitle('Edit Player Submission');
    $this->setAttrib('class', 'global_form_popup');
	
	$errorMessage = array();
	$arrValue = array();
	$arrSubmission = array();
	$viewer = Engine_Api::_() -> user() -> getViewer();
	
		
	//Title
    $this->addElement('Text', 'title', array(
      'label' => 'Note',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> title -> setAttrib('required', true);
		
	$this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 300)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> description -> setAttrib('required', true);
	
	
	
	//if error disable this button
    $this->addElement('Button', 'submit_button', array(
      'label' => 'Save changes',
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
