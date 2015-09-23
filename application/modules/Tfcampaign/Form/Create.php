<?php
class Tfcampaign_Form_Create extends Engine_Form
{
  public function init()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
	$view = Zend_Registry::get("Zend_View");
	
  	$user = Engine_Api::_()->user()->getViewer();
    $this
      ->setTitle('Add New Campaign')
	  -> setDescription("NEW_CAMPAIGN_DESCRIPTION");
	
	$maxCharTitle = $settings->getSetting('tfcampaign_max_title', "300");
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'description' => $view -> translate("Maximum %s characters.", $maxCharTitle),
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, $maxCharTitle)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> title -> setAttrib('required', true);
	
	$maxCharDesc = $settings->getSetting('tfcampaign_max_description', "300");
	$this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'description' => $view -> translate("Maximum %s characters.", $maxCharDesc),
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, $maxCharDesc)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> description -> setAttrib('required', true);
	
	// Start time
    $start = new Engine_Form_Element_CalendarDateTime('start_date');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $this->addElement($start);
	
    // End time
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $this->addElement($end);
	
	$sportCattable = Engine_Api::_() -> getDbtable('sportcategories', 'user');
	$node = $sportCattable -> getNode(0);
	$categories = $node -> getChilren();
	foreach($categories as $category)
	{
		$sport_categories[$category->getIdentity()] = $category -> getTitle();
	}
    $this->addElement('Select', 'category_id', array(
      'label' => 'Sport Category',
      'multiOptions' => $sport_categories,
      'onchange' => 'subCategories()',
    ));
	
	$positions = $sportCattable -> getMultiOptions('--', '', FALSE);
	array_shift($positions);
	$this -> addElement('Select', 'position_id', array(
		'label' => 'Position',
		'multiOptions' => $positions,
	));
	
	$this -> addElement('Select', 'referred_foot', array(
		'label' => 'Preferred Foot',
		'multiOptions' => array('1' => 'Left', '2' => 'Right', '0' => 'Both'),
	));
	
	/*
	$this->addElement('File', 'photo', array(
      'label' => 'Campaign Photo'
    ));
	$this -> photo -> setAllowEmpty(true);
    $this -> photo -> addValidator('Extension', false, 'jpg,png,gif,jpeg');
	$this -> photo -> setAttrib('accept', 'image/*');
	*/
	
	$arrAge = array();
	$arrAge[] = "Any";
    for ($i = 1; $i <= 100; $i++) 
    {
    	$arrAge[] = $i;
    }
	
	$this -> addElement('Select', 'from_age', array(
		'label' => 'From Age',
		'multiOptions' => $arrAge,
	));
	
	$this -> addElement('Select', 'to_age', array(
		'label' => 'To Age',
		'multiOptions' => $arrAge,
	));
	
	
	$gender = new Engine_Form_Element_Select('gender');
    $gender->setLabel("Gender");
    $gender->setAllowEmpty(false);
	$gender->setMultiOptions(array('0' => 'Any', '1' => 'Male', '2' => 'Female'));
	$gender -> setRequired(true);
    $this->addElement($gender);
	
	
	$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
	$countriesAssoc = array('0'=>'Any') + $countriesAssoc;

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
	
   $languages = Engine_Api::_()->getDbTable('languages', 'user')->getLanguagesArray();
	$this->addElement('MultiCheckbox', 'languages', array(
      'label' => 'Languages',
      'required' => false,
      'allowEmpty' => true,
      'multiOptions' => $languages,
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this->languages->getDecorator("Description")->setOption("placement", "append");
	
	$this->addElement('Select', 'percentage', array(
        'label' => 'Matching Percentage',
        'description' => 'The minimum percentage of matching.',
        'multiOptions' => array(
			'25' => '25%',
			'50' => '50%',
			'75' => '75%',
			'100' => '100%',
		),
        'value' => 25,
    ));
	
	// View for specific users
    $this -> addElement('Text', 'user', array(
        'label' => 'Allow view for',
        'autocomplete' => 'off',
        'order' => '16'
    ));
    
    $this -> addElement('Hidden', 'user_ids', array(
        'filters' => array('HtmlEntities'),
        'order' => '18'
    ));
    Engine_Form::addDefaultDecorators($this -> user_ids);
	
	
	$allowPrivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('tfcampaign_private_allow', 1);
	
	if($allowPrivate) {
		// View
	    $availableLabels = array(
	      'everyone'            => 'Everyone',
	      'registered'          => 'All Registered Members',
	      'owner_network'       => 'Followers and Networks',
	      'owner_member_member' => 'Followers of Followers',
	      'owner_member'        => 'My Followers',
	      'owner'               => 'Only Me'
	    );
	
	    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('tfcampaign_campaign', $user, 'auth_view');
	    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
	    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
	      // Make a hidden field
	      if(count($viewOptions) == 1) {
	        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
	      // Make select box
	      } else {
	        $this->addElement('Select', 'auth_view', array(
	            'label' => 'Who may see this campaign',
	            'multiOptions' => $viewOptions,
	            'value' => key($viewOptions),
	            'onchange' => 'privacyChange()'
	        ));
	        $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
	      }
	    }
    }
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Campaign ',
      'type' => 'submit',
      'ignore' => true,
      'onClick' => 'return checkValid();',
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}