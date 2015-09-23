<?php
class Slprofileverify_AdminAjaxController extends Core_Controller_Action_Admin
{
  public function init() {
      $this->_helper->layout->disableLayout();
  }

  public function getVerifyAction()
  {
      if ($this->getRequest()->isXmlHttpRequest()) {
        $arrFieldIdNew = $this->_getParam('data');
        $optionId = $this->_getParam('option_id');
        $arrFieldId = array();
        if($optionId){ 
            $requiresTbl = Engine_Api::_()->getDbTable('requires', 'slprofileverify');
            $requireRow = $requiresTbl->getRequireRow($optionId);
            if($requireRow){    
                $arrFieldIdOld = Zend_Json::decode($requireRow['required']);
                $arrFieldId = $arrFieldIdOld;
                if(count($arrFieldIdNew)){
                    $arrFieldId = $arrFieldIdNew;
                    if(count($arrFieldIdOld)){
                        $arrFieldId = array_merge($arrFieldIdNew, $arrFieldIdOld);
                    }
                    $where = array();
                    $where[] = $requiresTbl->getDefaultAdapter()->quoteInto('option_id = ?', $optionId);
                    $data = array(
                        'required' => Zend_Json::encode($arrFieldId),
                    );
                    $requiresTbl->update($data, $where);
                }
            } else{ 
                if(count($arrFieldIdNew) > 0){
                    $arrFieldId = $arrFieldIdNew;
                    $data = array(
                        'option_id' => $optionId,
                        'exp_document' => "",
                        'image' => "",
                        'required' => Zend_Json::encode($arrFieldId),
                    );
                    $requiresTbl->insert($data);
                } else{
                    return null;
                }
                
            }
            
            if(!count($arrFieldId)){
                return null;
            }
            
            $mapsTbl = Engine_Api::_()->fields()->getTable('user', 'maps');
            $mapsNameTbl = $mapsTbl->info('name');
            $metaTbl = Engine_Api::_()->fields()->getTable('user', 'meta');
            $metaNameTbl = $metaTbl->info('name');
            $select = $mapsTbl->select()->setIntegrityCheck(false)
                ->from($mapsNameTbl, array())
                ->join($metaNameTbl, $mapsNameTbl. '.child_id = ' . $metaNameTbl . '.field_id', array('field_id', 'label'))
                ->where($mapsNameTbl . '.option_id = ?', $optionId)
                ->where($mapsNameTbl . '.child_id IN(?)', $arrFieldId);
            $arrFieldMeta = $mapsTbl->fetchAll($select);
            $this->view->arrFieldMeta = $arrFieldMeta;
        } else{
            return;
        }
        
      } else{
          return;
      }
  }
  
  public function removeVerifyAction()
  {
      if ($this->getRequest()->isXmlHttpRequest()) {
        $arrFieldIdNew = $this->_getParam('data');
        $optionId = $this->_getParam('option_id');
        $arrFieldId = array();
        if($optionId){ 
            $requiresTbl = Engine_Api::_()->getDbTable('requires', 'slprofileverify');
            $requireRow = $requiresTbl->getRequireRow($optionId);
            if($requireRow['option_id']){             
                $arrFieldIdOld = Zend_Json::decode($requireRow['required']);
                if(count($arrFieldIdNew) > 0){
                    $arrFieldId = array_diff( $arrFieldIdOld, $arrFieldIdNew ); 
                    $where = array();
                    $where[] = $requiresTbl->getDefaultAdapter()->quoteInto('option_id = ?', $optionId);
                    $data = array(
                        'required' => Zend_Json::encode($arrFieldId),
                    );
                    $requiresTbl->update($data, $where);
                } else{
                    return;
                }
            } else{ 
                return;
            }

            $mapsTbl = Engine_Api::_()->fields()->getTable('user', 'maps');
            $mapsNameTbl = $mapsTbl->info('name');
            $metaTbl = Engine_Api::_()->fields()->getTable('user', 'meta');
            $metaNameTbl = $metaTbl->info('name');
            $select = $mapsTbl->select()->setIntegrityCheck(false)
                ->from($mapsNameTbl, array())
                ->join($metaNameTbl, $mapsNameTbl. '.child_id = ' . $metaNameTbl . '.field_id', array('field_id', 'label'))
                ->where($mapsNameTbl . '.option_id = ?', $optionId)
                ->where($metaNameTbl . '.type != ?', 'heading');
            if($arrFieldId){
                $select->where($metaNameTbl . '.field_id NOT IN(?)', $arrFieldId);
            }
            $fieldMeta = $mapsTbl->fetchAll($select);
            $this->view->listFieldMeta = $fieldMeta;
        } else{
            return;
        }
        
      }
  }
	
}