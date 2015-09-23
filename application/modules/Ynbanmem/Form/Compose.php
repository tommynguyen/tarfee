<?php

class Ynbanmem_Form_Compose extends Engine_Form
{
  public function init()
  {
    $this->setTitle('Compose Message');
    $this->setDescription('Create your new message with the form below. Your message can be addressed to up to 10 recipients.')
       ->setAttrib('id', 'notice_compose');
	   
    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    
    // init to
    $this->addElement('Text', 'to',array(
        'label'=>'Send To',
        'autocomplete'=>'off'));

    Engine_Form::addDefaultDecorators($this->to);

    // Init to Values
    $this->addElement('Hidden', 'toValues', array(
      'label' => 'Send To',
      'required' => true,
      'allowEmpty' => false,
      'order' => 2,
      'validators' => array(
        'NotEmpty'
      ),
      'filters' => array(
        'HtmlEntities'
      ),
    ));
    Engine_Form::addDefaultDecorators($this->toValues);

    // init title
    $this->addElement('Text', 'title', array(
      'label' => 'Subject',
      'order' => 4,
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_HtmlSpecialChars(),
      ),
    ));
    
    // init body - editor
    $editor = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $user_level, 'editor');
    
    if ( $editor == 'editor' ) {
      $this->addElement('TinyMce', 'body', array(
        'disableLoadDefaultDecorators' => true,
        'order' => 5,
        'required' => true,
        'editorOptions' => array(
          'bbcode' => true,
          'html' => true,
        ),
        'allowEmpty' => false,
        'decorators' => array(
            'ViewHelper',
            'Label',
            array('HtmlTag', array('style' => 'display: block;'))),
        'filters' => array(
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      )); 
    } else {
      // init body - plain text
      $this->addElement('Textarea', 'body', array(
        'label' => 'Message',
        'order' => 5,
        'required' => true,
        'allowEmpty' => false,
        'filters' => array(
          new Engine_Filter_HtmlSpecialChars(),
          new Engine_Filter_Censor(),
          new Engine_Filter_EnableLinks(),
        ),
      ));
    }
	$this->addAdditionalElements();
	 
	$this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
	
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Message',
      'order' => 7,
      'type' => 'submit',
      'ignore' => true
    ));
  }
	protected function addAdditionalElements() {
	
        // Init info
        $this->addElement('Select', 'type', array(
            'label' => '*Type',
            'multiOptions' => array('1' => 'Notice',
                                    '2' => 'Warning',
                                    '3'=> 'Infraction'),
            //'onchange' => "updateTextFields()",
        ));
		// Init info
        $this->addElement('Select', 'from', array(
            'label' => '*Send from',
			'order' => 3,
            'multiOptions' => array('1' => 'Your email',
                                    '2' => 'Site admin email'),
           
        ));
       
        //ADD AUTH STUFF HERE
        // Init Email
        $this->addElement('Textarea', 'reason', array(
            'label' => '*Reason',
			'order' => 6,
            'description' => '',
			'required' => true,
            //'maxlength' => '150'
        ));
        $this->reason->getDecorator("Description")->setOption("placement", "append");

       
    }
  }