<?php

class Ynevent_Form_Agent_Create extends Engine_Form
{
	public function init()
	{
		$this->addPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator')->addPrefixPath('Ynevent_Form_Element', APPLICATION_PATH . '/application/modules/Ynevent/Form/Element', 'element')->addElementPrefixPath('Ynevent_Form_Decorator', APPLICATION_PATH . '/application/modules/Ynevent/Form/Decorator', 'decorator');

		$this->setTitle("Event Agent")->setAttrib('id', 'ynevent_agent_create');
		// Title
		$this->addElement('Text', 'title', array(
				'label' => 'Agent Name',
				'allowEmpty' => false,
				'required' => true,
				'validators' => array(
						array(
								'NotEmpty',
								true
						),
						array(
								'StringLength',
								false,
								array(
										1,
										64
								)
						),
				),
				'filters' => array(
						'StripTags',
						new Engine_Filter_Censor(),
				),
		));
		// Category
		$this->addElement('MultiLevel2', 'category_id', array(
				'label' => 'Event Category',
				'required' => false,
				'model' => 'Ynevent_Model_DbTable_Categories',
				'isSearch' => 1,
				'module' => 'ynevent',
		));

		$this->addElement('YnCalendarSimple', 'starttime', array(
				'label' => 'From Date:',
				'allowEmpty' => true,
				'required' => false,
		));

		$this->addElement('YnCalendarSimple', 'endtime', array(
				'label' => 'End Date:',
				'allowEmpty' => true,
				'required' => false,
		));

		$this->addElement('Text', 'keyword', array(
				'label' => 'Keyword:',
				'maxlength' => '60',
				'required' => false,
		));

		$this->addElement('Text', 'address', array(
				'label' => 'Address',
				'maxlength' => '60',
				'required' => false,
		));
		
		$this->addElement ( 'Select', 'country', array (
				'label' => 'Country',
				'multiOptions' => Ynevent_Model_DbTable_Countries::getMapMultiOptions(),
				'value' => '' 
		) );
		
		$this->addElement('Text', 'state', array(
				'label' => 'State',
				'placeholder' => 'state',
				'RegisterInArrayValidator' => false
		));

		$this->addElement('Text', 'city', array(
				'label' => 'City',
				'RegisterInArrayValidator' => false,
				'required' => false
		));

		$this->addElement('Text', 'mile_of', array(
				'label' => 'Within :',
				'maxlength' => '60',
				'required' => false,
				'description' => 'mile(s)'
		));

		$this->getElement('mile_of')->getDecorator("Description")->setOption("placement", "append");

		$this->addElement('Text', 'zipcode', array(
				'label' => 'Zip/Postal Code',
				'maxlength' => '60',
				'required' => false,
		));

		// Buttons
		$this->addElement('Button', 'submit', array(
				'label' => 'Save',
				'type' => 'submit',
				'ignore' => true,
				'decorators' => array('ViewHelper', ),
		));

		$this->addElement('Cancel', 'cancel', array(
				'label' => 'cancel',
				'link' => true,
				'prependText' => ' or ',
				'decorators' => array('ViewHelper', ),
		));

		$this->addDisplayGroup(array(
				'submit',
				'cancel'
		), 'buttons', array('decorators' => array(
					'FormElements',
					'DivDivDivWrapper',
			)));
	}

	function boundWord($word)
	{
		return '[[:<:]]' . trim($word) . '[[:>:]]';
	}

	public function getValues()
	{
		$values = parent::getValues();

		$address = trim($values['address']);
		$keyword = trim($values['keyword']);
		$zipcode = $values['zipcode'];

		$values['keyword'] = $keyword;
		$values['address'] = $address;
		$values['address_pattern'] = "%%";

		if ($keyword)
		{
			if ($values['match'])
			{
				$values['keyword_pattern'] = implode('|', array_map(array(
						$this,
						'boundWord'
				), preg_split('#\s+#', $keyword)));
			}
			else
			{
				$values['keyword_partern'] = $this->boundWord(implode('|', preg_split('#\s+#', $keyword)));
			}
		}
		else
		{
			$values['keyword_pattern'] = '';
		}

		try
		{
			$position = Engine_Api::_()->ynevent()->getPositionsAction($zipcode);
			$values['lat'] = $position[0];
			$values['lon'] = $position[1];
		}
		catch(Exception $e)
		{
			$values['lat'] = 0;
			$values['lon'] = 0;
			if (APPLICATION_ENV == 'development')
			{
				throw $e;
			}
		}

		// $values.
		return $values;
	}

}
?>