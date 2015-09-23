<?php
class Advalbum_Widget_FeaturedAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$params = $this -> _getAllParams();
		
		if(empty($params['title']))
		{
			$this -> view-> no_title = "no_title";
		}
		
		if ($this -> _getParam('number') != '' && $this -> _getParam('number') >= 0)
		{
			$limit = $this -> _getParam('number');
		}
		else
			$limit = 4;
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
		$mode_list = $mode_grid = $mode_pinterest = 1;
		$mode_enabled = array();
		$view_mode = 'list';

		if (isset($params['mode_list']))
		{
			$mode_list = $params['mode_list'];
		}
		if ($mode_list)
		{
			$mode_enabled[] = 'list';
		}
		if (isset($params['mode_grid']))
		{
			$mode_grid = $params['mode_grid'];
		}
		if ($mode_grid)
		{
			$mode_enabled[] = 'grid';
		}
		if (isset($params['mode_pinterest']))
		{
			$mode_pinterest = $params['mode_pinterest'];
		}
		if ($mode_pinterest)
		{
			$mode_enabled[] = 'pinterest';
		}
		if (isset($params['view_mode']))
		{
			$view_mode = $params['view_mode'];
		}
		if ($mode_enabled && !in_array($view_mode, $mode_enabled))
		{
			$view_mode = $mode_enabled[0];
		}

		$this -> view -> mode_enabled = $mode_enabled;

		$class_mode = "ynalbum-list-view";
		switch ($view_mode)
		{
			case 'grid' :
				$class_mode = "ynalbum-grid-view";
				break;
			case 'pinterest' :
				$class_mode = "ynalbum-pinterest-view";
				break;
			default :
				$class_mode = "ynalbum-list-view";
				break;
		}
		$this -> view -> view_mode = $view_mode;
		$this -> view -> class_mode = $class_mode;

		$this -> view -> limit = $limit;
		$table = Engine_Api::_() -> getDbtable('albums', 'advalbum');
		$Name = $table -> info('name');
		$select = $table -> select() -> from($Name) -> where("search = ?", "1") -> where('featured = ?', 1);

		$arr_albums = $table -> getAllowedAlbums($select, $limit);
		if (count($arr_albums) <= 0)
		{
			$this -> setNoRender();
		}
		$this -> view -> arr_albums = $arr_albums;
	}

}
