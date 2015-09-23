<?php
class Ynfeedback_Model_DbTable_Polls extends Engine_Db_Table
{
  protected $_rowClass = 'Ynfeedback_Model_Poll';
  protected $_name = 'ynfeedback_polls';
  
  public function getPollSelect($params = array())
  {
    // Setup
    $params = array_merge(array(
      'user_id' => null,
      'order' => 'recent',
      'search' => '',
      'closed' => 0,
    ), $params);

    $table = Engine_Api::_()->getItemTable('ynfeedback_poll');
    $tableName = $table->info('name');

    $select = $table
      ->select()
      ->from($tableName)
      ;

    // User
    if( !empty($params['user_id']) ) {
      $select
        ->where('user_id = ?', $params['user_id']);
    } else if( !empty($params['users']) && is_array($params['users']) ) {
      $select
        ->where('user_id IN(?)', $params['users']);
    }

    // Order
    switch( $params['order'] ) {
      case 'popular':
        $select
          ->order('vote_count DESC')
          ->order('view_count DESC');
        break;
      case 'recent':
      default:
        $select
          ->order('creation_date DESC');
        break;
    }

    return $select;
  }

  /**
   * Gets a paginator for polls
   *
   * @param Core_Model_Item_Abstract $user The user to get the messages for
   * @return Zend_Paginator
   */
  public function getPollsPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getPollSelect($params));
  }
}