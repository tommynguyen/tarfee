<?php
class Advalbum_Form_Search extends Engine_Form
{
  public function init()
  {
  	$translate = Zend_Registry::get("Zend_Translate");
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Albums:',
      'placeholder' => $translate->translate('Search albums'),
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'Browse By:', 
      'multiOptions' => array(
        'recent' => 'Most Recent',
        'popular' => 'Most Popular',
		'most_commented' => 'Most Commented',
		'top' => 'Most Liked',
      ),
    ));
    
    $this->addElement('Hidden', 'color', array());
    
    // prepare categories
    $categories = Engine_Api::_()->advalbum()->getCategories();
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

  }
}