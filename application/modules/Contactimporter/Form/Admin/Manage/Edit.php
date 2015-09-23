<?php
class Contactimporter_Form_Admin_Manage_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'admin_providers_edit')
      ->setTitle('Edit Provider')
      ->setDescription('You can change the details of this provider here.');

    // init title
    $this->addElement('Text', 'title', array(
      'label' => 'Provider Title'
    ));
    
    $multiOptionsOrder = array();
    for($count = 1;$count<=200;$count++)
    {
        $multiOptionsOrder[$count] = $count;
    }
     $this->addElement('Select', 'order', array(
      'label' => 'Order',
      'multiOptions' => $multiOptionsOrder,
    ));

    $this->addElement('Select', 'enable', array(
      'label' => 'Enable/Disable',
      'multiOptions' => array(
            '1' => 'Enable',
            '0' => 'Disable'
        ),
    ));


    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));



    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');



    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}