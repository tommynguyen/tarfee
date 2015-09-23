<?php
class Ynblog_Model_Category extends Core_Model_Item_Abstract
{
  protected $_type = "blog_category";

  /*----- Get Category Link Fucntion -----*/
  public function getHref($params = array()) {
        $params = array_merge(array(
                    'category' => $this->category_id,
                    'reset'    => true,
                  ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

  /*----- Category Used Count Function -----*/
  public function getUsedCount(){
    $table  = Engine_Api::_()->getItemTable('blog');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.category_id = ?', $this->category_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    return $total;
  }

  /*----- Ownership Function -----*/

  public function isOwner($owner)
  {
    if( $owner instanceof Core_Model_Item_Abstract )
    {
      return ( $this->getIdentity() == $owner->getIdentity() && $this->getType() == $owner->getType() );
    }

    else if( is_array($owner) && count($owner) === 2 )
    {
      return ( $this->getIdentity() == $owner[1] && $this->getType() == $owner[0] );
    }

    else if( is_numeric($owner) )
    {
      return ( $owner == $this->getIdentity() );
    }

    return false;
  }
}
?>
