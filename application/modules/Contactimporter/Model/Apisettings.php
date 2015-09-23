<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: List.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Contactimporter_Model_Apisettings extends Core_Model_List
{
  protected $_owner_type = 'contactimporter_apisettings';

  protected $_child_type = 'contactimporter_apisettings';
  protected $_collection_type = 'contactimporter_apisettings';

  protected $_collection_column_name = 'api_name';

 public function getCollection()
  {
    return Engine_Api::_()->getItem($this->_collection_type, $this->name);

  }
    public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('apisettings', 'Contactimporter');
    }

    return $this->_table;
  }

}