<?php

class Ynevent_Widget_BrowseAdvsearchController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $viewer = Engine_Api::_()->user()->getViewer();

        $p = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        // Create form
        $formFilter = new Ynevent_Form_Filter_Advbrowse();
        $formFilter->getElement('is_search')->setValue('1');
        
        $formFilter->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'event_general', true));
        
        $defaultValues = $formFilter->getValues();
		
        // Populate form data
        
        if ($formFilter->isValid($p)) {
            $this->view->formValues = $values = $formFilter->getValues();
            
        } else {
            $formFilter->populate($defaultValues);
            $this->view->formValues = $values = array();
        }
        
        // Prepare data         
        $this->view->formValues = $values = $formFilter->getValues();
        if ($viewer->getIdentity() && @$values['view'] == 5) {
            $values['users'] = array();
            foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo) {
                $values['users'][] = $memberinfo->user_id;
            }
        } else {
            if ($viewer->getIdentity() && @$values['view'] != null) {
                $memberTable = Engine_Api::_()->getDbtable('membership', 'ynevent');
                $values['events'] = array();
                foreach ($memberTable->getMemberEvents($viewer->user_id, $values['view']) as $event) {
                    $values['events'][] = $event->resource_id;
                }
            }
        }

        $values['search'] = '1';
        $values['is_search'] = '1';

        if ($filter == "past") {
            $values['past'] = 1;
        } else {
            $values['future'] = 1;
        }

        // check to see if request is for specific user's listings
        if (($user_id = $this->_getParam('user'))) {
            $values['user_id'] = $user_id;
        }
       
        if (!empty($p['mile_of']))
        	if (empty($p['zipcode']))
        	{
        		$formFilter->addError(Zend_Registry::get('Zend_Translate')->_('Please enter the zipcode/postal code'));
        		$this->view->mile_of_error = true;
        	}
        	
        $this->view->form = $formFilter;
    }

}
