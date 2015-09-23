<?php
class Slprofileverify_Plugin_Core
{
  public function onUserEnable($event)
  {
    $user = $event->getPayload();
    if( !($user instanceof User_Model_User) ) { return; }
    
    $slverifyTbl = Engine_Api::_()->getItemTable('slprofileverify_slprofileverify');
    $verifyRow = $slverifyTbl->getVerifyInfor($user->user_id);
    if(!$verifyRow){
        $data =  array(
            'user_id' => $user->user_id,
            'approval' => 'default',
            'verified_date' => '0000-00-00 00:00:00',
            'request_date' => '0000-00-00 00:00:00',
            'file_id' => 0,
            'file_id_cus' => 0,
            'reason' => null
        );
        $slverifyTbl->insert($data);
    }
  }
  
  public function onFieldsValuesSave($event)
  {
    $payload = $event->getPayload();
    if( $payload['item'] instanceof User_Model_User ) {
        $user_id = $payload['item']->user_id;
        // Status User Send Verify
        $slverifyTbl = Engine_Api::_()->getItemTable('slprofileverify_slprofileverify');
        $verifyRow = $slverifyTbl->getVerifyInfor($user_id);
        // User Verify
        $slverifyUserTbl = Engine_Api::_()->getDbTable('users', 'slprofileverify');
        $select = $slverifyUserTbl->select()->from($slverifyUserTbl->info('name'))->where('user_id = ?', $user_id);
        $slverifyUserRow = $slverifyUserTbl->fetchRow($select);
        $slverifyValue = Zend_Json::decode($slverifyUserRow['value']);
        // User alias value field
        $aliasedFields = Engine_Api::_()->fields()->getFieldsObjectsByAlias($payload['item']);
        $field_id = $aliasedFields['profile_type']->field_id;
        $profileOption = $aliasedFields['profile_type']->getOption($payload['item']);
        $option_id = $profileOption->option_id;
        
        // Check data change
        $check = "true";
        $slverifyValueSave = array();
        foreach ($payload['values']->toArray() as $value){
            $tmp = $field_id . '_' . $option_id . '_' . $value['field_id'];
            if($slverifyValue[$tmp]){
                if(md5($value['value']) != md5($slverifyValue[$tmp])){
                    $check = "false";
                }
                $slverifyValue[$tmp] = $value['value'];
            }
            $slverifyValueSave[$tmp] = $value['value'];
        }

        if($verifyRow->approval == 'verified' && $check == "false"){
            $view = Zend_Registry::get("Zend_View");
            $link = $view->url(array('module' => 'slprofileverify', 'controller' => 'index', 'action' => 'profile-change'), 'default', true);
            $link_close = $view->url(array('module' => 'slprofileverify', 'controller' => 'index', 'action' => 'profile-change', 'type' => 'close'), 'default', true);
            $format = "" . 
            'window.addEvent("domready", function(){ ' .
                "Smoothbox.open('" . $link ."', {onClose:function(){window.location.href='". $link_close ."'}});" .
                "$$('.global_form .form-notices').destroy();" .
            '});';
            $view->headScript()->appendScript($format);
        } else{
            if(!$slverifyUserRow){
                $insert = $slverifyUserTbl->createRow();
                $insert->user_id = $user_id;
                $insert->value = Zend_Json::encode($slverifyValueSave);
                $insert->save();
            } else{
                $slverifyUserRow->value = Zend_Json::encode($slverifyValue);
                $slverifyUserRow->save();
            }
        }
    }
  }
}