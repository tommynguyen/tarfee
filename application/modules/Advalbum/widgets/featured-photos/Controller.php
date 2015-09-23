<?php
class Advalbum_Widget_FeaturedPhotosController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$params = $this -> _getAllParams();
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
		if ($this -> _getParam('max') != '' && $this -> _getParam('max') >= 0)
		{
			$max = $this -> _getParam('max');
		}
		else {
			$max = 8;
		}
		$table = Engine_Api::_() -> getDbtable('features', 'advalbum');
		$Name = $table -> info('name');
		$tableP = Engine_Api::_() -> getDbtable('photos', 'advalbum');
		$NameP = $tableP -> info('name');
		$select = $table -> select() -> from($Name);
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$featured_photos_max = $max;
		$select -> join($NameP, "$NameP.photo_id = $Name.photo_id", '') -> join("engine4_users", "engine4_users.user_id = $NameP.owner_id", '') -> where("photo_good  = ?", "1") -> order(" RAND() ") -> limit($featured_photos_max * 3);

		$photos_list = $table -> fetchAll($select);
		if (count($photos_list) <= 0)
		{
			$this -> setNoRender();
		}
		$session = new Zend_Session_Namespace('mobile');
		$arr_photos = array();
	
		$photo_ids = ",";
		$photo_count = 0;
		foreach ($photos_list as $item)
		{
			$photo = Engine_Api::_() -> getItem('advalbum_photo', $item -> photo_id);
			if (!$photo)
			{
				continue;
			}
			$album = $photo -> getParent();
			if ($album -> authorization() -> isAllowed($viewer, 'view') && $album -> type != "profile" && $album -> search = 1)
			{
				$arr_photos[] = $photo;
				$photo_count++;
				$photo_ids .= "{$photo->getIdentity()},";
				if ($photo_count >= $featured_photos_max)
					break;
			}
		}
		$photo_ids = trim($photo_ids, " ,");
		if ($session -> mobile)
		{
			$this -> view -> html_mobile_slideshow = $this -> view -> partial('_m_slideshow.tpl', 'advalbum', array('photo_list' => $arr_photos));
		}
		else
		{
			$background_image = $this -> _getParam('background_image', null);
			if (!$background_image)
			{
				$background_image = $this -> view -> layout() -> staticUrl . 'application/modules/Advalbum/externals/images/slideshow_bg.jpg';
			}
			$this -> view -> html_ynresponsive_slideshow = $this -> view -> partial('_responsive_slideshow.tpl', 'advalbum', array(
				'items' => $arr_photos,
				'show_title' => false,
				'show_description' => false,
				'background_image' => $background_image,
				'height' => $this -> _getParam('height', 400),
				'speed' => $this -> _getParam('speed', 3) * 1000,
				'slider_id' => '_' . uniqid()
			));
		}
	}

}
