<?php
class Ynfeedback_Model_File extends Core_Model_Item_Abstract {
	protected $_type = 'ynfeedback_file';
	protected $_parent_type = 'ynfeedback_idea';
    protected $_searchTriggers = false;
    
    public function getDownloadLink() {
        $storageFile = Engine_Api::_()->getItemTable('storage_file')->getFile($this->storagefile_id);
        if( $storageFile ) {
            return $storageFile->map();
        }
    }
}
