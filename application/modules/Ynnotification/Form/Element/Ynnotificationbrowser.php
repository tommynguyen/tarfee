<?php

class Ynnotification_Form_Element_Ynnotificationbrowser extends Zend_Form_Element_Text
{
	
  public $helper = 'formYnnotificationbrowser';
  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators()
  {
  	
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      $this->addDecorator('ViewHelper');
      Engine_Form::addDefaultDecorators($this);
    }
  }
  
  //public function setValue($value)
}
