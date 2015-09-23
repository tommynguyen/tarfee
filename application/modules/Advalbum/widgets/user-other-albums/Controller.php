<?php

class Advalbum_Widget_UserOtherAlbumsController extends Engine_Content_Widget_Abstract
{
    public function indexAction ()
    {
    	$params = $this -> _getAllParams();
    	if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
    	$settings = Engine_Api::_() -> getApi('settings', 'core');

    	$p = Zend_Controller_Front::getInstance()->getRequest()->getParams();
    	$albumId = $p['album_id'];
		if (!$albumId)
		{
			$this->setNoRender();
		}
		$this->view->album = $album = Engine_Api::_()->getItem("advalbum_album", $albumId);
		
    	// other albums
    	if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;
    	$otherTable = Engine_Api::_() -> getDbTable('albums', 'advalbum');
    	$otherTableName = $otherTable -> info('name');
    	$otherSelect = $otherTable 
    		-> select() 
	    	-> from($otherTableName) 
	    	-> where("owner_id  = ?", $album -> owner_id) 
	    	-> where("album_id  <> ?", $album -> album_id) 
	    	-> where("search = ?", "1") 
	    	-> order("view_count DESC") 
	    	-> limit($limit);
    	
    	$otherAlbums = $otherTable -> fetchAll($otherSelect);
    	$this -> view -> otherAlbums = $otherAlbums;
    }
}