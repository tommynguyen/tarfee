<?php
class Tfcampaign_Form_WithDrawList extends Engine_Form
{
  protected $_campaign;
  
  public function getCampaign() {
  	return $this ->_campaign;
  }
  
  public function setCampaign($campaign) {
  	$this ->_campaign = $campaign;
  }
  
  public function init()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
	$view = Zend_Registry::get("Zend_View");
	$viewer = Engine_Api::_() -> user() -> getViewer();
    $this -> setTitle('Withdraw Player');
	$this -> setAttrib('class', 'global_form_popup');
	$this -> setDescription("Choose player that you want to withdraw");
	
	$submissionIds = $this ->_campaign -> getSubmissionByUser($viewer);
	$arrValues = array();
	if(count($submissionIds)) {
		foreach($submissionIds as $submissionId) {
			$submission = Engine_Api::_() -> getItem('tfcampaign_submission', $submissionId);
			if($submission) {
				$player = $submission -> getPlayer();
				if($player) {
					$arrValues[$submissionId] = $player -> getTitle();
				}
			}
		}
	}
    $this->addElement('Multiselect', 'submission_ids', array(
      'label' => 'Submission List',
      'multiOptions' => $arrValues,
      'allowEmpty' => false,
      'required' => true,
    ));
	
	 // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Withdraw',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Withdraw',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

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

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}