<?php
class Ynmember_Widget_FeaturedMembersController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$limit = (int) $this->_getParam('itemCountPerPage', 7);
		if ($limit < 1) {
			$limit = 7;
		}
		$this->view->limit = $limit;

		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$featureTbl = Engine_Api::_()->getItemTable('ynmember_feature');
		$featureTblName = $featureTbl ->info('name');
		$select = $userTbl -> select() -> setIntegrityCheck(false)
		-> from ($userTblName)
		-> joinLeft($featureTblName, "{$userTblName}.`user_id` = {$featureTblName}.`user_id`", array("{$featureTblName}.active"))
		-> where("{$userTblName}.`enabled` = 1") -> where("{$userTblName}.`verified` = 1") -> where("{$userTblName}.`approved` = 1")
		-> where("{$featureTblName}.`active` = 1")
		-> order(new Zend_Db_Expr(('rand()')))
		-> limit($limit);
		$this->view->users = $users = $userTbl -> fetchAll($select);

		if(count($users) < 1)
		{
			return $this->setNoRender();
		}
	}
}