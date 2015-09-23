<?php
class Yncomment_Model_DbTable_Modules extends Engine_Db_Table {

    protected $_name = 'yncomment_modules';
    protected $_rowClass = 'Yncomment_Model_Module';

    public function checkEnableModule($params = array()) 
    {
        return $this->select()->from($this->info('name'), array('enabled'))->where('resource_type =?', $params['resource_type'])->where('enabled =?', 1)->query()->fetchColumn();
    }

    public function getModules() 
    {
        $modules = array();
        $tableName = $this->info('name');
        $coreTable = Engine_Api::_()->getDbtable('modules', 'core');
        $coreTableName = $coreTable->info('name');

        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from($tableName, array('resource_type'))
                ->join($coreTableName, "$coreTableName . name = $tableName . module", array('enabled', 'title', 'name'))
                ->where($tableName . '.enabled = ?', 1)
                ->where($coreTableName . '.enabled = ?', 1);
        $row = $select->query()->fetchAll();

        if (!empty($row)) {
            $modules[0] = '';
            foreach ($row as $modName) {
                $modules[$modName['resource_type']] = $modName['title'];
            }
        }

        return $modules;
    }

    public function getModuleName($params = array()) {

        if ($params['resource_type'] === 0) {
            return false;
        }
        return $this->select()->from($this->info('name'), array('module'))->where('resource_type =?', $params['resource_type'])->where('enabled =?', 1)->query()->fetchColumn();
    }
}