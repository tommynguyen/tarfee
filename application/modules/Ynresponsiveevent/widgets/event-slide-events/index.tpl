<?php
    $this -> headLink() -> appendStylesheet($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/css/style.css');
    $this -> headLink() -> appendStylesheet($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/rs-plugin/css/settings.css');
    $this -> headScript() -> appendFile($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/rs-plugin/js/jquery.themepunch.plugins.min.js');
    $this -> headScript() -> appendFile($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/rs-plugin/js/jquery.themepunch.revolution.min.js');
    $this -> headScript() -> appendFile($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/rs-plugin/js/jquery-ui-1.8.21.custom.min.js');    
    $this -> headScript() -> appendFile($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/revolution/previewjs/preview-fullwidth.js');
?>
<div class="fullwidthbanner-container">					
    <div class="fullwidthbanner">
    	<ul>
            <?php 
            $rs_array_slide = array(
                array(
                     "transition" => "slotslide-horizontal", 
                     "title" => array("fade", "easeOutExpo"),
                     "description" => array("lfr", "easeOutBack"),
                     "meta" => array("lfl", "easeOutExpo")
                ),
                array(
                     "transition" => "boxslide", 
                     "title" => array("sfr", "easeOutBack"),
                     "description" => array("sfl", "easeOutExpo"),
                     "meta" => array("lft", "easeOutBack")
                ),
                array(
                     "transition" => "slideright", 
                     "title" => array("lfb", "easeOutExpo"),
                     "description" => array("sft", "easeOutBack"),
                     "meta" => array("sfb", "easeOutExpo")
                ),
                array(
                     "transition" => "boxfade", 
                     "title" => array("fade", "easeOutBack"),
                     "description" => array("sfl", "easeOutExpo"),
                     "meta" => array("lft", "easeOutBack")
                ),
                array(
                     "transition" => "slideright", 
                     "title" => array("sfr", "easeOutBack"),
                     "description" => array("sfl", "easeOutExpo"),
                     "meta" => array("fade", "easeOutBack")
                )
            );
            $i_rs_slider = 0;
            if ($this -> events -> getTotalItemCount() > 0) :
	            foreach($this -> events as $item):                
	        		$event = Engine_Api::_() -> getItem('event', $item -> getIdentity());
					$title = $item -> getTitle();
					$description = $item -> description;
					$photo_url = $item -> getPhotoUrl();
					if(!$title)
					{
						$title = $event -> getTitle();
					}
					if(!$description)
					{
						$description = $event -> description;
					}
					if(!$photo_url)
					{
						$photo_url = $event -> getPhotoUrl();
					}
	        		$startDateObject = new Zend_Date(strtotime($event->starttime));
	        		$endDateObject = new Zend_Date(strtotime($event->endtime));
	        		if( $this->viewer() && $this->viewer()->getIdentity() ) 
	        		{
	        			$tz = $this->viewer()->timezone;
	        			$startDateObject->setTimezone($tz);
	        			$endDateObject->setTimezone($tz);  
	        		}?>
	        		<li data-transition="<?php echo $rs_array_slide[$i_rs_slider]["transition"];?>" data-slotamount="10" data-thumb="<?php echo $photo_url;?>">
	                    <img src="<?php echo $item -> getPhotoUrl();?>" />
	                    <div class="caption <?php echo $rs_array_slide[$i_rs_slider]["meta"][0];?> big_white" data-x="400" data-y="120" data-speed="400" data-start="1700" data-easing="<?php echo $rs_array_slide[$i_rs_slider]["title"][1];?>" title="<?php echo strip_tags($title)?>"><a href= "<?php echo $event -> getHref();?>"><?php echo $this -> string() -> truncate(strip_tags($title), 30);?></a></div>
	        			<?php if ($description) : ?>
	        			<div class="caption <?php echo $rs_array_slide[$i_rs_slider]["meta"][0];?> big_orange" data-x="400" data-y="170" data-speed="400" data-start="1900" data-easing="<?php echo $rs_array_slide[$i_rs_slider]["description"][1];?>" title="<?php echo strip_tags($description)?>"><?php echo $this -> string() -> truncate(strip_tags($description),90);?></div>
	                    <?php endif; ?>
	        			<div class="caption <?php echo $rs_array_slide[$i_rs_slider]["meta"][0];?> medium_grey" data-x="400" data-y="240" data-speed="400" data-start="2500" data-easing="<?php echo $rs_array_slide[$i_rs_slider]["meta"][1];?>">
	                        <i class="ynicon-time-w"></i> <?php echo $this->locale()->toDate($startDateObject).' - '.$this->locale()->toDate($endDateObject)?>
	                        <?php if($event -> location || ($this -> event_active == 'ynevent' && $event -> address)):
	                        	$location = $event -> location;
	                        	if($this -> event_active == 'ynevent')
	                        	{
	                        		if($event -> address)
	                        			$location = $event -> address;
									
	                        	}?>
	                        - <i class="ynicon-location-w" title="<?php echo strip_tags($location)?>"></i> <?php echo $this -> string() -> truncate(strip_tags($location), 25);?>
	        			    <?php endif;?>
	        			    - <i class="ynicon-person-w"></i> <?php echo $this -> translate(array('%s guest', '%s guests', $event -> member_count), $event -> member_count);?>
	                    </div>
	        		</li>
	        	<?php $i_rs_slider = ($i_rs_slider+1)%5;                
	            endforeach;
	           else:?>
            	<li data-transition="slotslide-horizontal" data-slotamount="10" data-thumb="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/06/07/06ff_734d.jpg?c=da82" style="visibility: visible; left: 0px; top: 0px; z-index: 16; opacity: 1;">
                    <div class="slotholder"><img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/06/07/06ff_734d.jpg?c=da82" class="defaultimg" style="width: 1313px; height: 603.9287109375px; position: absolute; left: -25px; opacity: 0;"></div>
                    <div class="caption lfl big_white start" data-x="400" data-y="120" data-speed="400" data-start="1700" data-easing="easeOutExpo" title="2014 Venice is Sinking" style="font-size: 30px; padding: 5px 30px 5px 10px; margin: 0px; border: 0px; line-height: 30px; opacity: 1; left: -297px; top: 120px;"><a href="#">2014 Venice is Sinking</a></div>
        			<div class="caption lfl big_orange start" data-x="400" data-y="170" data-speed="400" data-start="1900" data-easing="easeOutBack" title="The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested" style="font-size: 18px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 25px; opacity: 1; left: -405px; top: 170px;">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those inter...</div>
        			<div class="caption lfl medium_grey start" data-x="400" data-y="240" data-speed="400" data-start="2500" data-easing="easeOutExpo" style="font-size: 14px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 20px; opacity: 1; left: -246px; top: 240px;">
                    	<i class="ynicon-time-w"></i> 1/21/14 - 1/31/14 - <i class="ynicon-location-w" title="Venice"></i> Venice - <i class="ynicon-person-w"></i> 2 guests                    
                    </div>
        		</li>
        	    <li data-transition="boxslide" data-slotamount="10" data-thumb="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/08/07/0701_9476.jpg?c=e3f2" style="z-index: 16; visibility: visible; left: 0px; top: 0px; opacity: 1;">
                    <div class="slotholder"><img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/08/07/0701_9476.jpg?c=e3f2" class="defaultimg" style="width: 1313px; height: 712.91796875px; position: absolute; left: -25px; opacity: 0;"></div>
                    <div class="caption lft big_white start" data-x="400" data-y="120" data-speed="400" data-start="1700" data-easing="easeOutBack" title="Tet Holiday 2014" style="font-size: 30px; padding: 5px 30px 5px 10px; margin: 0px; border: 0px; line-height: 30px; opacity: 1; left: 586.5px; top: -45px;"><a href="#">Tet Holiday 2014</a></div>
        			<div class="caption lft big_orange start" data-x="400" data-y="170" data-speed="400" data-start="1900" data-easing="easeOutExpo" title="It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout." style="font-size: 18px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 25px; opacity: 1; left: 586.5px; top: -65px;">It is a long established fact that a reader will be distracted by the readable content of ...</div>
        			<div class="caption lft medium_grey start" data-x="400" data-y="240" data-speed="400" data-start="2500" data-easing="easeOutBack" style="font-size: 14px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 20px; opacity: 1; left: 586.5px; top: -35px;">
                    	<i class="ynicon-time-w"></i> 12/26/13 - 1/31/14 - <i class="ynicon-location-w" title="Vietnam"></i> Vietnam - <i class="ynicon-person-w"></i> 1 guest                    
                    </div>
        		</li>
        	    <li data-transition="slideright" data-slotamount="10" data-thumb="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/02/07/06fb_4a7f.jpg?c=4e8f" style="z-index: 18; visibility: visible; left: 0px; top: 0px; opacity: 1;">
                    <div class="slotholder"><img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/02/07/06fb_4a7f.jpg?c=4e8f" class="defaultimg" style="width: 1313px; height: 738.5625px; position: absolute; left: -25px; opacity: 0;"></div>
                    <div class="caption sfb big_white start" data-x="400" data-y="120" data-speed="400" data-start="1700" data-easing="easeOutExpo" title="Country Living Magazine" style="font-size: 30px; padding: 5px 30px 5px 10px; margin: 0px; border: 0px; line-height: 30px; opacity: 0; left: 586.5px; top: 170px;"><a href="#">Country Living Magazine</a></div>
        			<div class="caption sfb big_orange start" data-x="400" data-y="170" data-speed="400" data-start="1900" data-easing="easeOutBack" title="Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text" style="font-size: 18px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 25px; opacity: 0; left: 586.5px; top: 220px;">Many desktop publishing packages and web page editors now use Lorem Ipsum as their default...</div>
        			<div class="caption sfb medium_grey start" data-x="400" data-y="240" data-speed="400" data-start="2500" data-easing="easeOutExpo" style="font-size: 14px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 20px; opacity: 0; left: 586.5px; top: 290px;">
                    	<i class="ynicon-time-w"></i> 11/28/13 - 12/31/13 - <i class="ynicon-location-w" title="Soc Trang, Viet Nam"></i> Soc Trang, Viet Nam - <i class="ynicon-person-w"></i> 1 guest                    
                    </div>
        		</li>
        		<li data-transition="boxfade" data-slotamount="10" data-thumb="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/04/07/06fd_5407.jpg?c=e09b" style="z-index: 20; visibility: visible; left: 0px; top: 0px; opacity: 1;">
                    <div class="slotholder"><img src="http://se4templates.demo.younetco.com/responsive-event/public/ynresponsive1_event/04/07/06fd_5407.jpg?c=e09b" class="defaultimg" style="width: 1313px; height: 656.5px; position: absolute; left: -25px; opacity: 1;"></div>
                    <div class="caption lft big_white start" data-x="400" data-y="120" data-speed="400" data-start="1700" data-easing="easeOutBack" title="One more Work" style="font-size: 30px; padding: 5px 30px 5px 10px; margin: 0px; border: 0px; line-height: 30px; opacity: 1; left: 586.5px; top: 120px;"><a href="#">One more Work</a></div>
        			<div class="caption lft big_orange start" data-x="400" data-y="170" data-speed="400" data-start="1900" data-easing="easeOutExpo" title="There are many variations of passages of Lorem Ipsum available" style="font-size: 18px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 25px; opacity: 1; left: 586.5px; top: 170px;">There are many variations of passages of Lorem Ipsum available</div>
        			<div class="caption lft medium_grey start" data-x="400" data-y="240" data-speed="400" data-start="2500" data-easing="easeOutBack" style="font-size: 14px; padding: 5px 10px; margin: 0px; border: 0px; line-height: 20px; opacity: 1; left: 586.5px; top: 240px;">
                    	<i class="ynicon-time-w"></i> 10/31/13 - 8/29/14  - <i class="ynicon-location-w" title="FEAR town"></i> FEAR town - <i class="ynicon-person-w"></i> 1 guest 
                    </div>
        		</li>
	           <?php endif;?>
    	</ul>		
    	<div class="tp-bannertimer"></div>												
    </div>					
</div>

<?php if ($this -> events -> getTotalItemCount() > 0) : ?>
<?php $slider_id = '_' . uniqid(); ?>
<div class="flexslider" id="<?php echo $slider_id; ?>">
  <ul class="slides">
    <?php foreach($this->events as $item): 
    	$event = Engine_Api::_() -> getItem('event', $item -> getIdentity());
		$title = $item -> getTitle();
		$description = $item -> description;
		$photo_url = $item -> getPhotoUrl();
		if(!$title)
		{
			$title = $event -> getTitle();
		}
		if(!$description)
		{
			$description = $event -> description;
		}
		if(!$photo_url)
		{
			$photo_url = $event -> getPhotoUrl();
		}
		$startDateObject = new Zend_Date(strtotime($event->starttime));
		$endDateObject = new Zend_Date(strtotime($event->endtime));
		if( $this->viewer() && $this->viewer()->getIdentity() ) 
		{
			$tz = $this->viewer()->timezone;
			$startDateObject->setTimezone($tz);
			$endDateObject->setTimezone($tz);  
		}
		$index = 0;?> 
    <li class="<?php echo ++$index==1?'active':''; ?>">
      <div class="overflow-hidden">
		  <span style="background-image: url(<?php echo $photo_url;?>);"></span>
            <?php if($title): ?>
            <div class="carousel-caption">
              <p><a href="<?php echo $event -> getHref()?>"><?php echo $this -> string() -> truncate(strip_tags($title), 30);?></a></p>
              <p><i class="ynicon-time-w"></i> <?php echo $this->locale()->toDate($startDateObject).' - '.$this->locale()->toDate($endDateObject)?>
               <?php if($event -> location || ($this -> event_active == 'ynevent' && $event -> address)):
                	$location = $event -> location;
                	if($this -> event_active == 'ynevent')
                	{
                		if($event -> address)
                			$location = $event -> address;
						
                	}?>
                - <i class="ynicon-location-w" title="<?php echo strip_tags($location)?>"></i> <?php echo $this -> string() -> truncate(strip_tags($location), 30);?>
			    <?php endif;?>
			    - <i class="ynicon-person-w"></i> <?php echo $this -> translate(array('%s guest', '%s guests', $event -> member_count), $event -> member_count);?></p>
            </div>
            <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
<script type="text/javascript">
jQuery(window).load(function() {
  jQuery('#<?php echo $slider_id; ?>').flexslider({
    animation: "slide"
  });
});
</script>
<?php endif; ?>
<script type="text/javascript">
	jQuery.noConflict();
	if (jQuery.fn.cssOriginal != undefined)
	{
		jQuery.fn.css = jQuery.fn.cssOriginal;
		jQuery('.fullwidthbanner').revolution(
		{	
			delay:9000,												
			startwidth:890,
			startheight:450,
			onHoverStop:"on", // Stop Banner Timet at Hover on Slide on/off
			thumbWidth:100,	// Thumb With and Height and Amount (only if navigation Tyope set to thumb !)
			thumbHeight:50,
			thumbAmount:4,
			hideThumbs:200,
			navigationType:"both",	//bullet, thumb, none, both	 (No Shadow in Fullwidth Version !)
			navigationArrows:"verticalcentered", //nexttobullets, verticalcentered, none
			navigationStyle:"round",	//round,square,navbar
			touchenabled:"on",						// Enable Swipe Function : on/off
			navOffsetHorizontal:0,
			navOffsetVertical:20,
			fullWidth:"on",
			shadow:0		//0 = no Shadow, 1,2,3 = 3 Different Art of Shadows -  (No Shadow in Fullwidth Version !)
		});	
	}					
</script>