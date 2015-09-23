<?php
class Ynfeedback_Form_Admin_Search extends Engine_Form
{
	public function init() 
	{
        $this->clearDecorators()
             ->addDecorator('FormElements')
             ->addDecorator('Form')
             ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
             ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));
    
        $this->setAttribs(array(
            'class' => 'global_form_box',
            'id' => 'filter_form',
            'method'=>'GET',
        ));
        
        $this->addElement('Text', 'title', array(
            'label' => 'Feedback Title',
        ));
		
		$this->addElement('Text', 'owner', array(
            'label' => 'Owner',
        ));
		
        $this->addElement('Text', 'from_date', array(
            'label' => 'Create From',
            'class' => 'date_picker input_small',
        ));
        
        $this->addElement('Text', 'to_date', array(
            'label' => 'To',
            'class' => 'date_picker input_small',
        ));
        
		// Category 
      	$categories = Engine_Api::_() -> getItemTable('ynfeedback_category') -> getCategoriesAssoc();
	    if(count($categories) >= 1 ) {
		      $this->addElement('Select', 'category_id', array(
		        'label' => 'Cateogry',
		        'multiOptions' => $categories,
		      ));
	    }
        
		// Status
      	$status = Engine_Api::_() -> getItemTable('ynfeedback_status') -> getStatusAssoc();
	    if(count($status) >= 1 ) {
		      $this->addElement('Select', 'status_id', array(
		        'label' => 'Status',
		        'multiOptions' => $status,
		      ));
	    }
	    
        // Element: order
		$this->addElement('Hidden', 'orderby', array(
            'order' => 998,
            'value' => 'idea_id'
        ));
    
        // Element: direction
        $this->addElement('Hidden', 'direction', array(
            'order' => 999,
            'value' => 'DESC',
        ));
		
        $this->addElement('Button', 'button_submit', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
        
        $this->button_submit->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));
    }
}