<?php

class Ynbanmem_Model_DbTable_Conversations extends Engine_Db_Table
{
  protected $_rowClass = 'Messages_Model_Conversation';
  protected $_name = 'messages_conversations';
  public function getInboxPaginator(User_Model_User $user)
  {
    $paginator = new Zend_Paginator_Adapter_DbTableSelect($this->getInboxSelect($user));
    $paginator->setRowCount($this->getInboxCountSelect($user));
    return new Zend_Paginator($paginator);
  }

  public function getInboxSelect(User_Model_User $user)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');
    $select = $this->select()
      ->from($cName)
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      ->where("`{$rName}`.`inbox_deleted` = ?", 0)
      ->order(new Zend_Db_Expr('inbox_updated DESC'));
      ;

    return $select;
  }

  public function getInboxCountSelect(User_Model_User $user)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($cName, new Zend_Db_Expr('COUNT(1) AS zend_paginator_row_count'))
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      ->where("`{$rName}`.`inbox_deleted` = ?", 0)
      ;
    return $select;
  }

  public function getOutboxPaginator(User_Model_User $user)
  {
    $paginator = new Zend_Paginator_Adapter_DbTableSelect($this->getOutboxSelect($user));
    $paginator->setRowCount($this->getOutboxCountSelect($user));
    return new Zend_Paginator($paginator);
  }

   
  public function getOutboxSelect(User_Model_User $user)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');
	
	$select = $this->select()
      ->from($cName)
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      ->where("`{$rName}`.`outbox_deleted` = ?", 0)
      ->order(new Zend_Db_Expr('outbox_updated DESC'));
      ;
	
    return $select;
  }

  public function getOutboxCountSelect(User_Model_User $user)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');
	
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($cName, new Zend_Db_Expr('COUNT(1) AS zend_paginator_row_count'))
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` = ?", $user->getIdentity())
      ->where("`{$rName}`.`outbox_deleted` = ?", 0)
      ;
	 
    return $select;
  }
  
  public function getAllOutboxPaginator($users)
  {
	$arr = $this->getAllOutboxSelect($users);
    $paginator =  Zend_Paginator::factory($arr);
	
    return $paginator;
  }
  
  
  function getFirst($subArr)
	{
		
	    return $subArr['user_id'];
	}
  
  public function getAllOutboxSelect($users)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');  
  
    $str = implode(",",array_map(array($this, "getFirst"), $users));	
    
    $select = 
    "
    SELECT `engine4_messages_conversations`.* FROM `engine4_messages_conversations` 
		RIGHT JOIN (`engine4_messages_recipients` INNER JOIN `engine4_ynbanmem_extramessage` ON `engine4_messages_recipients`.outbox_message_id = `engine4_ynbanmem_extramessage`.message_id) 
		ON `engine4_messages_recipients`.`conversation_id` = `engine4_messages_conversations`.`conversation_id` WHERE (`engine4_messages_recipients`.`user_id` IN (".$str.")) AND (`engine4_messages_recipients`.`outbox_deleted` = 0)  ORDER BY outbox_updated DESC;
    
    ";    
	$db = Engine_Db_Table::getDefaultAdapter();
    return  $db->fetchAll($select); 
  }

  public function getAllOutboxCountSelect($users)
  {
    $rName = Engine_Api::_()->getDbtable('recipients', 'messages')->info('name');
    $cName = $this->info('name');
	
    $select = new Zend_Db_Select($this->getAdapter());
    $select
      ->from($cName, new Zend_Db_Expr('COUNT(1) AS zend_paginator_row_count'))
      ->joinRight($rName, "`{$rName}`.`conversation_id` = `{$cName}`.`conversation_id`", null)
      ->where("`{$rName}`.`user_id` IN (?)", $users)
      ->where("`{$rName}`.`outbox_deleted` = ?", 0)
      ;
	 
    return $select;
  }
///
  public function send(Core_Model_Item_Abstract $user, $recipients, $title, $body, $attachment = null, $info)
  {
    $resource = null;
    
    // Case: single user
    if( $recipients instanceof User_Model_User ) {
      $recipients = array($recipients->getIdentity());
    }
    // Case: group/event members
    else if( $recipients instanceof Core_Model_Item_Abstract &&
        method_exists($recipients, 'membership') ) {
      $resource = $recipients;
      $recipients = array();
      foreach( $resource->membership()->getMembers() as $member ) {
        if( $member->getIdentity() != $user->getIdentity() ) {
          $recipients[] = $member->getIdentity();
        }
      }
    }
    // Case: single id
    else if( is_numeric($recipients) ) {
      $recipients = array($recipients);
    }
    // Case: array
    else if( is_array($recipients) && !empty($recipients) ) {
      // Ok
    }
    // Whoops
    else {
      throw new Messages_Model_Exception("A message must have recipients");
    }
    
    // Create conversation
    $conversation = $this->createRow();
    $conversation->setFromArray(array(
      'user_id' => $user->getIdentity(),
      'title' => $title,
      'recipients' => count($recipients),
      'modified' => date('Y-m-d H:i:s'),
      'locked' => ( $resource ? true : false ),
      'resource_type' => ( !$resource ? null : $resource->getType() ),
      'resource_id' => ( !$resource ? 0 : $resource->getIdentity() ),
    ));
    $conversation->save();

    // Create message
    $message = Engine_Api::_()->getItemTable('messages_message')->createRow();
    $message->setFromArray(array(
      'conversation_id' => $conversation->getIdentity(),
      'user_id' => $user->getIdentity(),
      'title' => $title,
      'body' => $body,
      'date' => date('Y-m-d H:i:s'),
      'attachment_type' => ( $attachment ? $attachment->getType() : '' ),
      'attachment_id' => ( $attachment ? $attachment->getIdentity() : 0 ),
    ));
    $message->save();
    //print_r($info);die;
	//Create extra message info
	
	$extra = Engine_Api::_()->getDbTable('extramessage','ynbanmem');
	$extra->insert(array(
                'message_id' => $message->getIdentity(),
                'sender_email' => $user->email,
                'type'=>$info['type'],
                'email_type' => $info['from'],
                'reason' => $info['reason']
            ));
	//$extra->save();
	//$extraMessageTable->addExtraMessage($message->getIdentity(), $info);
	
    // Create sender outbox
    Engine_Api::_()->getDbtable('recipients', 'messages')->insert(array(
      'user_id' => $user->getIdentity(),
      'conversation_id' => $conversation->getIdentity(),
      'outbox_message_id' => $message->getIdentity(),
      'outbox_updated' => date('Y-m-d H:i:s'),
      'outbox_deleted' => 0,
      'inbox_deleted' => 1,
      'inbox_read' => 1
    ));

    // Create recipients inbox
    foreach( $recipients as $recipient_id ) {
      Engine_Api::_()->getDbtable('recipients', 'messages')->insert(array(
        'user_id' => $recipient_id,
        'conversation_id' => $conversation->getIdentity(),
        'inbox_message_id' => $message->getIdentity(),
        'inbox_updated' => date('Y-m-d H:i:s'),
        'inbox_deleted' => 0,
        'inbox_read' => 0,
        'outbox_message_id' => 0,
        'outbox_deleted' => 1,
      ));
    }

    return $conversation;
  }
}
?>