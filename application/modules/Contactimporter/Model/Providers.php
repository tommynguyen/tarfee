<?php
class Contactimporter_Model_Providers extends Core_Model_List
{
  protected $_owner_type = 'contactimporter_provider';

  protected $_child_type = 'contactimporter_provider';
  protected $_collection_type = 'contactimporter_provider';

  protected $_collection_column_name = 'name';

 public function getCollection()
  {
    return Engine_Api::_()->getItem($this->_collection_type, $this->name);

  }

}