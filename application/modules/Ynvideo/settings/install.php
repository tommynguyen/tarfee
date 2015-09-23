<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Installer extends Engine_Package_Installer_Module {

	public function onInstall() {
		$this -> _checkFfmpegPath();
		$this -> _addUserProfileContent();
		$this -> _addVideoIndexPage();
		$this -> _addVideoCreatePage();
		$this -> _addMyVideosPage();
		$this -> _addVideoListingPage();
		$this -> _addVideoFavoritePage();
		$this -> _addVideoPlaylistsPage();
		$this -> _addVideoPlaylistDetailsPage();
		$this -> _addVideoWatchlaterPage();
		$this -> _addVideoDetailPage();
		$this -> _editVideoTbl();

		parent::onInstall();

		//Disable SE Video
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'video';");

		$this -> _updateActionTypes('ynvideo');
		$this -> _addMenuItems();
		$this -> _addNonCategory();
		$this -> _synchronizeStatisticData();
	}

	protected function _checkFfmpegPath() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check ffmpeg path for correctness
		if (function_exists('exec') && function_exists('shell_exec')) {
			// Api is not available
			//$ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
			$ffmpeg_path = $db -> select() -> from('engine4_core_settings', 'value') -> where('name = ?', 'ynvideo.ffmpeg.path') -> limit(1) -> query() -> fetchColumn(0);

			$output = null;
			$return = null;
			if (!empty($ffmpeg_path)) {
				exec($ffmpeg_path . ' -version', $output, $return);
			}
			// Try to auto-guess ffmpeg path if it is not set correctly
			$ffmpeg_path_original = $ffmpeg_path;
			if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
				$ffmpeg_path = null;
				// Windows
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					// @todo
				}
				// Not windows
				else {
					$output = null;
					$return = null;
					@exec('which ffmpeg', $output, $return);
					if (0 == $return) {
						$ffmpeg_path = array_shift($output);
						$output = null;
						$return = null;
						exec($ffmpeg_path . ' -version', $output, $return);
						if (0 == $return) {
							$ffmpeg_path = null;
						}
					}
				}
			}
			if ($ffmpeg_path != $ffmpeg_path_original) {
				$count = $db -> update('engine4_core_settings', array('value' => $ffmpeg_path, ), array('name = ?' => 'ynvideo.ffmpeg.path', ));
				if ($count === 0) {
					try {
						$db -> insert('engine4_core_settings', array('value' => $ffmpeg_path, 'name' => 'ynvideo.ffmpeg.path', ));
					} catch (Exception $e) {

					}
				}
			}
		}
	}

	protected function _insertWidgetToProfileContent($page_id, $name, $params, $order) {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'container') -> limit(1);
		$container_id = $select -> query() -> fetchObject() -> content_id;

		// middle_id (will always be there)
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('parent_content_id = ?', $container_id) -> where('type = ?', 'container') -> where('name = ?', 'middle') -> limit(1);
		$middle_id = $select -> query() -> fetchObject() -> content_id;

		// tab_id (tab container) may not always be there
		$select -> reset('where') -> where('type = ?', 'widget') -> where('name = ?', 'core.container-tabs') -> where('page_id = ?', $page_id) -> limit(1);
		$tab_id = $select -> query() -> fetchObject();
		if ($tab_id && @$tab_id -> content_id) {
			$tab_id = $tab_id -> content_id;
		} else {
			$tab_id = null;
		}

		// tab on profile
		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => $name, 'parent_content_id' => ($tab_id ? $tab_id : $middle_id), 'order' => $order, 'params' => $params, ));
	}

	protected function _addUserProfileContent() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// profile page
		$select -> from('engine4_core_pages') -> where('name = ?', 'user_profile_index') -> limit(1);
		$page_id = $select -> query() -> fetchObject() -> page_id;

		// video.profile-videos
		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynvideo.profile-videos');
		$infoProfileVideos = $select -> query() -> fetch();

		if (empty($infoProfileVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynvideo.profile-videos', '{"title":"Videos","titleCount":true}', 12);
		}

		// check if the profile video widget of SE video existed or not,
		// it it is existed, then delete it from the user profie page
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'video.profile-videos');
		$infoSEProfileVideos = $select -> query() -> fetch();

		if (!empty($infoSEProfileVideos)) {
			$db -> delete('engine4_core_content', array("content_id = {$infoSEProfileVideos['content_id']}"));
		}

		// video.profile-favorite-videos
		// Check if it's already been placed
		$select -> reset('where') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynvideo.profile-favorite-videos');
		$infoProfileFavoriteVideos = $select -> query() -> fetch();
		if (empty($infoProfileFavoriteVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynvideo.profile-favorite-videos', '{"title":"Favorite Videos","titleCount":true}', 13);
		}

		// video.profile-video-playlists
		// Check if it's already been placed
		$select -> reset('where') -> where('page_id = ?', $page_id) -> where('type = ?', 'widget') -> where('name = ?', 'ynvideo.profile-video-playlists');
		$infoProfileFavoriteVideos = $select -> query() -> fetch();
		if (empty($infoProfileFavoriteVideos)) {
			$this -> _insertWidgetToProfileContent($page_id, 'ynvideo.profile-video-playlists', '{"title":"Video Playlists","titleCount":true}', 14);
		}
	}

	public function onEnable() {
		parent::onEnable();

		// Disable SE Video module
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 0 WHERE `engine4_core_modules`.`name` = 'video';");

		$this -> _addNonCategory();
		$this -> _synchronizeStatisticData();
		$this -> _updateActionTypes('ynvideo');
		$this -> _addMenuItems();
	}

	private function _addMenuItems() {
		$db = $this -> getDb();
		$select1 = new Zend_Db_Select($db);

		$select1 -> from('engine4_core_menuitems') -> where('name = ?', 'core_main_ynvideo') -> limit(1);
		$info = $select1 -> query() -> fetch();
		if (empty($info)) {
			$db -> insert('engine4_core_menuitems', array('name' => 'core_main_ynvideo', 'module' => 'ynvideo', 'label' => 'Videos', 'params' => '{"route":"video_general"}', 'menu' => 'core_main'));
		}

		$select2 = new Zend_Db_Select($db);
		$select2 -> from('engine4_core_menuitems') -> where('name = ?', 'core_admin_main_plugins_ynvideo') -> limit(1);
		$info = $select2 -> query() -> fetch();
		if (empty($info)) {
			$db -> insert('engine4_core_menuitems', array('name' => 'core_admin_main_plugins_ynvideo', 'module' => 'ynvideo', 'label' => 'Videos', 'params' => '{"route":"admin_default","module":"ynvideo","controller":"manage"}', 'menu' => 'core_admin_main_plugins'));
		}
	}

	private function _updateActionTypes($module) {
		$db = $this -> getDb();

		$db -> query("UPDATE `engine4_activity_actiontypes` SET `engine4_activity_actiontypes`.`module`= '$module' WHERE `engine4_activity_actiontypes`.`type` = 'video_new';");
		$db -> query("UPDATE `engine4_activity_actiontypes` SET `engine4_activity_actiontypes`.`module`= '$module' WHERE `engine4_activity_actiontypes`.`type` = 'comment_video';");
	}

	private function _addNonCategory() {
		$db = $this -> getDb();

		$select = new Zend_Db_Select($db);
		$select -> from('engine4_video_categories') -> where('category_id = 0') -> limit(1);
		$info = $select -> query() -> fetch();
		if (empty($info)) {
			$db -> insert('engine4_video_categories', array('parent_id' => '0', 'ordering' => '0', 'user_id' => '1', 'category_name' => 'Non-category', ));
			$category_id = $db -> lastInsertId();
			$db -> update('engine4_video_categories', array('category_id' => '0'), array("category_id = $category_id"));
		}
	}

	private function _removeNonCategory() {
		$db = $this -> getDb();
		$db -> delete('engine4_video_categories', array('category_id = 0'));
	}

	public function onDisable() {
		parent::onDisable();

		// Enable video SE module
		$db = $this -> getDb();
		$db -> query("UPDATE `engine4_core_modules` SET `enabled`= 1 WHERE `engine4_core_modules`.`name` = 'video';");

		$this -> _removeNonCategory();
		$this -> _updateActionTypes('video');
	}

	protected function _synchronizeStatisticData() {
		$db = $this -> getDb();

		// update favorite_count for videos
		$db -> query("UPDATE `engine4_video_videos` AS `videos` " . "SET `favorite_count` = " . "(SELECT COUNT(*) from `engine4_video_favorites` AS `favorites` WHERE `favorites`.video_id = `videos`.video_id)");

		// update video_count for playlists
		$db -> query("UPDATE `engine4_video_playlists` AS `playlists` " . "SET `video_count` = " . "(SELECT COUNT(*) from `engine4_video_playlistassoc` AS `playlistassoc` WHERE `playlists`.playlist_id = `playlistassoc`.playlist_id)");

		// remove all statistic data about a video
		$db -> query("DELETE FROM `engine4_video_signatures`");

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> distinct() -> from('engine4_video_videos', 'owner_id');
		foreach ($select->query()->fetchAll() as $userId) {
			$selectCountVideo = new Zend_Db_Select($db);
			$selectCountVideo -> from('engine4_video_videos', 'count(*)') -> where('owner_id = ?', $userId['owner_id']);
			$query = $selectCountVideo -> query();
			$videoCount = $query -> fetchColumn();
			$db -> insert('engine4_video_signatures', array('user_id' => $userId['owner_id'], 'creation_date' => date('Y-m-d H:i:s'), 'modified_date' => date('Y-m-d H:i:s'), 'video_count' => $videoCount));
		}
	}

	protected function _addVideoIndexPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_index_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_index_index', 'displayname' => 'Advanced Video Home Page', 'title' => 'Advanced Video', 'description' => 'This is the home page for the advanced video.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '["[]"]', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, 'params' => '["[]"]', ));
			$middle_top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-menu', 'parent_content_id' => $middle_top_id, 'order' => 1, 'params' => '["[]"]', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '["[]"]', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : left, middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 3, 'params' => '["[]"]', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-featured-videos', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '{"title":"","slidingDuration":"5000","nomobile":"0","name":"ynvideo.list-featured-videos","slideWidth":"740","slideHeight":"400"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.container-tabs', 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '{"max":"6"}', ));
			$container_tab_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '["[]"]', ));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// widgets in the container tab
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-recent-videos', 'parent_content_id' => $container_tab_id, 'order' => 1, 'params' => '{"title":"Latest Videos","recentType":"creation","nomobile":"0","itemCountPerPage":"12","name":"ynvideo.list-recent-videos"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-popular-videos', 'parent_content_id' => $container_tab_id, 'order' => 2, 'params' => '{"title":"Most Viewed","popularType":"view","nomobile":"0","itemCountPerPage":"4","name":"ynvideo.list-popular-videos","viewType":"big"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-liked-videos', 'parent_content_id' => $container_tab_id, 'order' => 3, 'params' => '{"title":"Most Liked","numberOfVideos":"5","nomobile":"0","name":"ynvideo.list-liked-videos","viewType":"big"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-popular-videos', 'parent_content_id' => $container_tab_id, 'order' => 4, 'params' => '{"title":"Most Rated","popularType":"rating","nomobile":"0","itemCountPerPage":"4","name":"ynvideo.list-popular-videos","viewType":"big"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-popular-videos', 'parent_content_id' => $container_tab_id, 'order' => 5, 'params' => '{"title":"Most Commented","popularType":"comment","nomobile":"0","itemCountPerPage":"4","name":"ynvideo.list-popular-videos","viewType":"big"}', ));

			// right column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-search', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":""}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-categories', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"Categories"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-top-members', 'parent_content_id' => $right_id, 'order' => 3, 'params' => '{"title":"Top Members","name":"ynvideo.list-top-members"}', ));
		}
	}

	protected function _addVideoCreatePage() {
		$db = $this -> getDb();

		// create page
		$page_id = $db -> select() -> from('engine4_core_pages', 'page_id') -> where('name = ?', 'ynvideo_index_create') -> limit(1) -> query() -> fetchColumn();

		// insert if it doesn't exist yet
		if (!$page_id) {
			// Insert page
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_index_create', 'displayname' => 'Advanced Video Create Page', 'title' => 'Video Create', 'description' => 'This page allows video to be added.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId();

			// Insert top
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'top', 'page_id' => $page_id, 'order' => 1, ));
			$top_id = $db -> lastInsertId();

			// Insert main
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'main', 'page_id' => $page_id, 'order' => 2, ));
			$main_id = $db -> lastInsertId();

			// Insert top-middle
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $top_id, ));
			$top_middle_id = $db -> lastInsertId();

			// Insert main-middle
			$db -> insert('engine4_core_content', array('type' => 'container', 'name' => 'middle', 'page_id' => $page_id, 'parent_content_id' => $main_id, 'order' => 2, ));
			$main_middle_id = $db -> lastInsertId();

			// Insert menu
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'ynvideo.browse-menu', 'page_id' => $page_id, 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			// Insert content
			$db -> insert('engine4_core_content', array('type' => 'widget', 'name' => 'core.content', 'page_id' => $page_id, 'parent_content_id' => $main_middle_id, 'order' => 1, ));
		}
	}

	private function _addContentTopAndContent($page_id, $widgetContent = 'core.content') {
		$db = $this -> getDb();

		// top
		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, ));
		$top_id = $db -> lastInsertId('engine4_core_content');

		// top contents
		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, ));
		$top_middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-menu', 'parent_content_id' => $top_middle_id, 'order' => 1, ));

		// main
		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, ));
		$main_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $main_id, 'order' => 1, ));
		$main_right_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-search', 'parent_content_id' => $main_right_id, 'order' => 1, 'params' => '{"title":""}', ));

		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $main_id, 'order' => 2, ));
		$main_middle_id = $db -> lastInsertId('engine4_core_content');

		$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => $widgetContent, 'parent_content_id' => $main_middle_id, 'order' => 1, ));
	}

	protected function _addMyVideosPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_index_manage') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_index_manage', 'displayname' => 'Advanced Video My Videos Page', 'title' => 'My Videos', 'description' => 'This is the view page for videos posted by the current user.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynvideo.list-manage-videos');
		}
	}

	protected function _addVideoListingPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_index_list') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_index_list', 'displayname' => 'Advanced Video Listing Page', 'title' => 'Listing Videos', 'description' => 'This is the advanced video listing page.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// top
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			// top contents
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, ));
			$top_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-menu', 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			// main
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, ));
			$main_id = $db -> lastInsertId('engine4_core_content');

			// main middle
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $main_id, 'order' => 2, 'params' => '["[]"]', ));
			$main_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-videos', 'parent_content_id' => $main_middle_id, 'order' => 1, ));

			// main right
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $main_id, 'order' => 2, 'params' => '["[]"]', ));
			$main_right_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-search', 'parent_content_id' => $main_right_id, 'order' => 1, 'params' => '{"title":""}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.list-categories', 'parent_content_id' => $main_right_id, 'order' => 2, 'params' => '{"title":"Categories"}', ));
		}
	}

	protected function _addVideoFavoritePage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_favorite_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_favorite_index', 'displayname' => 'Advanced Video Favorite Page', 'title' => 'View Favorite Videos', 'description' => 'This is the view page for favorite videos.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynvideo.list-my-favorite-videos');
		}
	}

	protected function _addVideoPlaylistsPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_playlist_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_playlist_index', 'displayname' => 'Advanced Video Video Playlists Page', 'title' => 'View Video Playlists', 'description' => 'This is the view page for video playlists.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynvideo.list-my-playlists');
		}
	}

	protected function _addVideoPlaylistDetailsPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_playlist_view') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_playlist_view', 'displayname' => 'Advanced Video View Playlist Detail Page', 'title' => 'View Video Playlist Detail', 'description' => 'This is the view page for the video playlist detail.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// top
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			// top contents
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, ));
			$top_middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-menu', 'parent_content_id' => $top_middle_id, 'order' => 1, ));

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '["[]"]', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 2, 'params' => '["[]"]', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '["[]"]', ));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// middle column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.content', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '["[]"]', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.comments', 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '["[]"]', ));

			// right column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-search', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":""}', ));
		}
	}

	protected function _addVideoWatchlaterPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_watch-later_index') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_watch-later_index', 'displayname' => 'Advanced Video Watch Later Page', 'title' => 'View Watch Later Videos', 'description' => 'This is the view page for watch later videos.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			$this -> _addContentTopAndContent($page_id, 'ynvideo.list-my-watch-later-videos');
		}
	}

	protected function _addVideoDetailPage() {
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);

		// Check if it's already been placed
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_pages') -> where('name = ?', 'ynvideo_index_view') -> limit(1);
		$info = $select -> query() -> fetch();

		if (empty($info)) {
			$db -> insert('engine4_core_pages', array('name' => 'ynvideo_index_view', 'displayname' => 'Advanced Video View Page', 'title' => 'View Video', 'description' => 'This is the view page for a video.', 'custom' => 0, ));
			$page_id = $db -> lastInsertId('engine4_core_pages');

			// containers
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'top', 'parent_content_id' => null, 'order' => 1, 'params' => '["[]"]', ));
			$top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $top_id, 'order' => 1, 'params' => '["[]"]', ));
			$middle_top_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.browse-menu', 'parent_content_id' => $middle_top_id, 'order' => 1, 'params' => '["[]"]', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'main', 'parent_content_id' => null, 'order' => 2, 'params' => '["[]"]', ));
			$container_id = $db -> lastInsertId('engine4_core_content');

			// insert columns : middle and right
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'middle', 'parent_content_id' => $container_id, 'order' => 2, 'params' => '["[]"]', ));
			$middle_id = $db -> lastInsertId('engine4_core_content');

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'container', 'name' => 'right', 'parent_content_id' => $container_id, 'order' => 1, 'params' => '["[]"]', ));
			$right_id = $db -> lastInsertId('engine4_core_content');

			// middle column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.content', 'parent_content_id' => $middle_id, 'order' => 1, 'params' => '["[]"]', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'core.comments', 'parent_content_id' => $middle_id, 'order' => 2, 'params' => '["[]"]', ));

			// right column content
			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.show-same-categories', 'parent_content_id' => $right_id, 'order' => 1, 'params' => '{"title":"Related Videos"}', ));

			$db -> insert('engine4_core_content', array('page_id' => $page_id, 'type' => 'widget', 'name' => 'ynvideo.show-same-poster', 'parent_content_id' => $right_id, 'order' => 2, 'params' => '{"title":"From the same Member"}', ));
		}
	}

	protected function _editVideoTbl() {
		$sql = "ALTER TABLE `engine4_video_videos` ADD COLUMN `file1_id` INT(11) DEFAULT '0' NULL AFTER `file_id`";
		$db = $this -> getDb();
		try {
			$info = $db -> describeTable('engine4_video_videos');
			if ($info && !isset($info['file1_id'])) {
				try {
					$db -> query($sql);
				} catch( Exception $e ) {
				}
			}
		} catch (Exception $e) {
		}
	}

}
