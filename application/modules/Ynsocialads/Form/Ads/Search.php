<?php
class Ynsocialads_Form_Ads_Search extends Engine_Form
{
	public function init()
	{
		$this -> clearDecorators() -> addDecorator('FormElements') -> addDecorator('Form') -> addDecorator('HtmlTag', array(
			'tag' => 'div',
			'class' => 'search'
		)) -> addDecorator('HtmlTag2', array(
			'tag' => 'div',
			'class' => 'clear'
		));

		$this -> setAttribs(array(
			'id' => 'filter_form',
			'class' => 'global_form_box',
			'method' => 'GET',
		));

		//Search Title
		$this -> addElement('Text', 'name', array(
			'label' => 'Name',
			'class' => 'filter_elem',
			'filters' => array(
                'StripTags'
            )
		));

		$status = array(
			"all" => 'All',
			"draft" => 'Draft',
			"unpaid" => 'Unpaid',
			"pending" => 'Pending',
			"denied" => 'Denied',
			"running" => 'Running',
			"paused" => 'Paused',
			"completed" => 'Completed',
			"approved" => 'Approved',
			"deleted" => 'Deleted',
		);
		$this -> addElement('Select', 'status', array(
			'label' => 'Status',
			'multiOptions' => $status,
			'class' => 'filter_elem',
		));

		$type = array(
			"all" => 'All',
			"text" => 'Text',
			"banner" => 'Banner',
			"feed" => 'Feed',
		);
		$this -> addElement('Select', 'type', array(
			'label' => 'Type',
			'multiOptions' => $type,
			'class' => 'filter_elem',
		));

		// Element: order
		$this -> addElement('Hidden', 'order', array(
			'order' => 101,
			'value' => 'ad_id'
		));

		// Element: direction
		$this -> addElement('Hidden', 'direction', array(
			'order' => 102,
			'value' => 'DESC',
		));

		// Element: direction
		$this -> addElement('Hidden', 'page', array('order' => 103, ));

		// Buttons
		$this -> addElement('Button', 'button', array(
			'label' => 'Search',
			'type' => 'submit',
			'class' => 'filter_elem',
		));

		$this -> button -> clearDecorators() -> addDecorator('ViewHelper') -> addDecorator('HtmlTag', array(
			'tag' => 'div',
			'class' => 'buttons'
		)) -> addDecorator('HtmlTag2', array('tag' => 'div'));
	}

}
