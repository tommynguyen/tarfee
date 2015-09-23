<?php 
/*
 * Controller for Group Directory widget
 */
class Advgroup_Widget_GroupsDirectoryController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
      $request = Zend_Controller_Front::getInstance()->getRequest();
      // clear title of widget
      if ($request->isPost()) {
      	$this->getElement()->setTitle('');
      	$element = $this->getElement();
      }
      
      $table = Engine_Api::_()->getItemTable('group');
      $select = $table->select()
      ->where('search = ?', 1)
      ->where('is_subgroup = ?', 0)
      ->order('title ASC');
  		     
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
		
      // Set item count per page and current page number
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 12));
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      if( count($paginator) <= 0 ) {
      	return $this->setNoRender();
      }
	}
}
?>