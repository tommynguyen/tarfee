<?php
class Ynmember_Form_Search extends Fields_Form_Search
{
	public function init()
	{
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$allow_search_location = $settings->getSetting('ynmember_allow_search_location', 1);
		// Add custom elements
		$this->getMemberTypeElement();
		$this->getDisplayNameElement();
		if($allow_search_location)
		{
			$this->getLocationElement();
		}
		$this->getOrderElement();
		$this->getAdditionalOptionsElement();
		parent::init();

		$this->loadDefaultDecorators();
		$this->setAttribs(array('class' => 'global_form_box search_form', 'id' => 'filter_form'))
                ->setMethod('GET');
		$this->getDecorator('HtmlTag')->setOption('class', 'browsemembers_criteria');
	}

	public function getLocationElement()
	{
		$view = Zend_Registry::get('Zend_View');
		$location = Zend_Controller_Front::getInstance()->getRequest()->getParam('location', '');
		$translate = Zend_Registry::get("Zend_Translate");
		$this -> addElement('Text', 'location', array(
			'label' => 'Location',
			'order' => -(1000000-2),
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'class' => 'form element',
					'location' => $location,
				)
			)), 
		));
		
		$this -> addElement('Text', 'within', array(
			'label' => 'Radius (mile)',
			'placeholder' => $view->translate('Radius (mile)..'),
			'maxlength' => '60',
			'value' => 50,
		));
		
		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
	}
	
	public function getOrderElement()
	{
		$translate = Zend_Registry::get("Zend_Translate");
		$this->addElement('Select', 'order', array(
		      'label' => 'Browse By',
			  'order' => 998,
		      'multiOptions' => array(
					'az' => $translate -> translate("A - Z"),
					'za' => $translate -> translate("Z - A"),
					'recent' => $translate -> translate("Recent Members"),
					'most_view' => $translate -> translate("Most Viewed"),
					'most_like' => $translate -> translate("Most Liked"),
					'most_rating' => $translate -> translate("Most Rated"),
			  ),
			
		));
		
		if (Engine_Api::_()->user()->getViewer()->getIdentity())
		{
			$this->addElement('Select', 'show', array(
			      'label' => 'Show',
				  'order' => 999,
			      'multiOptions' => array(
						'' => $translate -> translate("All Members"),
						'friend' => $translate -> translate("Only My Friends"),
						'network' => $translate -> translate("Only My Networks"),
						'featured' => $translate -> translate("Only Featured"),
						'like' => $translate -> translate("Members I Liked"),
				  ),
				
			));
		}
		
	}
	
	public function getMemberTypeElement()
	{
		$multiOptions = array('' => ' ');
		$profileTypeFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($this->_fieldType, 'profile_type');
		if( count($profileTypeFields) !== 1 || !isset($profileTypeFields['profile_type']) ) return;
		$profileTypeField = $profileTypeFields['profile_type'];

		$options = $profileTypeField->getOptions();

		if( count($options) <= 1 ) {
			if( count($options) == 1 ) {
				$this->_topLevelId = $profileTypeField->field_id;
				$this->_topLevelValue = $options[0]->option_id;
			}
			return;
		}

		foreach( $options as $option ) {
			$multiOptions[$option->option_id] = $option->label;
		}

		$this->addElement('Select', 'profile_type', array(
		      'label' => 'Member Type',
		      'order' => -1000001,
		      'class' =>
			        'field_toggle' . ' ' .
			        'parent_' . 0 . ' ' .
			        'option_' . 0 . ' ' .
			        'field_'  . $profileTypeField->field_id  . ' ',
		      'onchange' => 'changeFields($(this));',
		      'decorators' => array(
			        'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li', 'class' => 'ynmember_search_profile_type_field'))
			   ),
		      'multiOptions' => $multiOptions,
		));
		return $this->profile_type;
	}

	public function getDisplayNameElement()
	{
		$translate = Zend_Registry::get("Zend_Translate");
		$this->addElement('Text', 'displayname', array(
		      'label' => 'Name',
		      'order' => -1000000,
		      'placeholder' => $translate->translate('Search members..'),
		      'decorators' => array(
			        'ViewHelper',
					array('Label', array('tag' => 'span')),
					array('HtmlTag', array('tag' => 'li', 'class' => 'ynmember_search_displayname_field'))
					),
		));
		return $this->displayname;
	}

	public function getAdditionalOptionsElement()
	{
		$subform = new Zend_Form_SubForm(array(
		      'name' => 'extra',
		      'order' => 1000000,
		      'decorators' => array(
		        	'FormElements',
			  )
		));
		Engine_Form::enableForm($subform);

		$subform->addElement('Checkbox', 'has_photo', array(
		      'label' => 'Only Members With Photos',
		      'decorators' => array(
			        'ViewHelper',
					array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
					array('HtmlTag', array('tag' => 'li'))
					),
		));

		$subform->addElement('Checkbox', 'is_online', array(
		      'label' => 'Only Online Members',
		      'decorators' => array(
		        'ViewHelper',
				array('Label', array('placement' => 'APPEND', 'tag' => 'label')),
				array('HtmlTag', array('tag' => 'li'))
		      ),
    	));

	    $subform->addElement('Button', 'done', array(
			'label' => 'Search',
	    	'type' => 'submit',
	    ));

    	$this->addSubForm($subform, $subform->getName());

    	return $this;
	}
}