<?php
class Tfcampaign_Form_Submit extends Engine_Form
{
  protected $_campaign;
  
  public function setCampaign($campaign) {
    $this ->_campaign = $campaign;
  }
  
  public function getCampaign() {
  	return $this ->_campaign;
  }
  
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	$viewer = Engine_Api::_() -> user() -> getViewer();
	
    $this->setTitle('Submit Player');
    $this->setAttrib('class', 'global_form_popup');
    $this->setDescription('Which player do you want to submit?');
	
	$isError = false;
	$errorMessage = array();
	$arrValue = array();
	$arrSubmission = array();
	$viewer = Engine_Api::_() -> user() -> getViewer();
	
	//get submission players
	$submissionPlayers = $this ->_campaign -> getSubmissionPlayers();
	foreach($submissionPlayers as $submissionPlayer) {
		$arrSubmission[] = $submissionPlayer -> player_id;
	}
	//get players available
	$players = Engine_Api::_() -> getItemTable('user_playercard') -> getAllPlayerCard($viewer -> getIdentity());
	foreach($players as $player) 
	{
		if(!in_array($player -> getIdentity(), $arrSubmission)) 
		{
			if($player -> countPercentMatching($this ->_campaign) >= $this ->_campaign -> percentage)
			{
				if($this ->_campaign -> category_id != 0) {
					if($this ->_campaign -> category_id == $player -> category_id) {
						$arrValue[$player -> getIdentity()] = $player -> getTitle();
					}	
				} else {
					$arrValue[$player -> getIdentity()] = $player -> getTitle();
				}
			}
		}
	}
	
	if(count($arrValue)) {
		 $this->addElement('Select', 'player_id', array(
	      'label' => 'Player card',
	      'multiOptions' => $arrValue,
	    ));
    } else {
    	$isError = true;
		$errorMessage[] = $view -> translate("There are no available players");
    }
		
	//Title
    $this->addElement('Text', 'title', array(
      'label' => 'Note',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> title -> setAttrib('required', true);
		
	$this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 300)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this -> description -> setAttrib('required', true);
	
	
	//if error disable this button
    $this->addElement('Button', 'submit_button', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
	$startDate = date_create($this ->_campaign->start_date);
	$endDate = date_create($this ->_campaign->end_date);
    $nowDate = date_create('now');
    if ($nowDate < $startDate) {
    	
		$startDateObj = null;
		if (!is_null($this ->_campaign->start_date) && !empty($this ->_campaign->start_date) && $this ->_campaign->start_date) 
		{
			$startDateObj = new Zend_Date(strtotime($this ->_campaign->start_date));	
		}
		if( $viewer->getIdentity() ) {
			$tz = $viewer->timezone;
			if (!is_null($startDateObj))
			{
				$startDateObj->setTimezone($tz);
			}
	    }
    	$errorMessage[] = $view -> translate("Open in %s", (!is_null($startDateObj)) ?  date('d M, Y', $startDateObj -> getTimestamp()) : '');
		$isError = true;
    }
	
	if($isError) {
		$this -> submit_button -> setAttrib("disabled", "disabled");
		$this -> addErrors($errorMessage);
	}
	
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => '',
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper'
        )
    ));
    
	 $this->addDisplayGroup(array('submit_button', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}
