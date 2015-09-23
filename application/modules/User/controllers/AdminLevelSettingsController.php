<?php
class User_AdminLevelSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('player_admin_main', array(), 'player_admin_main_usergroupsettings');
  }
  public function indexAction()
  {
  	// Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new User_Form_Admin_Player_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('user_playercard', $id, array_keys($form->getValues())));
	// get max allow
    $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $msselect = $mtable->select()
                ->where("type = 'user_playercard'")
                ->where("level_id = ?",$id)
                ->where("name = 'max_player_card'");
    $mallow = $mtable->fetchRow($msselect);
    if (!empty($mallow))
        $max_player_card = $mallow['value'];
    if($id < 5)
    {
        $max_get = $form->max_player_card->getValue();
        if ($max_get < 1)
        	$form->max_player_card->setValue($max_player_card);
    }
    // Check post
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      $permissionsTable->setAllowed('user_playercard', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }
}
