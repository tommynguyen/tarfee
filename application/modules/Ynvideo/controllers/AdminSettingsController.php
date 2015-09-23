<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynvideo_admin_main', array(), 'ynvideo_admin_main_settings');

        // Check ffmpeg path for correctness
        if (function_exists('exec')) {
            $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path;

            $output = null;
            $return = null;
            if (!empty($ffmpeg_path)) {
                exec($ffmpeg_path . ' -version', $output, $return);                
            }
            // Try to auto-guess ffmpeg path if it is not set correctly
            $ffmpeg_path_original = $ffmpeg_path;
            if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
                $ffmpeg_path = null;
                // Windows
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // @todo
                }
                // Not windows
                else {
                    $output = null;
                    $return = null;
                    @exec('which ffmpeg', $output, $return);
                    if (0 == $return) {
                        $ffmpeg_path = array_shift($output);
                        $output = null;
                        $return = null;
                        exec($ffmpeg_path . ' -version', $output, $return);
                        if (0 != $return) {
                            $ffmpeg_path = null;
                        }
                    }
                }
            }
            if ($ffmpeg_path != $ffmpeg_path_original) {
                Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path = $ffmpeg_path;
            }
        }

        // Make form
        $this->view->form = $form = new Ynvideo_Form_Admin_Global();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        // Check ffmpeg path
        if (!empty($values['ynvideo_ffmpeg_path'])) {
            if (function_exists('exec')) {
                $ffmpeg_path = $values['ynvideo_ffmpeg_path'];
                $output = null;
                $return = null;
                exec($ffmpeg_path . ' -version', $output, $return);
                if ($return > 0) {
                    $form->ynvideo_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
                    $values['ynvideo_ffmpeg_path'] = '';
                }
            } else {
                $form->ynvideo_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
                $values['ynvideo_ffmpeg_path'] = '';
            }
        }

        // Okay, save
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }

    public function categoriesAction() {
        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
                'application/modules/Ynvideo/externals/scripts/collapsible.js');

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynvideo_admin_main', array(), 'ynvideo_admin_main_categories');
        $this->view->categories = Engine_Api::_()->getDbTable('categories', 'ynvideo')
                ->getAllCategoriesAndSortByLevel(null, array('category_name'));
    }

    public function levelAction() {
        // Make navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynvideo_admin_main', array(), 'ynvideo_admin_main_level');

        // Get level id
        if (null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $level_id = $id = $level->level_id;

        // Make form
        $this->view->form = $form = new Ynvideo_Form_Admin_Settings_Level(array(
                    'public' => ( in_array($level->type, array('public')) ),
                    'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
                ));
        $form->level_id->setValue($id);

        // Populate values
        $formSettingValues = $form->getSettingsValues();
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $videoKeyValues = $permissionsTable->getAllowed('video', $id, array_keys($formSettingValues['video']));
        
        // TODO [DangTH] : get the max number of video that a user leven can upload
        $videoKeyValues['max'] = Engine_Api::_()->ynvideo()->getAllowedMaxValue('video', $id, 'max');
        foreach($videoKeyValues as $key => $value) {
            $videoKeys['video_' . $key] = $value;
        }
        $playlistKeyValues = $permissionsTable->getAllowed('ynvideo_playlist', $id, array_keys($formSettingValues['playlist']));
        $playlistKeys = array();
        foreach($playlistKeyValues as $key => $value) {
            $playlistKeys['playlist_' . $key] = $value;
        }
        $form->populate(array_merge($videoKeys, $playlistKeys));

        // Check post
        if (!$this->getRequest()->isPost()) {
            return;
        }

        // Check validitiy
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $settingValues = $form->getSettingsValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try {
            // Set permissions
            $permissionsTable->setAllowed('video', $id, $settingValues['video']);
            $permissionsTable->setAllowed('ynvideo_playlist', $id, $settingValues['playlist']);
            
            // Commit
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
    }

    public function utilityAction() {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynvideo_admin_main', array(), 'ynvideo_admin_main_utility');

        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path;
        if (function_exists('shell_exec')) {
            // Get version
            $this->view->version = $version
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
            $command = "$ffmpeg_path -formats 2>&1";
            $this->view->format = $format
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
                    . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
        }
    }

    public function addCategoryAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');

        // Generate and assign form
        $form = $this->view->form = new Ynvideo_Form_Admin_Category();

        $tableCategory = Engine_Api::_()->getDbtable('categories', 'ynvideo');
        $cats = $tableCategory->getAllCategoriesAndSortByLevel(null, array('category_name'));
        $parentCatElement = $form->getElement('parent_id');
        $parentCatElement->addMultiOption(0, '');
        foreach ($cats as $cat) {
            if ($cat->parent_id == 0) {
                $parentCatElement->addMultiOption($cat->getIdentity(), $cat->category_name);
            }
        }

        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
        // Check post
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            // we will add the category

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // add category to the database
                // Transaction
                $user = Engine_Api::_()->user()->getViewer();
                
                $fullFilePath = $this->_getUploadedFile();
                $values = $form->getValues();
                $values['fullFilePath'] = $fullFilePath;
                
                // insert the category into the database
                $row = $tableCategory->createRow();
                $row->user_id = $user->getIdentity();
                $row->category_name = $values["label"];
                $row->parent_id = $values["parent_id"];
                $row->photo_url = $values["fullFilePath"];
                $row->save();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category is created successfully.')),
                'layout' => 'default-simple',
                'parentRefresh' => true,
            ));
        }

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        // Output
        $this->renderScript('admin-settings/form.tpl');
    }

    public function deleteCategoryAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost() && $id) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // go through logs and see which videos used this category id and set it to ZERO

                $videoTable = Engine_Api::_()->getDbtable('videos', 'ynvideo');
                $select = $videoTable->select()->where('category_id = ?', $id);
                $videos = $videoTable->fetchAll($select);

                // create permissions
                foreach ($videos as $video) {
                    //this is not working
                    $video->category_id = 0;
                    $video->save();
                }

                $row = Engine_Api::_()->ynvideo()->getCategory($id);
                // delete the video category into the database
                if ($row) {
                    $row->delete();
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category is deleted successfully.'))
            ));
        }

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        // Output
        $this->renderScript('admin-settings/delete.tpl');
    }

    public function editCategoryAction() {
        // Must have an id
        if (!($id = $this->_getParam('id'))) {
            die('No identifier specified');
        }

        // Generate and assign form
        $category = Engine_Api::_()->ynvideo()->getCategory($id);
        
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $form = $this->view->form = new Ynvideo_Form_Admin_Category(
            array('category' => $category, 'isEditing' => true)
        );
        $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

        // Check post
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $fullFilePath = $this->_getUploadedFile();
            
            // Ok, we're good to add field
            $values = $form->getValues();
            $values['fullFilePath'] = $fullFilePath;

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                // edit category in the database
                // Transaction
                $row = Engine_Api::_()->ynvideo()->getCategory($values["id"]);
                $row->category_name = $values["label"];
                if (!empty ($values['fullFilePath'])) {
                    $row->photo_url = $values["fullFilePath"];
                }
                
                $row->save();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category is edited successfully.'))
            ));
        }

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');
        // Output
        $this->renderScript('admin-settings/form.tpl');
    }

    private function _getUploadedFile() {
        $fullFilePath = '';
        $upload = new Zend_File_Transfer_Adapter_Http();
        if ($upload->getFileName('photo')) {
            $destination = "public/ynvideo_category/";
            if (!is_dir($destination)) {
                mkdir($destination);
            }
            $upload->setDestination($destination);
            $fullFilePath = $destination . time() . '_' . $upload->getFileName('photo', false);

            $image = Engine_Image::factory();
            $image->open($_FILES['photo']['tmp_name'])
                ->resize(128, 128)
                ->write($fullFilePath);
        }
        return $fullFilePath;
    }
}