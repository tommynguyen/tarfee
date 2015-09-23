<div class="yntheme-event-container">
	<div class="yntheme-hot-event clearfix">
    	<?php foreach($this -> events as $event):?>
    		<div class="hot-event-item col-xs-6 col-md-3">
                <a href="<?php echo $event -> getHref();?>" class="hot-event-image">
                	 <?php $imgUrl = $event -> getPhotoUrl();
                	if(!$imgUrl)
                		$imgUrl = 'application/modules/Ynresponsiveevent/externals/images/nophoto_event_thumb_main.png';?>
                    <span class="hot-event-image-span" style="background-image: url('<?php echo $imgUrl;?>'); "></span>
                </a>
                <div class="hot-event-content">
                    <a href="<?php echo $event -> getHref();?>" class="hot-event-title"><?php echo $event -> getTitle();?></a>
                    <div class="hot-event-line"></div>
                    <div class="hot-event-date">
                        <i class="ynicon-time-w"></i>
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
                        echo date("F j, Y", $start_time); ?>
                    </div>
                     <?php if($event -> location || ($this -> event_active == 'ynevent' && $event -> address)):
                    	$location = $event -> location;
                    	if($this -> event_active == 'ynevent')
                    	{
                    		if($event -> address)
                    			$location = $event -> address;
							
                    	}?>
                    	<div class="hot-event-host"><i class="ynicon-location-w"></i> <?php echo $location;?> <br/></div> <?php endif;?>
                    <div class="hot-event-guest"><i class="ynicon-person-w"></i> <?php echo "Guest: ".$this -> translate(array('%s guest', '%s guests', $event -> member_count), $event -> member_count);?> </div>
                </div>	
            </div>	
    	<?php endforeach;?>   
    </div>
</div>