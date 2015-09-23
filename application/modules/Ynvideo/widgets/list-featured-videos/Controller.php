<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Widget_ListFeaturedVideosController extends Engine_Content_Widget_Abstract 
{
    public function indexAction() 
    {
        $slidingDuration = $this->_getParam('slidingDuration', 5000);
        $slideWidth = $this->_getParam('slideWidth', 530);
        $slideHeight = $this->_getParam('slideHeight', 340);
				$this->view->slideWidth = $slideWidth;
	      $this->view->slideHeight = $slideHeight;
		
				$videoTable = Engine_Api::_()->getDbTable('videos', 'ynvideo');
        $select = $videoTable->select();
        $select->order(new Zend_Db_Expr(('rand()')));
        $select->where('featured = 1');
        
        $videos = $videoTable->fetchAll($select);
        
        if ($videos->count() == 0) 
        {
            return $this->setNoRender();
        } 
        
        Engine_Api::_()->ynvideo()->fetchVideoLargeThumbnail($videos);
        $session = new Zend_Session_Namespace('mobile');
				
				if ($session -> mobile)
				{
						$this -> view -> html_mobile_slideshow = $this -> view -> partial('_m_slideshow.tpl', 'ynvideo', array('videos' => $videos));
						
				}
				else 
				{
					if (defined('YNRESPONSIVE'))
					{
						$background_image = $this -> _getParam('background_image', null);

						if (!$background_image)
						{
							$background_image = $this -> view -> layout() -> staticUrl . 'application/themes/ynresponsive1/images/slideshow_bg.jpg';
						}
						$this -> view -> html_ynresponsive_slideshow = $this -> view -> partial('_responsive_slideshow.tpl', 'ynvideo', array(
								'items' => $videos,
								'show_title' => true,
								'show_description' => true,
								'background_image' => $background_image,
								'height' => 274,
								'slider_id' => '_' . uniqid()
							));
					}
					$this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl').'application/modules/Ynvideo/externals/scripts/slides.min.jquery.js');
        	$this->view->headScript()->appendScript("jQuery(document).ready(function() {jQuery('#slides').slides({preload: true, play: $slidingDuration, hoverPause: true})})");
        	$this->view->headLink()->appendStylesheet(Zend_Registry::get('StaticBaseUrl').'application/modules/Ynvideo/externals/styles/jquery-slides.css');        

	        $this->view->videos = $videos;
				}
    }
}
