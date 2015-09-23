<?php
class Slprofileverify_Plugin_Menus
{
  public function disable()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $spverifyTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
    $verifyRow = $spverifyTbl->getVerifyInfor($viewer->user_id);
    if($verifyRow->approval == 'verified' || $viewer->isAdmin()){ return false; }
    return true;
  }
  public function onMenuInitialize_UserProfileVerified()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    $metaTbl = Engine_Api::_()->getDbTable('slprofileverifies', 'slprofileverify');
    $verifyRow = $metaTbl->getVerifyInfor($subject->getIdentity());
    if($viewer->isAdmin()){
        if($verifyRow->approval == 'verified'){
            return array(
                'label' => $translate->translate("Unverify this user"),
                'icon' => 'application/modules/Slprofileverify/externals/images/un_verify.png',
                'class' => 'smoothbox',
                'route' => 'default',
                'params' => array(
                  'module' => 'slprofileverify',
                  'controller' => 'verify',
                  'action' => 'deny',
                  'id' => $subject->getIdentity(),
                  'type' => 'unverifying'
                ),
            );
        } else{
            return array(
                'label' => $translate->translate("Verify this user"),
                'icon' => 'application/modules/Slprofileverify/externals/images/verify.png',
                'class' => 'smoothbox',
                'route' => 'default',
                'params' => array(
                  'module' => 'slprofileverify',
                  'controller' => 'verify',
                  'action' => 'verify',
                  'id' => $subject->getIdentity()
                ),
            );
        }
    }
    
    if($verifyRow->approval == 'verified'){
        return false;
    }
    
    if( Engine_Api::_()->authorization()->isAllowed('slprofileverify', null, 'send') && $viewer->user_id == $subject->getIdentity()) {
        return array(
            'icon' => 'application/modules/Slprofileverify/externals/images/send_receive.png',
            'route' => 'default',
            'params' => array(
              'module' => 'slprofileverify',
              'controller' => 'index',
              'action' => 'setting-verification',
            ),
        );
    }
    
  }
}