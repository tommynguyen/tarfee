<?php
class Ynfeedback_Form_Comment_Create extends Engine_Form
{
	  public function init()
	  {
		    $this->clearDecorators()
		      ->addDecorator('FormElements')
		      ->addDecorator('Form')
		      ->setAttrib('class', null);
		      //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('route' => 'ynfeedback_general')));
		

		    // Member Level specific 
		    $viewer = Engine_Api::_()->user()->getViewer();
		    $allowed_html = "";
		    if($viewer->getIdentity()){
		      $allowed_html = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'commentHtml');
		    }
		    
		    if ($viewer -> getIdentity() == 0)
		    {
		    	$this->addElement('Text', 'poster_name', array(
			      'label' => '*Your Name',
			      'allowEmpty' => false,
			      'required' => true,
			      'filters' => array(
			        new Engine_Filter_Censor(),
			        'StripTags',
			        new Engine_Filter_StringLength(array('max' => '63'))
			      ),
			    ));
			    
			    $this->addElement('Text', 'poster_email', array(
			      'label' => '*Your Email',
			      'allowEmpty' => false,
			      'required' => true,
			      'filters' => array(
			        new Engine_Filter_Censor(),
			        'StripTags',
			      ),
			      'validators' => array(
                    array('NotEmpty', true),
                    array('EmailAddress', true)
                  ),
			    ));
		    }
		    
		    $this->addElement('Textarea', 'body', array(
		      	'rows' => 1,
		    	'allowEmpty' => false,
			    'required' => true,
		        'filters' => array(
    		        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)),
    		        new Engine_Filter_Censor(),
		      ),
		    ));
		    if ($viewer -> getIdentity() == 0)
            {
                $this -> body -> setLabel('*Content');
            }
		    if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ) {
		      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions());
		    }
		
		    $this->addElement('Button', 'submit', array(
		      'type' => 'submit',
		      'ignore' => true,
		      'label' => 'Post Comment',
		      'decorators' => array(
		        'ViewHelper',
		      )
		    ));
		    
		    $this->addElement('Hidden', 'type', array(
		      'order' => 990,
		      'validators' => array(
		        // @todo won't work now that item types can have underscores >.>
		        // 'Alnum'
		      ),
		    ));
		  
		    $this->addElement('Hidden', 'identity', array(
		      'order' => 991,
		      'validators' => array(
		        'Int'
		      ),
		    ));
	  }
}