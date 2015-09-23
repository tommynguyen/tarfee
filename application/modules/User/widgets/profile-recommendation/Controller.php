<?php
class User_Widget_ProfileRecommendationController extends Engine_Content_Widget_Abstract {
	public function indexAction() {

		$this->view->section = 'recommendation';
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
		
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() == $user->getIdentity()) {
			$this->view->params = $params = $this->_getAllParams();
			if (!empty($params['hide'])) {
				$recommendation = Engine_Api::_()->getItem('user_recommendation', $params['hide']);
				if ($recommendation) {
					$recommendation->show = 0;
					$recommendation->save();
				}
			}
			
			if (!empty($params['show'])) {
				$recommendation = Engine_Api::_()->getItem('user_recommendation', $params['show']);
				if ($recommendation) {
					$recommendation->show = 1;
					$recommendation->save();
				}
			}
			
			if (!empty($params['delete'])) {
				$recommendation = Engine_Api::_()->getItem('user_recommendation', $params['delete']);
				if ($recommendation) {
					$recommendation->delete();
				}
			}
			
			if (!empty($params['approve'])) {
				$recommendation = Engine_Api::_()->getItem('user_recommendation', $params['approve']);
				if ($recommendation) {
					$recommendation->approved = 1;
					$recommendation->approved_date = date('Y-m-d H:i:s');
					$recommendation->save();
				}
			}
		}
		else {
			$this->view->params = array();
		}
		
	}
}
