<?php
class Yncomment_Api_Core extends Core_Api_Abstract {

    public function getEmoticons() {
        $table = Engine_Api::_() -> getDbTable("emoticons", "yncomment");
        return $table -> fetchAll($table -> select());
    }
    public function getParams($params) {
        $defaultArray = array('resource_type = ?' => $params['resource_type']);
        if(isset($params['moduleName'])) {
            $defaultArray = array_merge($defaultArray, array('module = ?' => $params['moduleName']));
        }
        
        $customCheck = Engine_Api::_()->getDbTable('modules', 'yncomment')->fetchRow($defaultArray);

        if (!$customCheck->params) {
            return '';
        } else {
            return Zend_Json_Decoder::decode($customCheck->params);
        }
    }

    public function getEnabledModule($params) {
        $defaultArray = array('resource_type = ?' => $params['resource_type']);
        if(isset($params['moduleName'])) {
            $defaultArray = array_merge($defaultArray, array('module = ?' => $params['moduleName']));
        }
        
        $customCheck = Engine_Api::_()->getDbTable('modules', 'yncomment')->fetchRow($defaultArray);

        if (isset($params['checkModuleExist'])) {
            return $customCheck;
        }

        return $customCheck->enabled;
    }

}