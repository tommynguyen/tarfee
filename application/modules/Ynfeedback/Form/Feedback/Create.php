<?php
class Ynfeedback_Form_Feedback_Create extends Engine_Form
{
	protected $_formArgs;
	
	public function getFormArgs()
	{
		return $this -> _formArgs;
	}
	
	public function setFormArgs($formArgs)
	{
		$this -> _formArgs = $formArgs;
	} 
	
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
    $settings = Engine_Api::_()->getApi('settings', 'core');
	$viewer = Engine_Api::_()->user()->getViewer();
	
    $this->setTitle('Add New Feedback');
	
	//Category
	 $this->addElement('Select', 'category_id', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => '*Category',
    ));
	
	//Custom field
    if( !$this->_item ) {
      $customFields = new Ynfeedback_Form_Feedback_Fields($this -> _formArgs);
	}
    if( get_class($this) == 'Ynfeedback_Form_Feedback_Create' ) {
      $customFields->setIsCreation(true);
    }
	
    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
	//title
    $this->addElement('Text', 'title', array(
      'label' => '*Feedback',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 100)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
    if (isset($_GET['title']) && !empty($_GET['title']))
    {
    	$this -> getElement('title') -> setValue($_GET['title']);
    }
    
	//description
	$this->addElement('Textarea', 'description', array(
        'label' => '*Description',
        'allowEmpty' => false,
      	'required' => true,
        'filters' => array(
        	new Engine_Filter_HtmlSpecialChars(),
        	new Engine_Filter_Censor(),
        	new Engine_Filter_EnableLinks(),
        ),
        'validators' => array(
	        array('NotEmpty', true),
		),
    ));
	
	//Co-authors 
	$this -> addElement('Text', 'to', array('label' => 'Co-authors', 'autocomplete' => 'off'));
	Engine_Form::addDefaultDecorators($this -> to);
	
	// Init to Values
	$this -> addElement('Hidden', 'toValues', array(
		'style' => 'margin-top:-5px',
		'order' => 5,
		'filters' => array('HtmlEntities'),
	));
	
	Engine_Form::addDefaultDecorators($this -> toValues);
	
	//Severity
	 $this->addElement('Select', 'severity', array(
	  'required'  => true,
      'allowEmpty'=> false,
      'label' => 'Severity',
    ));
	
	$tableSeverity = Engine_Api::_() -> getDbTable('severities', 'ynfeedback');
	$this -> severity -> addMultiOptions($tableSeverity -> getSeverityArray());
		
	
	if($viewer -> getIdentity())
	{
		// Privacy
	    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynfeedback_idea', $viewer, 'auth_view');
	    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynfeedback_idea', $viewer, 'auth_comment');
		
		$availableLabels = array(
	        'everyone' => $view->translate('Everyone'),
	        'owner_network' => $view->translate('Friends and Networks'),
	        'owner_member_member' => $view->translate('Friends of Friends'),
	        'owner_member' => $view->translate('Friends Only'),
	        'owner' => $view->translate('Just Me')
	      );
	    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
	    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
		
		
		// View
	    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($viewOptions) == 1) {
	        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_view', array(
	            'label' => 'Privacy',
	            'multiOptions' => $viewOptions,
	            'value' => key($viewOptions),
	        ));
	      }
	    }
	
	    // Comment
	    if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($commentOptions) == 1) {
	        $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_comment', array(
	            'label' => 'Comment Privacy',
	            'multiOptions' => $commentOptions,
	            'value' => key($commentOptions),
	        ));
	      }
	    }
	}
	else
	{
		//name
	    $this->addElement('Text', 'guest_name', array(
	      'label' => '*Your Name',
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
		
		//email
	    $this->addElement('Text', 'guest_email', array(
	        'label' => '*Your Email',
	        'allowEmpty' => false,
	     	'required' => true,
	     	'description' => $view -> translate('We will not show your email but your name for this feedback.'),
	     	'validators' => array(
		        array('NotEmpty', true),
		        array('StringLength', false, array(1, 64)),
			),
		    'filters' => array(
		        'StripTags',
		        new Engine_Filter_Censor(),
		    ),
	    ));
		$this->guest_email->getDecorator("Description")->setOption("placement", "append");
	}
	
	
    $this->addElement('Button', 'submit_button', array(
      'value' => 'submit_button',
      'label' => 'Create',
      'onclick' => 'removeSubmit()',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'Cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
	 $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
