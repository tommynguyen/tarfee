<?php

class Ynevent_Form_Filter_Browse extends Engine_Form
{

	public function init()
	{
		$this -> addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator') -> addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element') -> addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');

		$this -> setAttrib('id', 'filter_form');

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
			'label' => 'Keyword',
			'maxlength' => '60',
			'placeholder' => $translate->translate('Search events..'),
			'required' => false,
		));
		
		$this -> addElement('Select', 'order', array(
			'label' => 'Sort by',
			'multiOptions' => array(
				'starttime ASC' => 'Start Time',
				'creation_date DESC' => 'Recently Created',
				'member_count DESC' => 'Most Popular',
			),
			'value' => 'creation_date DESC',
		));
		
		// Category
		$this -> addElement('MultiLevel2', 'category_id', array(
			'label' => 'Event Category',
			'required' => false,
			'model' => 'Ynevent_Model_DbTable_Categories',
			'module' => 'ynevent',
		));
		
		$this -> addElement('Dummy', 'location', array(
			'label' => 'Location',
			'decorators' => array( array(
				'ViewScript',
				array(
					'viewScript' => '_location_search.tpl',
					'class' => 'form element',
				)
			)), 
		));
		
		$this -> addElement('Text', 'within', array(
			'label' => 'Radius (mile)',
			'placeholder' => $translate->translate('Radius (mile)..'),
			'maxlength' => '60',
			'required' => false,
			'style' => "display: block",
			'validators' => array(
				array(
					'Int',
					true
				),
				new Engine_Validate_AtLeast(0),
			),
		));

		// Start time
		$start = new Ynevent_Form_Element_YnCalendarSimple('start_date');
		$start -> setLabel("Start Time");
		$start -> setAllowEmpty(true);
		$this -> addElement($start);

		// End time
		$end = new Ynevent_Form_Element_YnCalendarSimple('end_date');
		$end -> setLabel("End Time");
		$end -> setAllowEmpty(true);
		$this -> addElement($end);

		$this -> addElement('hidden', 'is_search', array(
			'value' => '1',
			'order' => '0'
		));
		
		$this -> addElement('hidden', 'lat', array(
			'value' => '0',
			'order' => '98'
		));
		
		$this -> addElement('hidden', 'long', array(
			'value' => '0',
			'order' => '99'
		));
		
		$this -> addElement('hidden', 'filter', array(
			'value' => 'future',
			'order' => '100'
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
