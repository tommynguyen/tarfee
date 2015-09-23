<?php

class Ynevent_Form_Create extends Engine_Form
{
  protected $_parent_type;

  protected $_parent_id;  
  
  protected $_item;
  
  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }
  
  public function setParent_type($value)
  { 
    $this->_parent_type = $value;
  }
  
  public function setParent_id($value)
  {
    $this->_parent_id = $value;
  }
  public function init()
  {
	 $this
      ->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')
      ->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')
      ->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');
	$view = Zend_Registry::get('Zend_View');  	
  	
    $user = Engine_Api::_()->user()->getViewer();

    $this->setTitle('Create New Tryout/Event')
      ->setAttrib('id', 'ynevent_create_form')
      ->setMethod("POST")
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
      
    // Title
    $this->addElement('Text', 'title', array(
      'label' => 'Title(*)',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this -> title -> setAttrib('required', true);
    $title = $this->getElement('title');
	
	$this -> addElement('Select', 'type_id', array(
		'label' => 'Type',
		'multiOptions' => array(
		0 => "Event",
		1 => 'Tryout'
		)
	));

    // Brief Description
    $this->addElement('Textarea', 'brief_description', array(
      'label' => 'Brief Description',
      'description' => 'Max 250 characters',
      'maxlength' => '250',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 250)),
      ),
    ));
    $this->brief_description->getDecorator('Description')->setOption('placement', 'append');
	
	
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'maxlength' => '10000',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));
	
	$this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'description' => 'Separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
	
	// Description
    $this->addElement('Textarea', 'metadata', array(
      'label' => 'Metadata Keywords',
      'description' => 'Put in keywords for so that Search Engine can search your event more easily. Separate keywords with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
	$this->metadata->getDecorator("Description")->setOption("placement", "append");
    
	$this -> addElement('Radio', 'repeat_type', array(
            'label' => 'Please Select',
            'multiOptions' => array(
                '0' => 'One Time',
                '1' => 'Repeating'
            ),
            'value' => 0,     
            'onclick'=>'isrepeat(this)',      
        ));
	
	 // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time(*)");
    $start->setAllowEmpty(false);
    $this->addElement($start);
	
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time(*)");
    $end->setAllowEmpty(false);
    $this->addElement($end);
	
	$this -> addElement('Select', 'repeat_frequency', array(
            'label' => 'Repeat',
            'multiOptions' => array(
                '1' => 'Daily',
                '7' => 'Weekly',
                '30' => 'Monthly',	
                '99' => 'Specify'			
            ),
            'order' => 6,
            'onchange' => 'en4.ynevent.specify(this)',
        ));
	// Start repeat time
	$start_repeat_time = new Engine_Form_Element_CalendarDateTime('repeatstarttime');
    $start_repeat_time->setLabel("Start Time(*)");
    $start_repeat_time->setAllowEmpty(false);
    $this->addElement($start_repeat_time);
	
	// End repeat time
	$end_repeat_time = new Engine_Form_Element_CalendarDateTime('repeatendtime');
    $end_repeat_time->setLabel("End Time(*)");
    $end_repeat_time->setAllowEmpty(false);
    $this->addElement($end_repeat_time);	
	
	// Start repeat date
	$start_repeat = new Engine_Form_Element_CalendarDateTime('repeatstartdate');
    $start_repeat->setLabel("Start Date(*)");
    $start_repeat->setAllowEmpty(false);
    $this->addElement($start_repeat);
	
	// End repeat date
	$end_repeat = new Engine_Form_Element_CalendarDateTime('repeatenddate');
    $end_repeat->setLabel("End Date(*)");
    $end_repeat->setAllowEmpty(false);
    $this->addElement($end_repeat);
	
	// Start time
	$spec_start_date = new Engine_Form_Element_CalendarDateTime('spec_start_date');
	$spec_start_date -> setLabel("Add Start Date(*)");
	$spec_start_date -> setAllowEmpty(false);
	$this -> addElement($spec_start_date);

	// End time
	$spec_end_date = new Engine_Form_Element_CalendarDateTime('spec_end_date');
	$spec_end_date -> setLabel("Add End Date(*)");
	$spec_end_date -> setAllowEmpty(false);
	$this -> addElement($spec_end_date);
	
	$this -> addElement('Dummy', 'specify_repeat', array(
		'decorators' => array( array(
			'ViewScript',
			array(
				'viewScript' => '_specify_repeat.tpl',
				'class' => 'form element',
			)
		)), ));
	
    // Capacity
    $this->addElement('Text', 'capacity', array(
      'label' => 'Capacity',
      'description' => 'Set 0 for unlimited participants',
      'allowEmpty' => false,
      'required' => true,
      'value' => 0,
      'validators'  => array(
			array('Int', true),
			new Engine_Validate_AtLeast(0),
	  ),
    ));
    $this->capacity->getDecorator('Description')->setOption('placement', 'append');
	
    // Capacity
    $this->addElement('Text', 'price', array(
      'label' => 'Price',
      'description' => 'Set 0 for free',
      'allowEmpty' => false,
      'required' => true,
      'value' => 0,
      'validators'  => array(
			array('Float', true),
			new Engine_Validate_AtLeast(0),
	  ),
    ));
    $this->price->getDecorator('Description')->setOption('placement', 'append');
	
	$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
	$countriesAssoc = array('0'=>'') + $countriesAssoc;
	$this->addElement('Select', 'country_id', array(
		'label' => 'Country',
		'multiOptions' => $countriesAssoc,
	));

	$this->addElement('Select', 'province_id', array(
		'label' => 'Province/State',
	));

	$this->addElement('Select', 'city_id', array(
		'label' => 'City',
	));
    
    // Address
	$this -> addElement('Text', 'full_address', array(
		'label' => 'Full Address',
		'required' => false,
		'style' => 'width: 400px;',
		'description' => $view -> htmlLink(array(
			'action' => 'add-location',
			'route' => 'event_general',
			'reset' => true,
		), $view -> translate('View map'), array('class' => 'smoothbox')),
		'filters' => array(new Engine_Filter_Censor())
	));
	$this -> full_address -> getDecorator("Description") -> setOption("placement", "append") -> setEscape(FALSE);
        
    if ($this->_parent_type == 'user') 
    {
      $host = "";
	  if($this->_item)      
	  	$host = $this->_item->host;
	  $this->addElement('Dummy', 'host', array(
  		  'label' => 'Host',  	
  		  'value' => $host,
	      'decorators' => array(		          
		          array('ViewScript',array(
		                'viewScript' => '_host.tpl',
		                'class'      => 'form element'
		          ))
		      ),
		));
	  
    }

	// Cover Photo
    $this->addElement('File', 'cover_thumb', array(
      'label' => 'Cover Photo'
    ));
    $this->cover_thumb->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
    // Photo
    $this->addElement('File', 'photo', array(
      'label' => 'Event/Tryout Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

	$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
	$node = $sportCattable -> getNode(0);
	$categories = $node -> getChilren();
	$sport_categories[0] = '';
	foreach($categories as $category)
	{
		$sport_categories[$category->getIdentity()] = $category -> getTitle();
	}
    $this->addElement('Select', 'category_id', array(
      'label' => 'Sport Category',
      'multiOptions' => $sport_categories,
    ));

    // Email
    $this->addElement('Text', 'email', array(
      'label' => 'Email',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      )
    ));
    
    // Url
    $this->addElement('Text', 'url', array(
      'label' => 'Url',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Phone
    $this->addElement('Text', 'phone', array(
      'label' => 'Phone',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Contact Information
    $this->addElement('Text', 'contact_info', array(
      'label' => 'Contact Information',
      'allowEmpty' => true,
      'required' => false,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    // Search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'People can search for this event',
      'value' => True
    ));

    // Approval
    $this->addElement('Checkbox', 'approval', array(
      'label' => 'People must be invited to RSVP for this event',
    ));

    // Invite
    $this->addElement('Checkbox', 'auth_invite', array(
      'label' => 'Invited guests can invite other people as well',
      'value' => True
    ));
    
	// Add subforms	
	
    if( !$this->_item ) {
      $customFields = new Ynevent_Form_Custom_Fields();
    } else {
      $customFields = new Ynevent_Form_Custom_Fields(array(
        'item' => $this->getItem()
      ));
    }
    if( get_class($this) == 'Ynevent_Form_Create' ) {
      $customFields->setIsCreation(true);
    }
    $this->addSubForms(array(
      'fields' => $customFields
    ));
	
    // Privacy
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_view');
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('event', $user, 'auth_comment');
    
  	$availableLabels = array(
        'everyone'            => 'Everyone',
        'registered'          => 'All Registered Members',
        'owner_network'       => 'Followers and Networks',
        'owner_member_member' => 'Followers of Followers',
        'owner_member'        => 'Followers Only',
        'member'              => 'Event Guests Only',
        'leader'			  => 'Owner and Leader',	
        'owner'               => 'Just Me'
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
            'description' => 'Who may see this event?',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
        ));
        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
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
            'description' => 'Who may post comments on this event?',
            'multiOptions' => $commentOptions,
            'value' => key($commentOptions),
        ));
        $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
      }
    }

	$this -> addElement('Hidden', 'address', array('order' => '21'));
	$this -> addElement('Hidden', 'city', array('order' => '22'));
	$this -> addElement('Hidden', 'country', array('order' => '23'));
	$this -> addElement('Hidden', 'zip_code', array('order' => '24'));
	$this -> addElement('Hidden', 'latitude', array('order' => '25'));
	$this -> addElement('Hidden', 'longitude', array('order' => '26'));
	$this -> addElement('Hidden', 'apply_for_action', array('order' => '27', 'value' => 0));
	$this -> addElement('Hidden', 'f_repeat_type', array('order' => '28'));
	$this -> addElement('Hidden', 'g_repeat_type', array('order' => '29'));
	
    // Buttons
    $this->addElement('Button', 'save_change', array(
      'label' => 'Post Event/Tryous',
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
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('save_change', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
