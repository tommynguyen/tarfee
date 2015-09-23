<?php
class Slprofileverify_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('slprofileverify_admin_main', array(), 'slprofileverify_admin_main_verify');

    $page = $this->_getParam('page', 1);
    $userTbl = Engine_Api::_()->getDbtable('users', 'user');
    $userTblName = $userTbl->info('name');
    $slverifyTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
    $slverifyTblName = $slverifyTbl->info('name');
    $select = $userTbl->select();
    $select->assemble();
    $select->setIntegrityCheck(false);
    $select->join($slverifyTblName, $userTblName . '.user_id = ' . $slverifyTblName . '.user_id');

    // Process form
    $values = array();
    $this->view->formFilter = $formFilter = new Slprofileverify_Form_Admin_Filter();
    if($formFilter->isValid($this->_getAllParams())){
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    if(!empty($values['displayname'])){
      $select->where('displayname LIKE ?', '%' . $values['displayname'] . '%');
    }
    if(!empty($values['username'])){
      $select->where('username LIKE ?', '%' . $values['username'] . '%');
    }
    if(!empty($values['email'])){
      $select->where('email LIKE ?', '%' . $values['email'] . '%');
    }
    if(!empty($values['level_id'])){
      $select->where('level_id = ?', $values['level_id'] );
    }
    
    $enable_pending = true;
    if(isset($values['enabled']) && in_array($values['enabled'], array('pending', 'verified'))){
        $select->where('approval = ?', $values['enabled']);
        if($values['enabled'] == 'pending'){
            $select->order('request_date DESC');
        } else{
            $select->order('verified_date DESC');
            $enable_pending = false;
        }
    } else{
        $select->where('approval = ?', 'pending');
        $select->order('request_date DESC');
    }
    $this->view->enable_pending = $enable_pending;

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    
    $contentTbl = Engine_Api::_()->getDbTable('content', 'core');
    $selectContent = $contentTbl->select()
                         ->from($contentTbl->info('name'), array('content_id'))
                         ->where('name = ?', 'slprofileverify.verify-document')
                         ->limit(1);
    $contentRow = $contentTbl->fetchRow($selectContent);
    $this->view->tab_id = $contentRow['content_id'];
      
  }
	
}