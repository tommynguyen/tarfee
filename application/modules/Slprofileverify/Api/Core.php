<?php

class Slprofileverify_Api_Core extends Core_Api_Abstract {

    public function setPhotoUserIdentty($photo, $parent_type = "slprofileverify") {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = basename($file);
        }
        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $parent_type,
            'parent_id' => NULL,
            'user_id' => NULL,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image_main = Engine_Image::factory();
        $image_main->open($file)
                ->resize(450, 450)
                ->write($mainPath)
                ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image_normal = Engine_Image::factory();
        $image_normal->open($file)
                ->resize(70000, 145)
                ->write($normalPath)
                ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iNormal = $filesTable->createFile($normalPath, $params);

        $iMain->bridge($iNormal, 'thumb.normal');

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        return $iMain->file_id;
    }

    public function setPhotoVerification($photo, $parent_type, $task = null) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
            $fileName = $file;
        } else if ($photo instanceof Storage_Model_File) {
            $file = $photo->temporary();
            $fileName = $photo->name;
        } else if ($photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id)) {
            $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
            $file = $tmpRow->temporary();
            $fileName = $tmpRow->name;
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
            $fileName = $photo['name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
            $fileName = $photo;
        } else {
            throw new Engine_Exception('invalid argument passed to setPhoto');
        }

        if (!$fileName) {
            $fileName = basename($file);
        }
        $extension = ltrim(strrchr(basename($fileName), '.'), '.');
        $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_type' => $parent_type,
            'parent_id' => NULL,
            'user_id' => NULL,
            'name' => $fileName,
        );

        // Save
        $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

        if ($task == "pBadge") {
            // Resize image (main)
            $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
            $image_main = Engine_Image::factory();
            $image_main->open($file)
                    ->resize(70000, 27)
                    ->write($mainPath)
                    ->destroy();
            $iMain = $filesTable->createFile($mainPath, $params);
            // Remove temp files
            @unlink($mainPath);
            return $iMain->file_id;
        }

        // Resize image (main)
        $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
        $image_main = Engine_Image::factory();
        $image_main->open($file)
                ->resize(70000, 145)
                ->write($mainPath)
                ->destroy();

        // Resize image (normal)
        $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_in.' . $extension;
        $image_normal = Engine_Image::factory();
        $image_normal->open($file)
                ->resize(70000, 60)
                ->write($normalPath)
                ->destroy();

        // Store
        $iMain = $filesTable->createFile($mainPath, $params);
        $iNormal = $filesTable->createFile($normalPath, $params);

        $iMain->bridge($iNormal, 'thumb.normal');

        // Remove temp files
        @unlink($mainPath);
        @unlink($normalPath);

        return $iMain->file_id;
    }

    public function getPhotoVerificaiton($file_id, $type = null, $task = null) {
        if (!$file_id && !$type && !$task) {
            return null;
        }
        if (!$file_id && $task == "pBadge") {
            return Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/verified.png';
        }
        $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id, $type);
        if (!$file) {
            return null;
        }
        return $file->map();
    }

    public function getPhotoIdentityUrl(array $file_id, $type = null, $task = null) {

        if (count($file_id) <= 0) {
            $arrDefault = array();
            if ($task == 'identity') {
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_0.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_1.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_2.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_3.png';
            }

            if ($task == 'custom') {
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_cus_0.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_cus_1.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_cus_2.png';
                $arrDefault[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_cus_3.png';
            }
            return $arrDefault;
        }

        $arrPhoto = array();
        foreach ($file_id as $value) {
            if (in_array($value, array(0, 1, 2, 3))) {
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($value, $type);
                if ($file['parent_type'] == 'slprofileverify') {
                    $arrPhoto[] = $file->map();
                } elseif ($task == 'identity') {
                    $arrPhoto[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_' . $value . '.png';
                } elseif ($task == 'custom') {
                    $arrPhoto[] = Zend_Registry::get('Zend_View')->baseUrl() . '/application/modules/Slprofileverify/externals/images/id_cus_' . $value . '.png';
                }
            } else {
                $file = Engine_Api::_()->getItemTable('storage_file')->getFile($value, $type);
                if ($file['parent_type'] == 'slprofileverify') {
                    $arrPhoto[] = $file->map();
                }
            }
        }

        return $arrPhoto;
    }

    // Vefiry and unverify
    public function verifyUser($user_id, $type_verify = 'verified') {

        $metaTbl = Engine_Api::_()->getItemTable('slprofileverify_slprofileverify');
        $verifyRow = $metaTbl->getVerifyInfor($user_id);
        $user = Engine_Api::_()->getItem('user', $user_id);
        $oCoreSettings = Engine_Api::_()->getApi('settings', 'core');
        $sGroupVerified = $oCoreSettings->getSetting('group_member_verified', null);
        $aGroupVerified = Zend_Json::decode($sGroupVerified);
        $sGroupUnverified = $oCoreSettings->getSetting('group_member_unverified', null);
        $aGroupUnverified = Zend_Json::decode($sGroupUnverified);
        //$viewer = Engine_Api::_()->user()->getViewer();

        if ($type_verify == 'verified') {
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $user, $user, 'slprofileverify_verify');
            Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'slprofileverify_verify', null);

            // maping level
            if ($sGroupVerified && $sGroupUnverified) {
                if (in_array($user->level_id, $aGroupUnverified)) {
                    $iKey = array_search($user->level_id, $aGroupUnverified);
                    $user->level_id = $aGroupVerified[$iKey];
                    $user->save();
                }
            }
        } elseif ($type_verify == 'unverified') {
            if ($verifyRow->approval == 'pending') {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $user, $user, 'slprofileverify_deny');
            } else {
                Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $user, $user, 'slprofileverify_unverify');
                // maping level
                if ($sGroupVerified && $sGroupUnverified) {
                    if (in_array($user->level_id, $aGroupVerified)) {
                        $iKey = array_search($user->level_id, $aGroupVerified);
                        $user->level_id = $aGroupUnverified[$iKey];
                        $user->save();
                    }
                }
            }
        }

        if ($verifyRow) {
            $verifyRow->approval = $type_verify;
            $verifyRow->verified_date = new Zend_Db_Expr('NOW()');
            $verifyRow->save();
        } else {
            $data = array(
                'user_id' => $user_id,
                'approval' => $type_verify,
                'verified_date' => new Zend_Db_Expr('NOW()'),
                'request_date' => '0000-00-00 00:00:00',
                'file_id' => 0,
                'file_id_cus' => 0,
                'reason' => null
            );
            $metaTbl->insert($data);
        }
    }

    // Tan send mail verify and unverify
    public function sendMailVerify($user_id, $messages = null, $type = "verify") {
        if (!$user_id) {
            return;
        }
        $view = Zend_Registry::get("Zend_View");
        $recipient = Engine_Api::_()->getItem('user', $user_id);

        $mailType = '';
        $mailParams = array();
        $host = $_SERVER['HTTP_HOST'];
        $contactSite = $view->url(array('module' => 'core', 'controller' => 'help', 'action' => 'contact'), 'default', true);
        if ($type == "verify") {
            $mailType = 'slprofileverify_verified';
            $mailParams = array(
                'host' => $host,
                'sender_link' => $recipient->getHref(),
            );
        } else if ($type == "unverifying") {
            $mailType = 'slprofileverify_unverified';
            $mailParams = array(
                'host' => $host,
                'sender_messages' => $messages,
                'sender_link' => $contactSite,
            );
        } else if ($type == "denied") {
            $mailType = 'slprofileverify_denied';
            $mailParams = array(
                'host' => $host,
                'sender_messages' => $messages,
                'sender_link' => $contactSite,
            );
        } else {
            $mailType = 'slprofileverify_change_profile';
            $mailParams = array(
                'host' => $host,
                'sender_link' => $contactSite,
            );
        }
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($recipient, $mailType, $mailParams);
    }

}