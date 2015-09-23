<?php
class Ynnotification_Installer extends Engine_Package_Installer_Module
{
	function onInstall()
	{
		parent::onInstall();
		
		$db = $this -> getDb();
		$select = new Zend_Db_Select($db);
		$select -> from('engine4_core_content') -> where('name = ?', 'ynnotification.displays-notification') -> limit(1);
		$widget = $select -> query() -> fetch();
		if (empty($widget))
		{
			//$db = $this -> getDb();
			$selectPage = new Zend_Db_Select($db);
			$selectPage -> from('engine4_core_pages') -> where('name = ?', 'header') -> limit(1);
			$page = $selectPage -> query() -> fetch();
			
			$db -> insert('engine4_core_content', array(
					'page_id' => $page['page_id'],
					'type' => 'widget',
					'name' => 'ynnotification.displays-notification',
					'parent_content_id' => 100,
					'order' => 999,
			));
		}
		
		
		$db2 = Engine_Db_Table::getDefaultAdapter();
		$select = "INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES ('ynnotification.current.time','".date('Y-m-d H:i:s')."');";		
		$data = $db2->query( $select);
	}

}
