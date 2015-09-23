<?php
class User_AdminLibrariesController extends Core_Controller_Action_Admin
{
  public function settingsAction()
  {
  	
  	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('libraries_admin_main', array(), 'libraries_admin_main_globalsettings');
  	
  	// Make form
    $settings = Engine_Api::_()->getApi('settings', 'core');
     $this->view->form = $form = new User_Form_Admin_Library_Global();
     if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
        $values = $form->getValues();
        foreach ($values as $key => $value) {
            $settings->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.'); 
    }
  }
  
  public function tokenAction()
  {
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(true);
		$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
		    FILTER_SANITIZE_URL);
		 $redirect = str_replace("index.php", "admin/user/libraries/settings?code=".$this->_getParam('code'), $redirect);   
		header('Location: ' . $redirect);
		
  }
  
  public function levelAction()
  {
	
	$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('libraries_admin_main', array(), 'libraries_admin_main_levelsettings');
	
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
    $this->view->form = $form = new User_Form_Admin_Library_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('user_library', $id, array_keys($form->getValues())));

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
      $permissionsTable->setAllowed('user_library', $id, $values);

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
