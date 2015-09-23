<?php
class Slprofileverify_Widget_ProfileBadgeController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $subject = Engine_Api::_()->core()->getSubject('user');
    $slverifyTbl = Engine_Api::_()->getItemTable('slprofileverify_slprofileverify');
    $verifyRow = $slverifyTbl->getVerifyInfor($subject->getIdentity());
    if($verifyRow->approval == 'verified')
    {
        $settingsCore = Engine_Api::_()->getApi('settings', 'core');
        $photo_badge = $settingsCore->getSetting('sl_verify_badge', 0);
        $this->view->src_img = $src_img = Engine_Api::_()->slprofileverify()->getPhotoVerificaiton($photo_badge, null, 'pBadge');
    }
    else 
    {
        return $this->setNoRender();
    }
    
  }
}