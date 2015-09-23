<?php
class Ynevent_Form_Video_Search extends Engine_Form
{
  protected $_manage;
	
	public function getManage()
	{
		return $this -> _manage;
	}
	
	public function setManage($manage)
	{
		$this -> _manage = $manage;
	} 
	
  public function init()
  {
   //Form Attribute and Method
    $this->setAttribs(array('id' => 'filter_form',
                            'class' => 'global_form_box',))
         ->setMethod('GET')
         ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('page' => null)));


    $this->addElement('Hidden','page');
    //Search Text
    $this->addElement('Text', 'title', array(
      'label' => 'Search Videos',
    ));
	
	if(!$this -> _manage)
	{
	    //Closed
	    $this->addElement('Text', 'owner', array(
	      'label' => 'Member',
	    ));
	}
    //Order
    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
        'most_liked' =>  'Most Liked',
        'most_commented' =>  'Most Commented',
        'rating' =>  'Highest Rated',
      ),
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}