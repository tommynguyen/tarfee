<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Model_Video extends Core_Model_Item_Abstract
{

	// protected $_parent_type = 'user';
	protected $_owner_type = 'user';
	protected $_type = 'video';

	// protected $_parent_is_owner = true;
	
	function canAddRatings()
	{
		$viewer = Engine_Api::_() -> user() -> getViewer();
		return Engine_Api::_() -> authorization() -> getPermission($viewer, $this -> getType(),  'addratings');
	}
	
	public function getRating($total = false) {
		if($this -> parent_type == "user_playercard") {
			$ratingTable = Engine_Api::_() -> getDbTable('reviewRatings', 'ynvideo');
			
			//get list users rating of this video
			$userIds = $ratingTable -> getUserRatingByResource($this -> video_id);
			
			$overrallRatingTotal = 0;
			$userCount = 0;
			$overrallTotal = 0;
			//loop for reach user
			foreach($userIds as $user_id) {
				$userCount++;
				// get all ratings of this user for this video
				$params = array(
					'resource_id' => $this -> video_id,
					'user_id' => $user_id,
				);
				$ratings = $ratingTable -> getRatingsBy($params);
				$ratingCount = 0;
				$ratingTotal = 0;
				$overrall = 0;
				// loop for each rating then count the overall rating of user for this video
				foreach ($ratings as $rating)
				{
					$ratingTotal += $rating -> rating;
					$ratingCount++;
				}
				if($ratingCount != 0) {
					$overrall = round(($ratingTotal/$ratingCount), 2);
				} 
				else {
					$overrall = 0;
				}
				$overrallRatingTotal += $overrall;
			}
			if($userCount) 	{
				$overrallTotal = round(($overrallRatingTotal/$userCount), 2);
			}
			if($total) {
				return $overrallRatingTotal;
			}
			return $overrallTotal;
		} 
		else {
			return $this -> rating;
		}
	}
	
	public function getPopupHref($params = array())
	{
		$params = array_merge(array(
				'route' => 'video_popup_view',
				'reset' => true,
				'user_id' => $this -> owner_id,
				'video_id' => $this -> video_id,
				'slug' => $this -> getSlug(),
			), $params);
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}
	public function getHref($params = array())
	{
      	$isMobile = false;
        if(Engine_Api::_() -> hasModuleBootstrap('ynresponsive1')) {
      		$isMobile = Engine_Api::_()->getApi('mobile','ynresponsive1')->isMobile();
      	} 
		
		if($isMobile){
			$params = array_merge(array(
				'route' => 'video_mobile_view',
				'reset' => true,
				'user_id' => $this -> owner_id,
				'video_id' => $this -> video_id,
				'slug' => $this -> getSlug(),
			), $params);
		}
		else {
			$params = array_merge(array(
				'route' => 'video_popup_view',
				'reset' => true,
				'user_id' => $this -> owner_id,
				'video_id' => $this -> video_id,
				'slug' => $this -> getSlug(),
			), $params);
		}
		
		$route = $params['route'];
		$reset = $params['reset'];
		unset($params['route']);
		unset($params['reset']);
		return Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, $route, $reset);
	}

	public function getRichContent($view = false, $params = array())
	{
		$session = new Zend_Session_Namespace('mobile');
		$mobile = $session -> mobile;
		$count_video = 0;
		if (isset($session -> count))
			$count_video = ++$session -> count;
		$paramsForCompile = array_merge(array(
			'video_id' => $this -> video_id,
			'code' => $this -> code,
			'view' => $view,
			'mobile' => $mobile,
			'duration' => $this -> duration,
			'count_video' => $count_video
		), $params);
		if ($this -> type == Ynvideo_Plugin_Factory::getUploadedType())
		{
			$responsive_mobile = FALSE;
			if (defined('YNRESPONSIVE'))
			{
				$responsive_mobile = Engine_Api::_() -> ynresponsive1() -> isMobile();
			}
			if (!empty($this -> file1_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($session -> mobile || $responsive_mobile)
				{
					$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file1_id);
				}
				if ($storage_file)
				{
					$paramsForCompile['location1'] = $storage_file -> getHref();
					$paramsForCompile['location'] = '';
				}
			}
			else 
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $this -> file_id);
				if ($storage_file)
				{
					$paramsForCompile['location'] = $storage_file -> getHref();
					$paramsForCompile['location1'] = '';
				}
			}
		}
		else
		if ($this -> type == Ynvideo_Plugin_Factory::getVideoURLType())
		{
			$paramsForCompile['location'] = $this -> code;
		}
        $videoEmbedded = Ynvideo_Plugin_Factory::getPlugin((int)$this -> type) -> compileVideo($paramsForCompile);

		// $view == false means that this rich content is requested from the activity feed
		if ($view == false)
		{
			$video_duration = "";
			if ($this -> duration)
			{
				if ($this -> duration >= 3600)
				{
					$duration = gmdate("H:i:s", $this -> duration);
				}
				else
				{
					$duration = gmdate("i:s", $this -> duration);
				}
				$video_duration = "<span class='video_length'>" . $duration . "</span>";
			}

			// prepare the thumbnail
			$thumb = Zend_Registry::get('Zend_View') -> itemPhoto($this, 'thumb.large');
			if ($this -> photo_id)
			{
				$thumb = Zend_Registry::get('Zend_View') -> itemPhoto($this, 'thumb.large');
			}
			else
			{
				$thumb = '<img alt="" src="' . Zend_Registry::get('StaticBaseUrl') . 'application/modules/Video/externals/images/video.png">';
			}

			if (!$mobile)
			{
				$thumb = '<a id="video_thumb_' . $this -> video_id . $count_video . '" style="" href="javascript:void(0);" onclick="javascript:var myElement = $(this);myElement.style.display=\'none\';var next = myElement.getNext(); next.style.display=\'block\';">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
			}
			else
			{
				$thumb = '<a id="video_thumb_' . $this -> video_id . $count_video . '" class="video_thumb" href="javascript:void(0);">
                  <div class="video_thumb_wrapper">' . $video_duration . $thumb . '</div>
                  </a>';
			}

			// prepare title and description
			$title = "<a class='smoothbox' href='" . $this -> getPopupHref($params) . "'>". $this-> getTitle()."</a>";
			$tmpBody = strip_tags($this -> description);
			$description = "<div class='video_desc'>" . (Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 255) . '...' : $tmpBody) . "</div>";

			$class_html5 = "";
			if ($this -> type == Ynvideo_Plugin_Factory::getVideoURLType() || $this -> type == Ynvideo_Plugin_Factory::getUploadedType())
			{
				$class_html5 = 'html5_player';
			}
			$totalLike = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($this) -> getLikeCount();
          	$totalDislike = Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> getDislikeCount($this);
			
			$videoEmbedded = '<div class="video_info">' .$title . $description . '</div>'.$thumb . '<div id="video_object_' . $this -> video_id . '" class="video_object ' . $class_html5 . '">' . $videoEmbedded . '</div>';
			
			$view = Zend_Registry::get('Zend_View');  
			$videoEmbedded .= '<div class="tfvideo_statistics">
          	<span>'. $view->translate(array('%s like', '%s likes', $totalLike), $totalLike). '</span>
          	<span>'. $view->translate(array('%s dislike', '%s dislikes', $totalDislike), $totalDislike). '</span>
          	<span>'. $view->translate(array('%s comment', '%s comments', $this -> comment_count), $this -> comment_count).'</span>
      		</div>';
		}

		return $videoEmbedded;
	}

	public function getEmbedCode(array $options = null)
	{
		$options = array_merge(array(
			'height' => '525',
			'width' => '525',
		), (array)$options);

		$view = Zend_Registry::get('Zend_View');
		$url = 'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(
			'module' => 'ynvideo',
			'controller' => 'video',
			'action' => 'external',
			'video_id' => $this -> getIdentity(),
		), 'default', true) . '?format=frame';
		return '<iframe ' . 'src="' . $view -> escape($url) . '" ' . 'width="' . sprintf("%d", $options['width']) . '" ' . 'height="' . sprintf("%d", $options['width']) . '" ' . 'style="overflow:hidden;"' . '>' . '</iframe>';
	}

	public function getKeywords($separator = ' ')
	{
		$keywords = array();
		foreach ($this->tags()->getTagMaps() as $tagmap)
		{
			$tag = $tagmap -> getTag();
			$keywords[] = $tag -> getTitle();
		}

		if (null === $separator)
		{
			return $keywords;
		}

		return join($separator, $keywords);
	}

	// Interfaces

	/**
	 * Gets a proxy object for the comment handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function comments()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('comments', 'core'));
	}

	/**
	 * Gets a proxy object for the like handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function likes()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('likes', 'core'));
	}

	/**
	 * Gets a proxy object for the tags handler
	 *
	 * @return Engine_ProxyObject
	 * */
	public function tags()
	{
		return new Engine_ProxyObject($this, Engine_Api::_() -> getDbtable('tags', 'core'));
	}

	public function storeThumbnail($thumbnail, $type = 'small')
	{
		if (!empty($thumbnail))
		{
			if (is_string($thumbnail))
			{
				$pathInfo = @pathinfo($thumbnail);
				$parts = explode('?', preg_replace("/#!/", "?", $pathInfo['extension']));
				$ext = $parts[0];
				$thumbnail_parsed = @parse_url($thumbnail);

				if (@GetImageSize($thumbnail))
				{
					$valid_thumb = true;
				}
				else
				{
					$valid_thumb = false;
				}

				if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array(
					'jpg',
					'jpeg',
					'gif',
					'png'
				)))
				{
					$tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
					$thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;

					$src_fh = fopen($thumbnail, 'r');
					$tmp_fh = fopen($tmp_file, 'w');
					stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);

					$image = Engine_Image::factory();

					$width = 300;
					$height = 150;
					if ($type == 'large')
					{
						$width = 600;
						$height = 400;
					}
					$image -> open($tmp_file) -> resize($height, $width) -> write($thumb_file) -> destroy();

					try
					{
						$thumbFileRow = Engine_Api::_() -> storage() -> create($thumb_file, array(
							'parent_type' => $this -> getType(),
							'parent_id' => $this -> getIdentity(),
							'type' => ($type == 'large') ? 'thumb.large' : 'thumb.normal'
						));

						// Remove temp file
						@unlink($thumb_file);
						@unlink($tmp_file);

						if ($type == 'large')
						{
							$this -> large_photo_id = $thumbFileRow -> file_id;
						}
						else
						{
							$this -> photo_id = $thumbFileRow -> file_id;
						}
						$this -> save();
					}
					catch (Exception $e)
					{
						Zend_Registry::get('Zend_Log') -> log($e -> __toString(), Zend_Log::WARN);
					}
				}
			}
			else
			if (is_numeric($thumbnail))
			{
				if ($type == 'large')
				{
					$this -> large_photo_id = $thumbnail;
				}
				else
				{
					$this -> photo_id = $thumbnail;
				}
			}
		}
	}

	protected function _postInsert()
	{
		$table = Engine_Api::_() -> getDbTable('signatures', 'ynvideo');
		$select = $table -> select() -> where('user_id = ?', $this -> owner_id) -> limit(1);
		$row = $table -> fetchRow($select);

		if (null == $row)
		{
			$row = $table -> createRow();
			$row -> user_id = $this -> owner_id;
			$row -> video_count = 1;
		}
		else
		{
			$row -> video_count = new Zend_Db_Expr('video_count + 1');
		}
		$row -> save();
		parent::_postInsert();
	}

	protected function _delete()
	{
		// remove video from favorite table
		Engine_Api::_() -> getDbTable('favorites', 'ynvideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from favorite table
		Engine_Api::_() -> getDbTable('favorites', 'ynvideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from rating table
		Engine_Api::_() -> getDbTable('ratings', 'ynvideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// remove video from watchlater table
		Engine_Api::_() -> getDbTable('watchlaters', 'ynvideo') -> delete(array('video_id = ?' => $this -> getIdentity(), ));

		// update video count in signature table
		$signatureTbl = Engine_Api::_() -> getDbTable('signatures', 'ynvideo');
		$signature = $signatureTbl -> fetchRow($signatureTbl -> select() -> where('user_id = ?', $this -> owner_id));
		if ($signature)
		{
			$signature -> video_count = new Zend_Db_Expr('video_count - 1');
		}
		$signature -> save();

		// remove video from playlists
		$playlistAssocTbl = Engine_Api::_() -> getDbTable('playlistassoc', 'ynvideo');
		$playlistAssocs = $playlistAssocTbl -> fetchAll($playlistAssocTbl -> select() -> where('video_id = ?', $this -> getIdentity()));
		foreach ($playlistAssocs as $playlistAssoc)
		{
			$playlistAssoc -> delete();
		}

		parent::_delete();
	}

	protected function _postDelete()
	{
		parent::_postDelete();

		//         $signatureItem = Engine_Api::_()->getItem('ynvideo_signature', $this->owner_id);
		//         if ($signatureItem) {
		//             if ($signatureItem->video_count > 0) {
		//                 $signatureItem->video_count = new Zend_Db_Expr('video_count - 1');
		//                 $signatureItem->save();
		//             }
		//         }
	}

	/**
	 * Gets a url to the current photo representing this item. Return null if none
	 * set
	 *
	 * @param string The photo type (null -> main, thumb, icon, etc);
	 * @return string The photo url
	 */
	public function getPhotoUrl($type = null)
	{
		$field = 'photo_id';
		if ($type == 'thumb.large')
		{
			$field = 'large_photo_id';
		}
		if (empty($this -> $field))
		{
			return null;
		}

		$file = Engine_Api::_() -> getItemTable('storage_file') -> getFile($this -> $field, $type);
		if (!$file)
		{
			return null;
		}

		return $file -> map();
	}
	
	public function getParent() {
		try{
			return Engine_Api::_() -> getItem($this -> parent_type, $this -> parent_id);
     	} 
     	catch( Exception $e ) {
     		return null;
        }
	}
	
	public function getSportId() {
		$parent = $this->getParent();
		if ($parent && method_exists($parent, 'getSportId')) {
			return $parent->getSportId();
		}
		$owner = $this->getOwner();
		if ($owner && method_exists($owner, 'getSportId')) {
			return $owner->getSportId();
		}
		return null;
	}
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new User_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'video',
			'parent_id' => $this -> getIdentity()
		);

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (main)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(720, 720) -> write($path . '/m_' . $name) -> destroy();

		// Resize image (profile)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(200, 400) -> write($path . '/p_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/m_' . $name, $params);
		$iProfile = $storage -> create($path . '/p_' . $name, $params);
		$iMain -> bridge($iProfile, 'thumb.normal');

		// Remove temp files
		@unlink($path . '/p_' . $name);
		@unlink($path . '/m_' . $name);

		// Update row
		$this -> modified_date = date('Y-m-d H:i:s');
		$this -> photo_id = $iMain -> file_id;
		$this -> large_photo_id = $iMain -> file_id;
		$this -> save();

		return $this;
	}
	public function hasFavorite()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$userId = $viewer -> getIdentity();
		$videoId = $this -> getIdentity();
		$favoriteTbl = Engine_Api::_()->getDbTable('favorites', 'ynvideo');
        $row = $favoriteTbl->fetchRow(array("video_id = $videoId", "user_id = $userId"));
		return $row;
	}
}
