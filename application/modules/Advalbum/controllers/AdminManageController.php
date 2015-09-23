<?php
class Advalbum_AdminManageController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_manage');

		$this -> view -> form = $form = new Advalbum_Form_Admin_AlbumSearch();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = array_filter($params);

		if (empty($params['orderby']))
			$params['orderby'] = 'album_id';
		if (empty($params['direction']))
			$params['direction'] = 'DESC';

		$this -> view -> formValues = $params;

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> advalbum() -> getAlbumPaginator($params);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);

		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			if (count($values) > 0)
			{
				foreach ($values as $key => $value)
				{
					if ($key == 'delete_' . $value)
					{
						$album = Engine_Api::_() -> getItem('advalbum_album', $value);
						$album -> delete();
					}
				}
			}
		}
	}

	public function photosAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_photos');

		$form = new Advalbum_Form_Admin_PhotoSearch();

		$this -> view -> form = $form;
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = array_filter($params);

		if (empty($params['orderby']))
			$params['orderby'] = 'photo_id';
		if (empty($params['direction']))
			$params['direction'] = 'DESC';

		$this -> view -> formValues = $params;

		$page = $this -> _getParam('page', 1);
		$paginator = Engine_Api::_() -> advalbum() -> getPhotoPaginator($params);
		$paginator -> setItemCountPerPage(25);
		$paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator = $paginator;
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value)
			{
				if ($key == 'delete_' . $value)
				{
					$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
					$fName = $ftable -> info('name');
					$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $value);
					$features = $ftable -> fetchAll($select);
					if (count($features) > 0)
					{
						$feature_id = $features[0] -> feature_id;
						$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
						$feature -> delete();
					}
					$photo = Engine_Api::_() -> getItem('advalbum_photo', $value);
					$photo -> delete();
				}
			}
		}
	}

	public function featuredAction()
	{
		$photo_id = $this -> _getParam('photo_id');
		$photo_good = $this -> _getParam('good');
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
		$fName = $ftable -> info('name');
		$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $photo_id);
		$features = $ftable -> fetchAll($select);
		if (count($features) > 0)
		{
			$feature_id = $features[0] -> feature_id;
			$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
			$feature -> photo_good = $photo_good;
			$feature -> save();
		}
		else
		{
			$feature = Engine_Api::_() -> getDbtable('features', 'advalbum') -> createRow();
			$feature -> photo_id = $photo_id;
			$feature -> photo_good = $photo_good;
			$feature -> save();
		}
		$db -> commit();
	}

	public function deleteAction()
	{
		$id = $this -> _getParam('id');
		$this -> view -> photo_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
				$fName = $ftable -> info('name');
				$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $id);
				$features = $ftable -> fetchAll($select);
				if (count($features) > 0)
				{
					$feature_id = $features[0] -> feature_id;
					$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
					$feature -> delete();
				}
				$blog = Engine_Api::_() -> getItem('advalbum_photo', $id);
				// delete the blog entry into the database
				$blog -> delete();
				$db -> commit();
			}

			catch ( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-manage/delete.tpl');
	}

	public function deleteselectedAction()
	{
		$this -> view -> ids = $ids = $this -> _getParam('ids', null);
		$confirm = $this -> _getParam('confirm', false);
		$this -> view -> count = count(explode(",", $ids));

		// Save values
		if ($this -> getRequest() -> isPost() && $confirm == true)
		{
			$ids_array = explode(",", $ids);
			foreach ($ids_array as $id)
			{
				$photo = Engine_Api::_() -> getItem('advalbum_photo', $id);
				if ($photo)
				{
					$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
					$fName = $ftable -> info('name');
					$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $photo -> photo_id);
					$features = $ftable -> fetchAll($select);
					if (count($features) > 0)
					{
						$feature_id = $features[0] -> feature_id;
						$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
						$feature -> delete();
					}
					$photo -> delete();
				}
			}

			$this -> _helper -> redirector -> gotoRoute(array('action' => 'photos'));
		}
	}

	public function deleteAlbumAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!$this -> _helper -> requireSubject('advalbum_album') -> isValid())
			return;
		if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
			return;

		$viewer = Engine_Api::_() -> user() -> getViewer();
		$album = Engine_Api::_() -> core() -> getSubject();

		$this -> view -> form = $form = new Album_Form_Album_Delete();

		if (!$album)
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Album doesn't exists or not authorized to delete");
			return;
		}

		if (!$this -> getRequest() -> isPost())
		{
			$this -> view -> status = false;
			$this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
			return;
		}

		$db = $album -> getTable() -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$album -> delete();

			$db -> commit();
		}

		catch ( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}

		$this -> view -> status = true;
		$this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Album has been deleted.');
		return $this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'messages' => Array($this -> view -> message)
		));
	}

	/* ----- Set Featured Album Function ----- */
	public function featureAction()
	{
		// Get params
		$id = $this -> _getParam('album_id');
		$is_featured = $this -> _getParam('status');

		// Get campaign need to set featured
		$table = Engine_Api::_() -> getDbTable('albums', 'advalbum');
		$select = $table -> select() -> where("album_id = ?", $id);
		$album = $table -> fetchRow($select);
		// Set featured/unfeatured
		if ($album)
		{
			$album -> featured = $is_featured;
			$album -> save();
		}
	}

}
