  <?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <?php if( $this->parent_type !== 'group' ) { ?>
	<div class="headline">
	  <h2>
	    <?php echo $this->translate('Events') ?>
	  </h2>
	  <div class="tabs">
	    <?php
	      // Render the menu
	      echo $this->navigation()
	        ->menu()
	        ->setContainer($this->navigation)
	        ->render();
	    ?>
	  </div>
	</div>
<?php } ?>
  <?php }
  else
  {?>
  	<div id='tabs'>
	  	<ul class="ymb_navigation_more">
		  <?php 
		  $max = 3;
		  $count = 0;
		  foreach( $this->navigation as $item ): $count ++;
		  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
	        'reset_params', 'route', 'module', 'controller', 'action', 'type',
	        'visible', 'label', 'href'
	        )));
		    if($count <= $max):?>
		     <li<?php echo($item->active?' class="active"':'')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        	</li>	
		  <?php endif; endforeach; ?>
		  <?php if(count($this->navigation) > $max):?>
		  <li class="ymb_show_more_menus">
		  	<a href="javascript:void(0)" class="ymb_showmore_menus">
		  		<i class="icon_showmore_menus">
		  			<?php echo $this-> translate("Show more");?>
		  		</i>	  		  		
		  	</a>
		  	<div class="ymb_listmore_option">
		  		<div class="ymb_bg_showmore">
		  			<i class="ymb_arrow_showmore"></i>
		  		</div>	  		
			<?php 
			 	$count = 0;
				foreach( $this->navigation as $item ): $count ++;
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				if($count > $max):
			?>
				<div<?php echo($item->active?' class="active"':'')?>>
				     <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
				 </div>
				 <?php endif; endforeach; ?>
			</div>
		  </li>
		  <?php endif;?>
		</ul>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_menus').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
			})
		});
	</script>
  <?php  }?>
  
  <ul class='ynevents_browse events_browse'>
  	<li>
  		<h3>
			<?php echo $this->selected_day;?>
		</h3>
  	</li>
  	<?php if( count($this->events) > 0 ): ?>
    <?php foreach( $this->events as $event ): ?>
      <li>
        <div class="ynevents_photo events_photo">
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class="ynevents_options events_options">
        </div>
        <div class="ynevents_info events_info">
          <div class="ynevents_title events_title ">
            <h3>
            	<?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
            </h3>
          </div>
      <div class="ynevents_members">
        <?php echo $this->locale()->toDateTime($event->starttime) ?>
      </div>
          <div class="ynevents_members">
            <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
            <?php echo $this->translate('led by') ?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </div>
          <?php if ($event->repeat_type > 0) : ?>
          <div class="ynevents_members ynevents_title_repeat_type">
          		<?php
          		$nextEvent = $event->getNextEvent();
          		if (is_object($nextEvent)) : ?>
          			<img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/types/event.png" style="margin-top: 3px;"/>
          			<a href="<?php echo $nextEvent->getHref();?>" title="<?php echo $this->translate('go to next event'); ?>" target="blank" style="margin-right: 10px;"><?php echo $this->translate('Next event'); ?></a>
				<?php endif;?>   
				<span title="<?php echo $this->translate('repeat type');?>"><?php echo $repeateType[(string)$event->repeat_type]; ?></span>       	
          </div>
          <?php endif;?>
          <div class="ynevents_desc events_desc">
            <?php
            	if(trim($event->brief_description) != "")
					echo $event->brief_description;
				else 
            		echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    <?php else:?>
	 <div class="tip">
	    <span>
	    	<?php echo $this->translate('There were no events found matching your search criteria.') ?>
	    </span>
  </div>
      <?php endif;?>
  </ul>
