<?php
class Tfcampaign_Widget_FilterCampaignsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this -> view -> params = $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$to = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.min_year', 1985);
		$age_to = intval(date('Y')) - intval($to);
		$from = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.max_year', 2003);
		$age_from = intval(date('Y')) - intval($from);
		
		$this->view->age_from = $this->view->max_age_from = $age_from;
		$this->view->age_to = $this->view->max_age_to = $age_to;
		
		if (!empty($params['from_age'])) {
			$this->view->age_from = $params['from_age'];
		}
		
		if (!empty($params['to_age'])) {
			$this->view->age_to = $params['to_age'];
		}
	}
}
