<?php
class Advalbum_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_settings');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->form = $form = new Advalbum_Form_Admin_Global();
    $form->album_page->setValue($settings->getSetting('album_page', 24));
	$form->album_others->setValue($settings->getSetting('album_others', 4));
	$form->album_thumbnailstyle->setValue($settings->getSetting('album_thumbnailstyle', 'crop'));
	$form->album_default_photo_title->setValue($settings->getSetting('album_default_photo_title', $this->view->translate('[Untitled]')));
	$form->album_max_photo_crontask->setValue($settings->getSetting('album_max_photo_crontask', 100));
    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();
       foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
	  $form->addNotice("Your changes have been saved.");
    }
  }
  
  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_categories');

    $this->view->categories = Engine_Api::_()->advalbum()->getCategories();
  }


  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Advalbum_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'advalbum');

        // insert the album category into the database
        $row = $table->createRow();
        $row->user_id   =  1;
        $row->category_name = $values["label"];
        $row->save();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->album_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // go through logs and see which album used this $id and set it to ZERO
        $albumTable = $this->_helper->api()->getDbtable('albums', 'advalbum');
        $select = $albumTable->select()->where('category_id = ?', $id);
        $albums = $albumTable->fetchAll($select);


        // create permissions
        foreach( $albums as $album )
        {
          //this is not working
          $album->category_id = 0;
          $album->save();
        }

        $row = Engine_Api::_()->advalbum()->getCategory($id);
        // delete the album entry into the database
        $row->delete();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Advalbum_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // edit category in the database
        // Transaction
        $row = Engine_Api::_()->advalbum()->getCategory($values["id"]);

        $row->category_name = $values["label"];
        $row->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Must have an id
    if( !($id = $this->_getParam('id')) )
    {
      die('No identifier specified');
    }

    // Generate and assign form
    $category = Engine_Api::_()->advalbum()->getCategory($id);
    $form->setField($category);

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }
}

