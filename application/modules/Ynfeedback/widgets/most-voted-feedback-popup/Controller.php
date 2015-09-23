<?php
class Ynfeedback_Widget_MostVotedFeedbackPopupController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $params = array(
            'orderby' => 'vote_count',
            'direction' => 'DESC'
        );
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this -> view -> inputTitle = $request->getParam('inputTitle');
		$this -> view -> isFinal = $request->getParam('isFinal');
        $ideaTbl = Engine_Api::_()->getItemTable('ynfeedback_idea');
        $select = $ideaTbl -> getIdeasSelect($params);
        $this -> view -> ideas = $ideaTbl -> fetchAll($select);
    }
}
