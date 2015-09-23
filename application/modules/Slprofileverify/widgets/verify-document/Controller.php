<?php
class Slprofileverify_Widget_VerifyDocumentController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->user = $user = Engine_Api::_()->core()->getSubject();
    
    if(!$viewer->isAdmin() && !$viewer->isSelf($user)){ return $this->setNoRender(); }
    
    if($viewer->isAdmin()){
        $this->view->is_admin = true;
    }
    
    $this->view->user_id = $user_id = $user->getIdentity();
    $slVerifyTbl= Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
    $this->view->verifyRow = $verifyRow = $slVerifyTbl->getVerifyInfor($user_id);
    
    if(!$verifyRow || $verifyRow->approval == 'default'){ return $this->setNoRender(); }
    
    // Load fields view helpers
    $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
    
    $fieldUserStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
    $slUserRow = Engine_Api::_()->getItem('slprofileverify_user', $user_id);
    $fieldUser = array();
    if($slUserRow){
        $fieldIdRequired = array_flip(Zend_Json::decode($slUserRow['value']));
        foreach($fieldUserStructure as $key=>$userStructure){
            if(in_array($key, $fieldIdRequired)){
                $fieldUser[$key] = $userStructure;
            }
        }
    }
    $this->view->fieldUserStructure = $fieldUser;
    $this->view->aFileId = Zend_Json::decode($verifyRow->file_id);
    
    // Field Values Slprofile
    $this->view->fieldSlverifyStructure = $fieldSlverifyStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($verifyRow);
    $this->view->aFileIdCus = Zend_Json::decode($verifyRow->file_id_cus);
    $settingCore = Engine_Api::_()->getApi('settings', 'core');
    $this->view->enable_step = $settingCore->getSetting('sl_enable_step', 0);
    return; 
  }
}