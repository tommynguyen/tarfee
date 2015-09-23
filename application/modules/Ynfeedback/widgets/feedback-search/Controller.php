<?php
class Ynfeedback_Widget_FeedbackSearchController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
       	$viewer = Engine_Api::_()->user()->getViewer();
	    $this->view->form = $form = new Ynfeedback_Form_Search();
		
	    $request = Zend_Controller_Front::getInstance()->getRequest();
        $module = $request->getParam('module');
        $controller = $request->getParam('controller');
        $action = $request->getParam('action');
        $forwardListing = true;
        if ($module == 'ynfeedback') {
            if ($controller == 'index' && (in_array($action, array('manage', 'manage-follow')))) {
                $forwardListing = false;
            }
        }
        if ($forwardListing) {
            $form -> setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynfeedback_general', true));
        }
		
	    $request = Zend_Controller_Front::getInstance() -> getRequest();
	    $params = $request->getParams();
	    $form->populate($params);
    }
}
