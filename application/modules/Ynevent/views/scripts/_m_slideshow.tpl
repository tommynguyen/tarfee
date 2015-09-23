<div class="callbacks_container">
<ul class="rslides" id="ymb_home_featuredevent">
	
	<?php 
		 foreach ($this->events as $event) 
		{ 		 
	?>
	
	      <li>
	      	<?php 
	             echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.featured'))
	        ?> 
	      	<div class="caption ynevent_albumfeatured_info">
				<div class="ynevent_album_info ynevent_album_title">
						<?php echo $this->htmlLink($event->getHref(), $event->title); ?>
				</div>
				<p class="ynevent_album_info"><?php echo $this->string()->truncate($event->description, 100); ?></p>
				<p class="ynevent_album_info"><?php echo $this->locale()->toDate( $event->starttime, array('size' => 'short'))?></p>
				<div class="ynevent_album_info ynevent_view_more"> <?php echo $this->htmlLink($event->getHref(), $this->translate("View more"));?></div>
			</div>
	      </li>
    <?php } ?>
    
</ul>
</div>

