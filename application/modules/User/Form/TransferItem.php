<?php
class User_Form_TransferItem extends Engine_Form {
	protected $_item;
	
	public function getItem() {
		return $this->_item;
	}
	
	public function setItem($item) {
		$this->_item = $item;
	}
  public function init()
  {
  	
	$item = $this->getItem();
	$view = Zend_Registry::get('Zend_View');
	$label = ($item->parent_type != 'group') ? $view->translate('club') : $view->translate('user profile');
	$title = $view->translate('Transfer item to %s', $label);
	$description = $view->translate('Are you sure you want to transfer this item to %s ?', $label);
    $this
      ->setTitle($title)
      ->setDescription($description)
      ->setAttrib('method', 'post')
	  ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Element: token
    $this->addElement('Hash', 'token');

    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Yes',
      'type' => 'submit',
      'ignore' => true,
      //'style' => 'color:#D12F19;',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'onclick' => 'parent.Smoothbox.close()',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons');
    
    return $this;
  }
}