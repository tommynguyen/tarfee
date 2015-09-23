<?php
class Ynevent_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'event';
    protected $_type = 'event_album';   
  protected $_owner_type = 'event';
  

  protected $_children_types = array('event_photo');

  protected $_collectible_type = 'event_photo';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'event_profile',
      'reset' => true,
      'id' => $this->getEvent()->getIdentity(),
      //'album_id' => $this->getIdentity(),
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }

  public function getEvent()
  {
    return $this->getOwner();
    //return Engine_Api::_()->getItem('event', $this->event_id);
  }

  public function getAuthorizationItem()
  {
    return $this->getParent('event');
  }

  protected function _delete()
  {
    // Delete all child posts
    $photoTable = Engine_Api::_()->getItemTable('event_photo');
    $photoSelect = $photoTable->select()->where('album_id = ?', $this->getIdentity());
    foreach( $photoTable->fetchAll($photoSelect) as $eventPhoto ) {
      $eventPhoto->delete();
    }

    parent::_delete();
  }
  
  public function getFeaturedPaginator($params)
  {
  	$tbl_photos = Engine_Api::_()->getItemTable('event_photo');
	$photoSelect = $tbl_photos->select()->where('album_id = ?', $this->getIdentity());
	
	if(isset($params['is_featured']))
	{
		 $photoSelect-> where("is_featured = ?", $params['is_featured'] );
	}
	
	return Zend_Paginator::factory($photoSelect);
  }
}