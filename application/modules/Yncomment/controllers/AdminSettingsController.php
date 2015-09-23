<?php
class Yncomment_AdminSettingsController extends Core_Controller_Action_Admin {

    // Function: Global Settings
    public function indexAction() 
    {
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yncomment_admin_main', array(), 'yncomment_admin_main_settings');
        $this -> view -> form = $form = new Yncomment_Form_Admin_Global();

        if ($this -> getRequest() -> isPost() && $form -> isValid($this -> _getAllParams())) {
            $values = $form -> getValues();
            foreach ($values as $key => $value) 
            {
                Engine_Api::_() -> getApi('settings', 'core') -> setSetting($key, $value);
            }
            $form -> addNotice(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'));
        }
    }
    
    // Function: Activity Settings
    public function activitySettingsAction() 
    {
        $modulestable = Engine_Api::_() -> getDbtable('modules', 'yncomment');
        $modules = $modulestable -> fetchRow(array('resource_type = ?' => 'ynfeed'));
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('yncomment_admin_main', array(), 'yncomment_admin_main_activitySettings');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $this -> view -> form = $form = new Yncomment_Form_Admin_ActivityEdit();

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$this -> getRequest() -> isPost()) {
            $val = $modules -> toArray();
            if ($val['params']) {
                $decodedParams = Zend_Json_Decoder::decode($val['params']);
                $form -> populate($decodedParams);

                if (isset($decodedParams['showAsLike'])) {
                    $this -> view -> showAsLike = $decodedParams['showAsLike'];
                }

                if (isset($decodedParams['ynfeed_comment_like_box'])) {
                    $this -> view -> ynfeed_comment_like_box = $decodedParams['ynfeed_comment_like_box'];
                }
            } else {
                $form -> populate($modules -> toArray());
            }
            return;
        }

        //IF NOT POST OR FORM NOT VALID THAN RETURN
        if (!$form -> isValid($this -> getRequest() -> getPost())) {
            return;
        }

        //GET FORM VALUES
        $values = $form -> getValues();
        $values['module'] = $modules -> module;
        $values['resource_type'] = $modules -> resource_type;

        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();
        try {

            $modules -> setFromArray($values);
            $modules -> save();
            
            $modules -> params = Zend_Json_Encoder::encode($values);
            $modules -> save();
            $db -> commit();

            $form -> addNotice('Your changes have been saved.');
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
    }

}