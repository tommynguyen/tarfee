<?php
class Ynfeedback_Form_Feedback_Fields extends Fields_Form_Standard
{
  protected $_fieldType = 'ynfeedback_idea';

  public $_error = array();
  
  protected $_name = 'fields';

  protected $_elementsBelongTo = 'fields';
	
	
  public function init()
  {
    // custom classified fields
    if( empty($this->_item )) {
      $idea = new Ynfeedback_Model_Idea(array());
      $this->setItem($idea);
    }
	
    parent::init();
    $this->removeElement('submit');
  }

  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this
        ->addDecorator('FormElements')
        ;
    }
  }
}