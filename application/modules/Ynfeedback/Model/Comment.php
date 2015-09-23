<?php
class Ynfeedback_Model_Comment extends Core_Model_Item_Abstract {
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }
}
