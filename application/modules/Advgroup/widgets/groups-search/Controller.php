<?php
class Advgroup_Widget_GroupsSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
    // Get quick navigation.
    $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_quick');
    
	$viewer = Engine_Api::_()->user()->getViewer();
	
    // Create search form.
    $search_form = $this->view->form = new Advgroup_Form_Search();
    $search_form->setAction($this->view->baseUrl() . "/groups/listing");
	
	if( !$viewer || !$viewer->getIdentity() ) {
      $search_form ->removeElement('view');
    }
	
    $request = Zend_Controller_Front::getInstance() -> getRequest();
    $params = $request->getParams();
    $search_form->populate($params);
    }
}