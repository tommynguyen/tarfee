<?php
class Ynfeedback_Model_DbTable_Files extends Engine_Db_Table {
    protected $_rowClass = 'Ynfeedback_Model_File';
    protected $_name = 'ynfeedback_files';
    
    public function getFilesOfIdea($idea_id) {
        $select = $this->select()->where('idea_id = ?', $idea_id);
        return $this->fetchAll($select);
    }
    
    public function deleteFilesByIdea($idea_id) {
        $where = $this->getAdapter()->quoteInto('idea_id = ?', $idea_id);
        $this->delete($where);
    }
}