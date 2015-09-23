<?php

class Advalbum_Widget_TopRecentAlbumsController extends Engine_Content_Widget_Abstract
{

    public function indexAction ()
    {	$params = $this -> _getAllParams();
	
    	$limit = 6;
    	$session = new Zend_Session_Namespace('mobile');
		if (!$session -> mobile)
		{
			return $this -> setNoRender();
		}
		if($session -> mobile)
		{
        	$limit = 12;
		}
        $this->getElement()->removeDecorator('Title');
        $this->getElement()->removeDecorator('Description');

        $atable = Engine_Api::_()->getDbtable('albums', 'advalbum');
        $aName = $atable->info('name');
        $select = $atable->select()
            ->from($aName)
            ->setIntegrityCheck(false);

        $select->order("like_count DESC")
               ->where("search = ?", "1");

        $top_albums = $atable->getAllowedAlbums($select, $limit);

        $this->view->top_albums_count = $top_albums_count = count($top_albums);

        $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
        $Name = $table->info('name');
        $select = $table->select()
            ->from($Name)
            ->where("search = ?", "1")
            ->order("creation_date DESC");

        $recent_albums = $table->getAllowedAlbums($select, $limit);

        $this->view->recent_albums_count = $recent_albums_count = count($recent_albums);

        $css = 'top_recent_album_list_half';
        if ($recent_albums_count > 3 || $top_albums_count > 3) {
            $css = 'top_recent_album_list';
        }

        $album_listing_id = 'div_top_albums';
        $no_albums_message = $this->view->translate('There are no top albums');
        $this->view->html_full_top_albums = $this->view->partial(
                Advalbum_Api_Core::partialViewFullPath('_albumlistsmall.tpl'),
                array(
                        'arr_albums' => $top_albums,
                        'album_listing_id' => $album_listing_id,
                        'no_albums_message' => $no_albums_message,
                        'css' => $css
                ));

        $album_listing_id = 'div_recent_albums';
        $no_albums_message = $this->view->translate('There are no recent albums');
        $this->view->html_full_recent_albums = $this->view->partial(
                Advalbum_Api_Core::partialViewFullPath('_albumlistsmall.tpl'),
                array(
                        'arr_albums' => $recent_albums,
                        'album_listing_id' => $album_listing_id,
                        'no_albums_message' => $no_albums_message,
                        'css' => $css
                ));
    }
}