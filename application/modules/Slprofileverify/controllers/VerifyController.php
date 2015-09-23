<?php
class Slprofileverify_VerifyController extends Core_Controller_Action_Admin
{
  
  public function verifyAction()
  {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->user_id = $user_id = $this->_getParam('id');
    if ($this->getRequest()->isPost()) {
        $settingApi = Engine_Api::_()->getApi('core', 'slprofileverify');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $settingApi->verifyUser($user_id);
            $settingApi->sendMailVerify($user_id, null, "verify");
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => 10,
                        'parentRefresh' => 10,
                        'messages' => array('successfully')
        ));
    }
  }
  
  public function verifyAllAction()
  {
    if ($this->getRequest()->isPost()) {
        $settingApi = Engine_Api::_()->getApi('core', 'slprofileverify');
        foreach($_POST as $user_id)
        {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $settingApi->verifyUser($user_id);
                $settingApi->sendMailVerify($user_id, null, "verify");
                $db->commit();
            } catch( Exception $e ) {
              $db->rollBack();
              throw $e;
            }
        }
    }
    return $this->_helper->redirector->gotoRoute(array('module' => 'slprofileverify', 'controller' => 'manage', 'action' => 'index'),'admin_default',true);
	    
  }  
  
  public function denyAction()
  {
    $id = $this->_getParam('id');
    $array_id = $this->_getParam('array_id');
    $reasonTable = Engine_Api::_()->getItemTable('slprofileverify_reason');
    $this->view->reasons = $reasonTable->fetchAll();
    $type = $this->_getParam('type');
    if($type != "unverifying"){
        $type = "denied";
    }
    $this->view->type = $type;
    if ($this->getRequest()->isPost()) {
        $values = $this->getRequest()->getPost();
        
        if(!isset($values['reason']) && !isset($values['other']))
        {
            $this->view->error_reason = 1;
            return;
        }
        if(empty($values['content']) && isset($values['other']))
        {
            $this->view->error_reason = 2;
            return;
        }

        $messages = "<ul style='list-style: square inside none;'>";
        if(isset($values['reason']))
        {
            foreach($values['reason'] as $reason_id)
            {
                $reason = Engine_Api::_()->getItem('slprofileverify_reason', $reason_id);
                $messages .= "<li>" . $reason->description . '</li>';
            }
        }
        if(!empty($values['content']))
        {
            $messages .= "<li>" . $values['content'] . '</li>';
        }
        $messages .= "</ul>";
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
            $settingApi = Engine_Api::_()->getApi('core', 'slprofileverify');
            if(!empty($array_id))
            {
                $array_ids = split(",", $array_id);
                foreach($array_ids as $id)
                {
                    if($id)
                    {
                        $settingApi->verifyUser($id, 'unverified');
                        $settingApi->sendMailVerify($id, $messages, $type);
                    }
                }
            }
            else
            {
                if($id)
                {
                    $settingApi->verifyUser($id, 'unverified');
                    $settingApi->sendMailVerify($id, $messages, $type);
                }
            }

            $db->commit();
        }
        catch( Exception $e )
        {
            $db->rollBack();
            throw $e;
        }

        $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => 10,
                    'parentRefresh' => 10,
                    'messages' => array('successfully')
        ));
    }
  }	
}