<?php
class Ynfeedback_Form_Search extends Engine_Form
{
	  public function init()
	  {
		  	$translate = Zend_Registry::get("Zend_Translate");
		    $this -> setAttribs(array( 
		    	'id' => 'filter_form',
				'class' => 'global_form_box',
				'method' => 'GET'
			));
			
		    // Search Text Field.
		    $this->addElement('Text', 'keyword', array(
				'label' => 'Search Feedback',
		    ));
	    
	      	// Category 
	      	$categories = Engine_Api::_() -> getItemTable('ynfeedback_category') -> getCategoriesAssoc();
		    if(count($categories) >= 1 ) {
			      $this->addElement('Select', 'category_id', array(
			        'label' => 'Category',
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
	
		    //Browse By Filter Element
		    $this->addElement('Select', 'orderby', array(
		      'label' => 'Browse By',
		      'multiOptions' => array(
		        'creation_date' => $translate -> _("Most Recent"),
		        'view_count' => $translate -> _("Most Viewed"),
		    	'vote_count' => $translate -> _("Most Voted"),
		    	'like_count' => $translate -> _("Most Liked"),
		    	'comment_count' => $translate -> _("Most Discussed"),
		    	'follow_count' => $translate -> _("Most Followed"),
		      ),
		    ));
		    
			$this -> addElement('Button', 'Search', array(
				'label' => 'Search',
				'type' => 'submit',
			));
	  }
}