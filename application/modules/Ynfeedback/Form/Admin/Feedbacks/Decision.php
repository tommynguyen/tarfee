<?php
class Ynfeedback_Form_Admin_Feedbacks_Decision extends Engine_Form
{
  
  protected $_item;
	
	public function getItem()
	{
		return $this -> _item;
	}
	
	public function setItem($item)
	{
		$this -> _item = $item;
	} 
	
  public function init()
  {
    //Set form attributes
    $this->setTitle('Make decision on Feedback')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');
      ;
	
	//Feedback Information
	$this ->addElement('heading', 'feedback_info_heading', array(
		'label' => 'Feedback Information',
	));
	
	if($this -> _item)
	{
		$this -> addElement('dummy', 'feedback_heading', array(
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_idea_info.tpl',
						'idea' =>  $this -> _item,
						'class' => 'form element',
					)
				)), 
		));  
	}
	
	//Your Decision
	$this ->addElement('heading', 'decision_heading', array(
		'label' => 'Your Decision',
		'value' => $this -> _item -> decision,
	));
	
	// Status
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'value' => $this -> _item -> status_id,
    ));
	$statusLists = Engine_Api::_() -> getDbTable('status', 'ynfeedback') -> getStatusList();
	unset($statusLists[0]);
	$this->status->addMultiOptions($statusLists);
	
	// Decision
    $this->addElement('Textarea', 'decision', array(
      'label' => 'Decision',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        	new Engine_Filter_HtmlSpecialChars(),
        	new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        ),
      'value' => $this -> _item -> decision,
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
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
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }
}