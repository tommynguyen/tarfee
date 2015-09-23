<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Playlist extends Core_Model_Item_Abstract {

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'video_playlist',
            'reset' => true,
            'playlist_id' => $this->getIdentity(),
            'slug' => $this->getSlug(),
            'action' => 'view'
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

    function isViewable() {
        return $this->authorization()->isAllowed(null, 'view');
    }

    function isEditable() {
        return $this->authorization()->isAllowed(null, 'edit');
    }

    function isDeletable() {
        return $this->authorization()->isAllowed(null, 'delete');
    }

    public function setPhoto($photo) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Event_Model_Exception('invalid argument passed to setPhoto');
        }

        if ($this->photo_id) {
            $this->removeOldPhoto();
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => 'ynvideo_playlist'
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(400, 400)
                ->write($path . '/m_' . $name)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);

        // Remove temp files
        @unlink($path . '/m_' . $name);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }

    protected function removeOldPhoto() {
        if ($this->photo_id) {
            $item = Engine_Api::_()->storage()->get($this->photo_id);

            $table = Engine_Api::_()->getItemTable('storage_file');
            $select = $table->select()
                    ->where('parent_type = ?', $this->getType())
                    ->where('parent_id = ?', $this->getIdentity());

            foreach ($table->fetchAll($select) as $file) {
                try {
                    $file->delete();
                } catch (Exception $e) {
                    if (!($e instanceof Engine_Exception)) {
                        $log = Zend_Registry::get('Zend_Log');
                        $log->log($e->__toString(), Zend_Log::WARN);
                    }
                }
            }
        }
    }

    protected function _postDelete() {
        parent::_postDelete();

        // Remove all association videos to this playlist
        $table = Engine_Api::_()->getDbtable('playlistassoc', 'ynvideo');
        $select = $table->select()->where('playlist_id = ?', $this->getIdentity());

        foreach ($table->fetchAll($select) as $playlistAssoc) {
            $playlistAssoc->delete();
        }
    }

    public function addVideoToPlaylist($video) {
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
        
        $row = $playlistAssocTbl->fetchRow(array("video_id = {$video->getIdentity()}", "playlist_id = {$this->getIdentity()}"));
        if (!$row) {
            $playlistAssoc = $playlistAssocTbl->createRow();
            $playlistAssoc->video_id = $video->getIdentity();
            $playlistAssoc->playlist_id = $this->getIdentity();
            $playlistAssoc->creation_date = date('Y-m-d H:i:s');
            $playlistAssoc->save();

            $this->video_count = new Zend_Db_Expr('video_count + 1');
            $this->save();
            
            return $playlistAssoc;
        } else {
            throw new Ynvideo_Model_ExistedException();
        }        
    }

    public function getVideos() {
        $videoTbl = Engine_Api::_()->getDbTable('videos', 'ynvideo');
        $videoTblName = $videoTbl->info('name');
        $playlistAssocTbl = Engine_Api::_()->getDbTable('playlistassoc', 'ynvideo');
        $playlistAssocTblName = $playlistAssocTbl->info('name');
        
        $select = $videoTbl->select()->setIntegrityCheck(false)
                ->from($videoTbl)
                ->join($playlistAssocTblName, "$playlistAssocTblName.video_id = $videoTblName.video_id")
                ->where("$playlistAssocTblName.playlist_id = ?", $this->getIdentity())
                ->where("$videoTblName.search = 1")
                ->where("$videoTblName.status = 1");
        
        return $videoTbl->fetchAll($select);
    }
    
    /**
     * Gets a proxy object for the comment handler
     *
     * @return Engine_ProxyObject
     * */
    public function comments() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
    }

    /**
     * Gets a proxy object for the like handler
     *
     * @return Engine_ProxyObject
     * */
    public function likes() {
        return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
    }
}