<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynbanmem
 * @author     YouNet Company
 */
class Ynbanmem_Form_Add extends Engine_Form {
	
	protected $_type;

    public function setType($type) {
		$this->_type = $type;
	}
	
    public function init() {

        // Init form
       
        $user = Engine_Api::_()->user()->getViewer();
        $this->setTitle('Ban Members');

        $maindescription = $this->getTranslator()->translate(
                'Social networks are often the target of aggressive spam tactics. This most often comes in the form of fake user accounts and spam in comments. On this page, you can manage various anti-spam and censorship features. Note: To turn on the signup image verification feature (a popular anti-spam tool), see the Signup Progress page. <br>');

        // Decorators
        $this->loadDefaultDecorators();
        $this->getDecorator('Description')->setOption('escape', false);

        // Set form attributes
        //$this->setTitle('Ban new Member');
        $this->setDescription($maindescription);
		$this->addAdditionalElements();

       
        

        // Expiry date
        $expiry_date = new Engine_Form_Element_CalendarDateTime('expiry_date');
        $expiry_date->setLabel("Expiry Date");
        $expiry_date->setAllowEmpty(true);
        $this->addElement($expiry_date);

        // init Reason
        $this->addElement('textarea', 'reason', array(
            'label' => '*Reason',
            'required' => true,
            'value' => 'Spam'
        ));

        // init submit

        $this->addElement('Button', 'submit', array(
            'label' => 'Ban',
            'type' => 'submit',
			'style'=> 'margin-left: 170px;',
            'link' => true,
            'ignore' => true,
            'onclick' => 'actionSubmit()',
            'decorators' => array('ViewHelper',),
        ));      
        
        
        // Set default action
        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    }
     protected function addAdditionalElements() {
        // Init info
        $this->addElement('Select', 'type', array(
            'label' => '*Type of ban',
            'multiOptions' => array('0' => 'Email',
                                    '1' => 'Username',
                                    '2'=> 'Ip'),
            'onchange' => "updateTextFields()",
            'value' => $this->_type,
        ));

       
        $user = Engine_Api::_()->user()->getViewer();
       
        //ADD AUTH STUFF HERE
        // Init Email
        $this->addElement('Textarea', 'email', array(
            'label' => '*Email Address Ban',
            'description' => 'YNBANMEM_FORM_EMAILBANS_DESCRIPTION',
			
            //'maxlength' => '150'
        ));
        $this->email->getDecorator("Description")->setOption("placement", "append");

        // Init Username
        // Init Email
        $this->addElement('Textarea', 'username', array(
            'label' => '*Username Address Ban',
            'description' => 'YNBANMEM_FORM_USERNAMEBANS_DESCRIPTION',
			
            //'maxlength' => '150'
        ));
        $this->username->getDecorator("Description")->setOption("placement", "append");

		$translator = $this->getTranslator();
		if( $translator ) {
		  $description = sprintf($translator->translate('YNBANMEM_FORM_IPBANS_DESCRIPTION'), Engine_IP::normalizeAddress(Engine_IP::getRealRemoteAddress()));
		} else {
		  $description = 'YNBANMEM_FORM_IPBANS_DESCRIPTION';
		}
        // Init Ip
        $this->addElement('Textarea', 'ip', array(
            'label' => '*IP Address Ban',
           'description' => $description,
		   
            //'maxlength' => '150'
        ));
        $this->ip->getDecorator("Description")->setOption("placement", "append");

	
		
        // init Email Message
        $value = "Your account has been banned.";
        $this->addElement('textarea', 'email_message', array(
            'label' => '*Message',
            'style' => 'width: 430px; height: 200px',
            
            'value' => $value
        ));
		$this->email_message->getDecorator("Description")->setOption("placement", "append");
    }
}