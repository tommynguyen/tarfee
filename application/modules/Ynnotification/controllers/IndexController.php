<?php

class Ynnotification_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
    
    
  }
  public function getFeedsAction()
  {
	$viewer = Engine_Api::_()->user()->getViewer();
	
	$now = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.current.time',date('Y-m-d H:i:s'));
	
       $select = Engine_Api::_()->getDbTable('notifications', 'activity')
                        ->select()
                        ->where("`user_id` = ?", $viewer->getIdentity())
                       // ->where("`type` NOT IN ('friend_request','message_new') AND `mitigated` = 0")       
						->where("`advnotification` = 0")  
						->where("TIMESTAMPDIFF(MINUTE, TIMESTAMP('$now'),TIMESTAMP(`date`))>0")					   
						//->where(" TIMESTAMPDIFF(SECOND,`engine4_activity_notifications`.DATE,'".date('Y-m-d H:i:s')."')<".$timeRefresh)
                        ->order('notification_id DESC')
                        ->limit(5);
	
    $datas = Engine_Api::_()->getDbTable('notifications', 'activity')->fetchAll($select);
    $arrayJs = array();   
    $view = Zend_Registry::get('Zend_View');
    
    foreach($datas as $data)
    {
    	$object = Engine_Api::_() -> user() -> getUser($data->subject_id);    
		
		
    	$arrayJs[] = array(
    			'notification_id' => $data->notification_id,
    			'user_id' 		=> $viewer->getIdentity(),
    			'subject_type'	=> $data->subject_type,
    			'subject_id'	=> $data->subject_id,
    			'object_type'	=> $data->object_type,
    			'object_id'	=> $data->object_id,
    			'type'	=> $data->type,
    			'params'	=> $data->params,
    			'read'	=> $data->read,
    			'mitigated'	=> $data->mitigated,
    			'date'	=> $data->date,
    			'user_getTitle'	=> $object->getTitle(),
    			'user_getHref'	=> $object->getHref(),
    			'user_getPhotoUrl'	=> $view->itemPhoto($object, 'thumb.icon'),    			    			
    			'content' 		=> 	$data->getContent()
    	);
		$not = Engine_Api::_() -> getDbTable('notifications', 'activity') -> find($data->notification_id) -> current();
		
		$not->advnotification = true;
		$not->save();
    }   
	
	if(count($arrayJs)>0)
		$_SESSION['data'] = Zend_Json::encode($arrayJs);
    echo Zend_Json::encode($arrayJs);
    exit;
  }
  
  public function notifyReadAction()
  { 	
		$request = Zend_Controller_Front::getInstance()->getRequest();

	    $action_id = $request->getParam('actionid', 0);
	
	    $viewer = Engine_Api::_()->user()->getViewer();
	    $notificationsTable = Engine_Api::_()->getDbtable('notifications', 'activity');
	    $db = $notificationsTable->getAdapter();
	    $db->beginTransaction();
	
	    try {
	      $notification = Engine_Api::_()->getItem('activity_notification', $action_id);
	      if( $notification ) {
	        $notification->read = 1;
			$notification->mitigated = 1;
	        $notification->save();
	      }
	      // Commit
	      $db->commit();
	    } catch( Exception $e ) {
	      $db->rollBack();
	      throw $e;
	    }
  }

  public function displayFeedAction()
  {
  	if($_SESSION['data'])
  		echo $_SESSION['data'];
	exit;
  }
  public function hideAction()
  {
	$notificationTb = Engine_Api::_()->getDbtable('notifications', 'activity');	
	$select = $notificationTb->select()->where("notification_id = ?",$this->_getParam('id') );
    $notification = $notificationTb->fetchRow($select);
	
	
	
	$notification->advnotification = true;
	$notification->save();
	echo $this->_getParam('id') ;exit;
  }
  public function soundSettingsAction()
  {
  	
	if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('ynnotification.sound.setting') ) {
     	$this->_forward('requireauth', 'error', 'core');

    }
	
  $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id )
    {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    }
    else
    {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }
 	
    $viewer = Engine_Api::_()->user()->getViewer();
	
    // Set up navigation
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
  	
  	$this->view->form = $form = new Ynnotification_Form_Sound();
  	if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
  	{
  		$values = $form->getValues();  	
  		
  	
  		Engine_Api::_()->getApi('settings', 'core')->setSetting('ynnotification.user'.$viewer->getIdentity().'sound.setting', $values['ynnotification_user_sound_setting']);
  	
  	
  		$form->addNotice('Your changes have been saved.');
  	}
  }
}
