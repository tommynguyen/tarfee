<?php

class Ynresponsiveevent_Form_Event_Browse extends Engine_Form
{
	public function init()
	{
		$this -> setAttrib('id', 'ynresponsive_event_filter_form');
		$translate = Zend_Registry::get("Zend_Translate");
		$this -> clearDecorators() -> addDecorators(array(
			'FormElements',
			array(
				'HtmlTag',
				array('tag' => 'dl')
			),
			'Form',
		)) -> setMethod('get') -> setAttrib('class', 'global_form_box');

		$this -> addElement('Text', 'keyword', array(
			'maxlength' => '60',
			'placeholder' => $translate->translate('Search events...'),
			'required' => false,
		));
		
		$this -> addElement('Select', 'order', array(
			'multiOptions' => array(
				'' => 'Sort by',
				'starttime ASC' => 'Start Time',
				'creation_date DESC' => 'Recently Created',
				'member_count DESC' => 'Most Popular',
			),
			'value' => '',
		));
		
		// Category
		$event_active = 'event';
		if (Engine_Api::_()->hasModuleBootstrap('ynevent'))
		{
			$event_active = 'ynevent';
		}
		$options = array();
		$categories = Engine_Api::_() -> ynresponsiveevent() -> getCategories($event_active);
		$options[0] = $translate -> translate("Categories");
		foreach ($categories as $category) {
			$options[$category -> getIdentity()] = $translate -> translate($category -> title);
		}
		$this -> addElement('Select', 'category_id', array(
			'required' => false,
			'multiOptions' => $options
		));
		if($event_active == 'ynevent')
		{
			$this -> addElement('Dummy', 'location', array(
				'decorators' => array( array(
					'ViewScript',
					array(
						'viewScript' => '_location_search.tpl',
						'class' => 'form element',
						'viewModule' => 'ynresponsive1'
					),
				)), 
			));
			
			$this -> addElement('Select', 'within', array(
				'multiOptions' => array(
					5 => 'Within 5 miles',
					10 => 'Within 10 miles',
					20 => 'Within 20 miles',
					50 => 'Within 50 miles',
					100 => 'Within 100 miles',
				),
				'value' => 5,
			));
		}
		// Start time
		$start = new Ynresponsiveevent_Form_Event_YnCalendarSimple('start_date');
		$start -> setAllowEmpty(true);
		$this -> addElement($start);

		// End time
		$end = new Ynresponsiveevent_Form_Event_YnCalendarSimple('end_date');
		$end -> setAllowEmpty(true);
		$this -> addElement($end);
		
		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
		
		// Buttons
		$this -> addElement('Button', 'Search', array(
			'label' => 'Search',
			'type' => 'submit',
		));

	}

	public function isValid($data)
	{
		$isValid = parent::isValid($data);
		if ($isValid)
		{
			if (array_key_exists('start_date', $data))
			{
				$startDate = $data['start_date'];
			}
			if (array_key_exists('end_date', $data))
			{
				$endDate = $data['end_date'];
			}
			if (!empty($startDate) && !empty($endDate))
			{
				$startDate = strtotime($startDate);
				$endDate = strtotime($endDate);
				if ($startDate > $endDate)
				{
					$isValid = false;
				}
			}
		}
		return $isValid;
	}

}
