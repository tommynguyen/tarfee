<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
class Ynvideo_Plugin_Adapter_Embed extends Ynvideo_Plugin_Adapter_Abstract {
	 public function compileVideo($params) 
	 {
	     $video_id = $params['video_id'];
         $code = $params['code'];
         $view = $params['view'];
         $videoEmbedded = "";
         if($code)
         {
            $videoFrame = $video_id."_".$params['count_video'];
            $videoEmbedded = '<iframe
                title="Embed video player"
                id="videoFrame' . $videoFrame . '"
                class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
                            'src="'. $code .'"
                frameborder="0"
                allowfullscreen=""
                scrolling="no">
               </iframe>';
         }
         return $videoEmbedded;
     }
     public function isValid() 
     {
        if (array_key_exists('link', $this->_params)) 
        {
            preg_match('/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/', $this->_params['link'], $matches);
            if(count($matches) > 2)
            {
                return true;
            }
        }
        return false;
    }
    public function getVideoThumbnailImage()
    {    
    }

    public function getVideoLargeImage(){    
    }

    public function getVideoDuration(){    
    }

    public function getVideoTitle(){    
    }
    
    public function getVideoDescription(){    
    }
    
    public function fetchLink(){    
    }
    
}