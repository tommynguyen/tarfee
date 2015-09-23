<?php
/**
 * Socialloft
 *
 * @category   Application_Extensions
 * @package    Advsubscription
 * @copyright  Copyright 2012-2012 Socialloft Developments
 * @author     Socialloft developer
 */

class Sladvsubscription_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	      ->getNavigation('sladvsubscription_admin_main', array(), 'sladvsubscription_admin_main_settings');
	
	    $this->view->form  = $form = new Sladvsubscription_Form_Admin_Global();
	    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
	    {
	      $values = $form->getValues();
	      $array_ignore = array('most_popular_file','ticker_image_file','x_image_file');
	      foreach ($values as $key => $value){
	      	if (!in_array($key, $array_ignore))
	        	Engine_Api::_()->getApi('settings', 'core')->setSetting('advsubscription.'.$key, $value);
	      }
		  if( !empty($values['ticker_image_file']) ) {
		  	$url = $this->savePhoto($form->ticker_image_file,24);
		  	if ($url)
		  	{
	      		Engine_Api::_()->getApi('settings', 'core')->setSetting('advsubscription.ticker_image_link', $url);
	      		$form->ticker_image_link->setValue($url);
		  	}
	      }
	      
	     if( !empty($values['x_image_file']) ) {
		  	$url = $this->savePhoto($form->x_image_file,24);
		  	if ($url)
		  	{
	      		Engine_Api::_()->getApi('settings', 'core')->setSetting('advsubscription.x_image_link', $url);
	      		$form->x_image_link->setValue($url);
		  	}
	      }
	      
	      if( !empty($values['most_popular_file']) ) {
		  	$url = $this->savePhoto($form->most_popular_file,46);
		  	if ($url)
		  	{
	      		Engine_Api::_()->getApi('settings', 'core')->setSetting('advsubscription.most_popular_icon', $url);
	      		$form->most_popular_icon->setValue($url);
		  	}
	      }
	      
	      $form->addNotice('Your changes have been saved.');
	    }
	}
	
	public function savePhoto($photo,$height)
	{		
		if( $photo instanceof Zend_Form_Element_File ) {
	      $file = $photo->getFileName();
	    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
	      $file = $photo['tmp_name'];
	    } else if( is_string($photo) && file_exists($photo) ) {
	      $file = $photo;
	    } else {
	      return;
	    }
	
	    $name = basename($file);
	    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
	    
	    // Save
	    $storage = Engine_Api::_()->storage();
	    
	    $image = Engine_Image::factory();
        $image->open($file)
          ->resize($height, $height)
          ->write($path.'/m_'.$name)
          ->destroy();

        $thumbFileRow = Engine_Api::_()->storage()->create($path.'/m_'.$name, array(
          'parent_type' => 'advsubscription',
          'parent_id' => 1
        ));
        unlink($path.'/m_'.$name);
        return $thumbFileRow->storage_path;
        
	}
	
	public function orderAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
	      ->getNavigation('sladvsubscription_admin_main', array(), 'sladvsubscription_admin_main_order');
	    $this->view->levels = $levels = Engine_Api::_()->sladvsubscription()->getLevels(true);
	}
	public function orderAjaxAction()
	{
		$table = Engine_Api::_()->getDbtable('levels', 'authorization');
		$inputs = $this->_getAllParams();
		foreach ($inputs as $id=>$value)
		{
			if (is_numeric($id) && $level = $table->find($id)->current())
			{
			 	if( $level->type == 'public' || $level->type == 'admin' || $level->type == 'moderator' ) {
		        	continue;
		      	}
				$level->order = $value;
				$level->save();
			}
		}
		die('ok');
	}
}
