<?php
class Ynblog_Api_Core extends Core_Api_Abstract {

	public function getYnBlog() {
		$table = Engine_Api::_() -> getDbTable('modules', 'core');
		$select = $table -> select() -> where('name = ?', 'ynblog') -> where('enabled = 1');
		$result = $table -> fetchAll($select);
		if (count($result) > 0)
			return true;
		else
			return false;
	}

	/*----- Checking Existing Blog URL Function-----*/
	public function checkURL($link = '', $user_id = 0) {
		$table = Engine_Api::_() -> getDbTable('links', 'ynblog');
		$select = $table -> select() -> where('link_url like ?', '%' . $link . '%') -> where('user_id <> ?', $user_id);
		$result = $table -> fetchAll($select);
		if (count($result) > 0)
			return false;
		else
			return true;
	}
    
    public function getLink($id) 
    {
        $table = Engine_Api::_() -> getDbTable('links', 'ynblog');
        $select = $table -> select() -> where('link_id = ?', $id);
        $result = $table -> fetchRow($select);
        return $result;
    }

	/*----- Get Sub-word Phrase Function -----*/
	public function subPhrase($string, $length = 0) {
		if (strlen($string) <= $length)
			return $string;
		$pos = $length;
		for ($i = $length - 1; $i >= 0; $i--) {
			if ($string[$i] == " ") {
				$pos = $i + 1;
				break;
			}
		}
		return substr($string, 0, $pos) . "...";
	}

	/*----- Get Collection Of Dates Where A Given User Created A Blog Entry Function -----*/
	public function getArchiveList($user_id) {
		$table = Engine_Api::_() -> getDbtable('blogs', 'ynblog');
		$rName = $table -> info('name');

		$select = $table -> select() -> from($rName) -> where($rName . '.owner_id = ?', $user_id) -> where($rName . '.draft = ?', "0") -> where($rName . '.search = ?', "1") -> where($rName . '.is_approved = ?', "1");
		return $table -> fetchAll($select);
	}

	/*----- Check User Become Function -----*/
	public function checkUserBecome($user_id, $blog_id) {
		$table = Engine_Api::_() -> getDbTable('becomes', 'ynblog');
		$name = $table -> info('name');
		$select = $table -> select() -> where("$name.user_id = ?", $user_id) -> where("$name.blog_id = ?", $blog_id);

		$rows = $table -> fetchAll($select);
		return Count($rows) > 0 ? false : true;
	}

	/*----- Get User Tags Function -----*/
	public function getUserTags($user_id) {
		$t_table = Engine_Api::_() -> getDbtable('tags', 'core');
		$tName = $t_table -> info('name');
		$select = $t_table -> select() -> from($tName, array("$tName.*", "Count($tName.tag_id) as count")) -> joinLeft("engine4_core_tagmaps", "engine4_core_tagmaps.tag_id = $tName.tag_id", '') -> order("$tName.text") -> group("$tName.text") -> where("engine4_core_tagmaps.tagger_id = ?", $user_id) -> where("engine4_core_tagmaps.resource_type = ?", "blog");
		$this -> view -> userTags = $t_table -> fetchAll($select);
	}

	/*----- Override getItemTable Function-----*/
	public function getItemTable($type) {
		if ($type == 'blog_category') {
			return Engine_Loader::getInstance() -> load('Ynblog_Model_DbTable_Categories');
		} else if ($type == 'blog') {
			return Engine_Loader::getInstance() -> load('Ynblog_Model_DbTable_Blogs');
		} else {
			$class = Engine_Api::_() -> getItemTableClass($type);
			return Engine_Api::_() -> loadClass($class);
		}
	}

	/*----- Get Blog Paginater Function -----*/
	public function getBlogsPaginator($params = array()) {
		//Get blogs paginator
		$blog_select = $this -> getBlogsSelect($params);
		$paginator = Zend_Paginator::factory($blog_select);

		//Set current page
		if (!empty($params['page'])) {
			$paginator -> setCurrentPageNumber($params['page'], 1);
		}
		//Item per page
		$itemPerPage = (int)Engine_Api::_() -> getApi('settings', 'core') -> getSetting('blog.page', 10);
		$paginator -> setItemCountPerPage($itemPerPage);
		return $paginator;
	}

	/*----- Get Blog Selection Query Function -----*/
	public function getBlogsSelect($params = array()) {
		// Get blog table
		$blog_table = Engine_Api::_() -> getItemTable('blog');
		$blog_name = $blog_table -> info('name');

		// Get Tagmaps table
		$tags_table = Engine_Api::_() -> getDbtable('TagMaps', 'core');
		$tags_name = $tags_table -> info('name');

		//Select blog
		if (!isset($params['direction']))
			$params['direction'] = "DESC";

		//Order by filter
		if (isset($params['orderby']) && $params['orderby'] == 'displayname') {
			$select = $blog_table -> select() -> from($blog_name) -> setIntegrityCheck(false) -> join('engine4_users as u', "u.user_id = $blog_name.owner_id", '') -> order("u.displayname " . $params['direction']);
		} else {
			$select = $blog_table -> select() -> from($blog_name) -> order(!empty($params['orderby']) ? $blog_name . "." . $params['orderby'] . ' ' . $params['direction'] : $blog_name . '.blog_id ' . $params['direction']);
		}
		//User id filter
		if (!empty($params['user_id']) && is_numeric($params['user_id'])) {
			$select -> where($blog_name . '.owner_id = ?', $params['user_id']);
		}
		
		if (!empty($params['parent_type'])) {
			$select -> where($blog_name . '.parent_type = ?', $params['parent_type']);
		}
		
		if (!empty($params['parent_id']) && is_numeric($params['parent_id'])) {
			$select -> where($blog_name . '.parent_id = ?', $params['parent_id']);
		}

		// Show type filter
		if ((!empty($params['show']) && $params['show'] == 2) || (!empty($params['by_authors']) && !in_array('all', $params['by_authors']))) 
		{
			$str = (string)(is_array($params['users']) ? "'" . join("', '", $params['users']) . "'" : $params['users']);
			$select -> where($blog_name . '.owner_id in (?)', new Zend_Db_Expr($str));
		}
		
		if (!empty($params['categories'])) 
		{
			$str = (string)(is_array($params['categories']) ? "'" . join("', '", $params['categories']) . "'" : $params['categories']);
			$select -> where($blog_name . '.category_id in (?)', new Zend_Db_Expr($str));
		}
		//Tag filter
		if (!empty($params['tag'])) {
			$select -> setIntegrityCheck(false) -> joinLeft($tags_name, "$tags_name.resource_id = $blog_name.blog_id", "") -> where($tags_name . '.resource_type = ?', 'blog') -> where($tags_name . '.tag_id = ?', $params['tag']);
		}

		//Category filter
		if (!empty($params['category'])) {
			$select -> where($blog_name . '.category_id = ?', $params['category']);
		}

		//Rss filter
		if (!empty($params['blogRss'])) {
			$select -> where($blog_name . '.blog_id = ?', $params['blogRss']);
		}

		//Blog mode filter
		if (isset($params['draft'])) {
			$select -> where($blog_name . '.draft = ?', $params['draft']);
		}

		//Blog moderaton filer
		if (isset($params['is_approved'])) {
			$select -> where($blog_name . '.is_approved = ?', $params['is_approved']);
		}
		//Search filter
		if (!empty($params['search'])) {
			$select -> where($blog_name . ".title LIKE ? OR " . $blog_name . ".body LIKE ?", '%' . $params['search'] . '%');
		}

		//Title filter
		if (!empty($params['title'])) {
			$select -> where($blog_name . ".title LIKE ?", '%' . $params['title'] . '%');
		}

		//Start date filter
		if (!empty($params['start_date'])) {
			$select -> where($blog_name . ".creation_date > ?", date('Y-m-d', $params['start_date']));
		}
		if (!empty($params['date'])) {
			$date = $params['date'];
			$temp = explode(" ", $date);
			$date = explode("-", $temp[0]);
			$y = $date[0];
			$m = $date[1];
			$d = $date[2];
			$select -> where("YEAR(" . $blog_name . ".creation_date) = ?", $y);
			$select -> where("MONTH(" . $blog_name . ".creation_date) = ?", $m);
			$select -> where("DAY(" . $blog_name . ".creation_date) = ?", $d);
		}

		//End date filter
		if (!empty($params['end_date'])) {
			$select -> where($blog_name . ".creation_date < ?", date('Y-m-d', $params['end_date']));
		}

		//Search privacy filter
		if (!empty($params['visible'])) {
			$select -> where($blog_name . ".search = ?", $params['visible']);
		}

		//Feature blog filter
		if (isset($params['featured'])) {
			$select -> where("$blog_name.is_featured = ?", $params['featured']);
		}
		
		$deactiveIds = Engine_Api::_()->user()->getDeactiveUserIds();
		if (!empty($deactiveIds)) {
			$select -> where("$blog_name.owner_id NOT IN (?)", $deactiveIds);
		}

		//Owner in Admin Search
		if (!empty($params['owner'])) {
			$key = stripslashes($params['owner']);
			$select -> setIntegrityCheck(false) -> join('engine4_users as u1', "u1.user_id = $blog_name.owner_id", '') -> where("u1.displayname LIKE ?", "%{$key}%");
		}
		
		// Favorite
        if (isset($params['favorite_owner_id']) && $params['favorite_owner_id']) 
        {
            $select -> where("engine4_blog_favorites.user_id = ?", $params['favorite_owner_id']);
            $select->join('engine4_blog_favorites', 'engine4_blog_favorites.blog_id = engine4_blog_blogs.blog_id','');
        }

		//Limit option
		if (!empty($params['limit'])) {
			$select -> limit($params['limit']);
		}
		//Return query
		return $select;
	}

}
?>
