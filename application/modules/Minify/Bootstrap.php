<?php

class Minify_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

    private function e($n, $s)
    {
        $table2 = Engine_Api::_() -> getDbTable('modules', 'core');
        $data = array('enabled' => $s, );
        $where = $table2 -> getAdapter() -> quoteInto('name = ?', $n);
        $table2 -> update($data, $where);
    }

    public function _initminify1317023719()
    {
        $table = Engine_Api::_() -> getDbtable('modules', 'core');
        $rName = $table -> info('name');
        $select = $table -> select() -> from($rName);
        $select -> where('name = ?', 'younet-core');
        $select -> where('enabled = ?', 1);
        $result = $table -> fetchRow($select);
        $module_name = 'minify';
        if (!$result)
        {

            $table2 = Engine_Api::_() -> getDbTable('modules', 'core');
            $data = array('enabled' => 0, );
            $where = $table2 -> getAdapter() -> quoteInto('name = ?', $module_name);
            $table2 -> update($data, $where);
        }
        else
        {
            defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
            $file = APPLICATION_PATH . '/application/settings/database.php';
            $options =
            include $file;
            $db = $options['params'];
            $connection = mysql_connect($db['host'], $db['username'], $db['password']);
            $prefix = $options['tablePrefix'];
            if (!$connection)
                return true;
            $db_selected = mysql_select_db($db['dbname']);
            if (!$db_selected)
                return true;
            mysql_query("SET character_set_client=utf8", $connection);
            mysql_query("SET character_set_connection=utf8", $connection);
            $r = mysql_query("SELECT * FROM engine4_younetcore_license where name = '" . $module_name . "' limit 1");
            $ra = mysql_fetch_assoc($r);
            if (count($ra) <= 0 || $ra == false)
            {
                $res = @mysql_query("INSERT IGNORE INTO `engine4_younetcore_license` (`name`, `title`, `descriptions`, `type`, `current_version`, `lasted_version`, `is_active`, `date_active`, `params`, `download_link`, `demo_link`) VALUES ('minify', 'Minify', '', 'module', '4.03p2', '4.03p2', '0', NULL, NULL, NULL, NULL);", $connection);
                $this -> e($module_name, 0);
            }
            else
            {
                if (!isset($ra['is_active']) || $ra['is_active'] != 1)
                {
                    $this -> e($module_name, 0);
                }
            }

        }
    }

}
