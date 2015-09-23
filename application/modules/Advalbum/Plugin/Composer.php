<?php
class Advalbum_Plugin_Composer extends Core_Plugin_Abstract
{
  public function onAttachPhoto($data)
  {
    if( !is_array($data) || empty($data['photo_id']) ) {
      return;
    }

    $photo = Engine_Api::_()->getItem('advalbum_photo', $data['photo_id']);

    if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() )
    {
      return;
    }

    return $photo;
  }
}