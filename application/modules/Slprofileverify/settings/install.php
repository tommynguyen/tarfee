<?php
class Slprofileverify_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addContentMemberProfile();
    
    parent::onInstall();
  }
  
  protected function _addContentMemberProfile()
  {
    $db = $this->getDb();
    $select = new Zend_Db_Select($db);

    // Get page id
    $page_id = $select
        ->from('engine4_core_pages', 'page_id')
        ->where('name = ?', 'user_profile_index')
        ->limit(1)
        ->query()
        ->fetchColumn(0)
        ;
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $verifyDocument = $select
        ->from('engine4_core_content', new Zend_Db_Expr('TRUE'))
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'slprofileverify.verify-document')
        ->query()
        ->fetchColumn()
    ;

    if(!$verifyDocument)
    {
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $container_id = $select
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1)
        ->query()
        ->fetchColumn()
        ;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $tab_id = $select
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->limit(1)
        ->query()
        ->fetchColumn()
        ;
    // insert
      if( $tab_id ) {
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'slprofileverify.verify-document',
          'parent_content_id' => $tab_id,
          'order'   => 999,
          'params'  => '{"title":"Verify Document","name":"slprofileverify.verify-document"}',
        ));
      }
    }
    
    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $profilebadge = $select
      ->from('engine4_core_content', new Zend_Db_Expr('TRUE'))
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'slprofileverify.profile-badge')
      ->query()
      ->fetchColumn()
      ;
      
    if(!$profilebadge)
    {
        
      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $container_id = $select
        ->from('engine4_core_content', 'content_id')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1)
        ->query()
        ->fetchColumn()
        ;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $left_id = $select
        ->from('engine4_core_content', 'content_id')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'left')
        ->limit(1)
        ->query()
        ->fetchColumn()
        ;
    // insert
      if( $left_id ) {
        $select = new Zend_Db_Select($db);
        $selectRow = $select
          ->from('engine4_core_content', 'order')
          ->where('page_id = ?', $page_id)
          ->where('type = ?', 'widget')
          ->where('name = ?', 'user.profile-photo')
          ->limit(1);
        $profile_photo = $db->fetchRow($selectRow);
        $order_badge = 999;
        if($profile_photo){
            $order_badge = $profile_photo['order'] + 1;
        }
        
        $db->insert('engine4_core_content', array(
          'page_id' => $page_id,
          'type'    => 'widget',
          'name'    => 'slprofileverify.profile-badge',
          'parent_content_id' => $left_id,
          'order'   => $order_badge
        ));
      }
    }
  }
}