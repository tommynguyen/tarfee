<?php
class User_Form_Playercard_Create extends Engine_Form
{
  public function init()
  {
  	$user = Engine_Api::_()->user()->getViewer();
    $this
      ->setTitle('Add New Player Card');
	  
	$this->addElement('File', 'photo', array(
      'label' => 'Card Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
    $this->addElement('Text', 'first_name', array(
      'label' => 'First Name',
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
	$this -> first_name -> setAttrib('required', true);
	
	$this->addElement('Text', 'last_name', array(
      'label' => 'Last Name',
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
	$this -> last_name -> setAttrib('required', true);
	
	$relations = Engine_Api::_() -> getDbTable('relations','user') -> getRelationArray();
	$relations['0'] = 'Other';
	$this->addElement('Select', 'relation_id', array(
      'label' => 'Relation',
      'multiOptions' => $relations,
      'onchange' => 'showOther()',
      'required' => (bool)Engine_Api::_()->getApi('settings', 'core')->getSetting('user.relation_require', 1)
    ));
	$this->addElement('Textarea', 'relation_other', array(
      'label' => 'Relation Other',
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 100)),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 10000)),
      ),
    ));
   
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
      'onchange' => 'subCategories()',
    ));
	
	$gender = new Engine_Form_Element_Select('gender');
    $gender->setLabel("Gender");
    $gender->setAllowEmpty(false);
	$gender->setMultiOptions(array('1' => 'Male', '2' => 'Female'));
	$gender -> setRequired(true);
    $this->addElement($gender);
    
    $birthday = new Fields_Form_Element_Birthdate('birth_date');
    $birthday->setLabel("Date of Birth");
    $birthday->setAllowEmpty(false);
	$birthday -> setRequired(true);
	$birthday -> setYearMax(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.max_year', 2003));
	$birthday -> setYearMin(Engine_Api::_()->getApi('settings', 'core')->getSetting('user.min_year', 1985));
    $this->addElement($birthday);
	$birthday -> setAttrib('required', true);
	$this -> birth_date -> setAttrib('required', true);
	
	$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
	$countriesAssoc = array(''=>'') + $countriesAssoc;

	$this->addElement('Select', 'country_id', array(
		'label' => 'Country',
		'multiOptions' => $countriesAssoc,
		'required' => true,
	));
	$this -> country_id -> setAttrib('required', true);

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
    ));
	$this->languages->getDecorator("Description")->setOption("placement", "append");
	
	$this -> addElement('Select', 'referred_foot', array(
		'label' => 'Preferred Foot',
		'multiOptions' => array('1' => 'Left', '2' => 'Right', '0' => 'Both'),
	));
	
	$positions = $sportCattable -> getMultiOptions('--', '', FALSE);
	$this -> addElement('Select', 'position_id', array(
		'label' => 'Position',
		'multiOptions' => $positions,
	));
	
	// View for specific users
    $this -> addElement('Text', 'user', array(
        'label' => 'Allow view for',
        'autocomplete' => 'off',
        'order' => '16'
    ));
    
    $this -> addElement('Hidden', 'user_ids', array(
        'filters' => array('HtmlEntities'),
        'order' => '17'
    ));
    Engine_Form::addDefaultDecorators($this -> user_ids);
	
	// View
    $availableLabels = array(
      'everyone'            => 'Everyone',
      'owner_network'       => 'Followers and Networks',
      'owner_member'        => 'My Followers',
      'owner'               => 'Only Me'
    );

    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user_playercard', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
      // Make a hidden field
      if(count($viewOptions) == 1) {
        $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
      // Make select box
      } else {
        $this->addElement('Select', 'auth_view', array(
            'label' => 'Who can view this player',
            'multiOptions' => $viewOptions,
            'value' => key($viewOptions),
            'onchange' => 'privacyChange()'
        ));
      }
    }

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Player Card',
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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}