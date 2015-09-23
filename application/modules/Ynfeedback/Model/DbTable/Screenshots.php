<?php
class Ynfeedback_Model_DbTable_Screenshots extends Engine_Db_Table {
    protected $_rowClass = 'Ynfeedback_Model_Screenshot';
    protected $_name = 'ynfeedback_screenshots';
    
    public function getScreenshotsOfIdea($idea_id) {
        $select = $this->select()->where('idea_id = ?', $idea_id);
        return $this->fetchAll($select);
    }
    
    public function deleteScreenshotsByIdea($idea_id) {
        $where = $this->getAdapter()->quoteInto('idea_id = ?', $idea_id);
        $this->delete($where);
    }
}