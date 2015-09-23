<div class="yntheme-event-container">
    <?php if ($this->limit > 6 && $this->events->getTotalItemCount() > 6) : ?>    
        <div class="yntheme-popular-event-nav">
            <span class="yntheme-event-control-prev">&lsaquo;</span>
            <span class="yntheme-event-control-next">&rsaquo;</span>
        </div>
    <?php endif; ?>
    <div class="yntheme-popular-event row clearfix">
        <div class="yntheme-popular-event-content">        
        <ul>        
    	<?php 
            $i_event = 1;
            foreach($this -> events as $event):
                if ($i_event == 7) { echo "</ul><ul>"; }
                ?>
            
    		<li class="popular-event-item col-md-4 col-xs-6">
                <a href="<?php echo $event -> getHref();?>" title="<?php echo $event -> getTitle();?>" class="popular-event-title"><?php echo $event -> getTitle();?></a>
                <div class="popular-event-description">
                    <span class="popular-event-date"><i class="ynicon-time-w"></i> 
                        <?php 
                            $start_time = strtotime($event -> starttime);
        					$oldTz = date_default_timezone_get();
        					if($this->viewer() && $this->viewer()->getIdentity())
        					{
        						date_default_timezone_set($this -> viewer() -> timezone);
        					}
        					else 
        					{
        						date_default_timezone_set( $this->locale() -> getTimezone());
        					}
                            echo date("M j, Y", $start_time); 
                        ?>
                    </span>
                    <?php if($event -> location || ($this -> event_active == 'ynevent' && $event -> address)):
                    	$location = $event -> location;
                    	if($this -> event_active == 'ynevent')
                    	{
                    		if($event -> address)
                    			$location = $event -> address;
							
                    	}?>
                    	<span class="popular-event-host" title="<?php echo $location;?>"><i class="ynicon-location-w"></i> <?php echo $location;?></span><?php endif; ?>
                </div>  
                <a href="<?php echo $event -> getHref();?>" class="popular-event-image">
                	 <?php $imgUrl = $event -> getPhotoUrl();
                	if(!$imgUrl)
                		$imgUrl = 'application/modules/Ynresponsiveevent/externals/images/nophoto_event_thumb_main.png';?>
                    <span class="popular-event-image-span" style="background-image: url('<?php echo $imgUrl;?>'); "></span>
                </a>
                <div class="popular-event-content"><?php echo $this -> string() -> truncate(strip_tags($event -> description),100);?></div>
                <a href="<?php echo $event -> getHref();?>" class="popular-event-more"><?php echo $this -> translate("More"); ?></a>
    		</li>
            
    	<?php             
                $i_event ++; 
            endforeach;?>
        </ul>
        </div>             
    </div>      
</div>

<script type="text/javascript">
    jQuery.noConflict();
    
    jQuery('.yntheme-popular-event-nav span').click(function(){
        var pe_content = jQuery('.yntheme-popular-event-content');
        
        if ( pe_content.hasClass('nav-next') ) {
            pe_content.removeClass('nav-next');  
        } else {
            pe_content.addClass('nav-next');
        }
    });
</script>