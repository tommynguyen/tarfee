<?php
class ProfileCompleteness_AdminManageController extends Fields_Controller_AdminAbstract {

    protected $_fieldType = 'user';
    protected $_requireProfileType = true;

    public function indexAction() {
        parent::indexAction();
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('profilecompleteness_admin_main', array(), 'profilecompleteness_admin_main_manage');
                
        $type_id = 1;
        $option_id = $this->_getParam('option_id');
        if(empty($option_id)){
            $option_id = 1;
        }
        $this->view->option_id = $option_id;
        $table =  new ProfileCompleteness_Model_DbTable_Weights;
        $db = $table->getAdapter();
        $select = $table->select()->setIntegrityCheck(false);
        $maptable = $db->select()
                    ->from(array('map' => 'engine4_user_fields_maps'))
                    ->from(array('meta' => 'engine4_user_fields_meta'), array())
                    ->where("map.child_id = meta.field_id AND meta.TYPE != 'heading'")
                    ->where('map.field_id=?', $type_id)
                    ->where('map.option_id = ?', $option_id);
        
        $result = $db->select()
                    ->from(array('m' => $maptable), array('m.option_id','m.child_id'))
                    ->joinleft(array('w' => 'engine4_profilecompleteness_weights'), 'm.`child_id` = w.`field_id`', array('w.field_id'))
                    ->where('w.field_id IS NULL');
        
        $select->from(array('r'=>$result));
        $rows = $table->fetchAll($select);
        
        if(count($rows) != 0){
            $db = $table->getAdapter();
            $db->beginTransaction();
            foreach($rows as $row){
                $values = array(
                    'field_id' => $row->child_id,
                    'weight' => 1,
                    'type_id' => $row->option_id);
                $row = $table->createRow();
                $row->setFromArray($values);
                $row->save();
            }
            $db->commit();
        }
        
        
        $this->view->type = $option_id;
    }

    public function editAction() {
        parent::indexAction();
        $this->view->type = $type_id = $this->_getParam('option_id');
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $table =  new ProfileCompleteness_Model_DbTable_Weights;
            $db = $table->getAdapter();
            $db->beginTransaction();
            foreach ($values as $key => $value) {
                $select = $table->select()
                        ->where('type_id = ?', $type_id)
                        ->where('field_id = ?', $key);
                $row = $table->fetchRow($select);
                if ($row) {
                    $row->weight = $value;
                    $row->save();
                }
                else
                {
                	$params = array(
								'type_id' => $type_id,
								'field_id' => $key,
								'weight' => $value);
					$row = $table->createRow();
					$row->setFromArray($params);
					$row->save();
                }
            }
            $db->commit();
            return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }
    }
}

?>
