<?php
class SocialConnect_Form_Admin_CreatePage extends Engine_Form {

    public function init() {
        $this
          ->setTitle('Create Page');
		  
		   // prepare categories
	    $categories = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> getAllCategories();
	    if (count($categories)!=0){
	      $categories_prepared[0]= "";
	      foreach ($categories as $category){
	        $categories_prepared[$category->category_id]= $category->category_name;
	      }
	
	      // category field
	      $this->addElement('Select', 'category_id', array(
	            'label' => 'Category',
	            'multiOptions' => $categories_prepared
	          ));
	    }
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'filters' => array(
                'StripTags'
            )
        ));
        
        $this->addElement('TinyMce', 'content', array(
            'label' => 'Content',
            'editorOptions' => array(
           	'browser_spellcheck' => true,
		  	'contextmenu' => false),
        ));
		
        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'prependText' => ' or ',
            'href' => '',
      		'onClick'=> 'javascript:parent.Smoothbox.close();',
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