<h3> <?php echo $this->translate("Event of the day")?></h3>
  <div class="photo">
    <?php echo $this->htmlLink($this->event->getHref(), $this->itemPhoto($this->event, 'thumb.normal'), array('class' => 'thumb', 'title' => $this->event->brief_description)) ?>
  </div>
  <div class="info">
    <div class="title">
      <?php echo $this->htmlLink($this->event->getHref(), $this->event->getTitle()) ?>
    </div>
    <div class="stats">
    	<ul class="events_browse">
    		<li>
	    		<div class="events_members">
	    			<?php 
	    			$startDateObject = new Zend_Date(strtotime($this->event->starttime));
					if( $this->viewer() && $this->viewer()->getIdentity() ) {
						$tz = $this->viewer()->timezone;
						$startDateObject->setTimezone($tz);
					}
	    			echo $this->translate('%1$s %2$s',
			            $this->locale()->toDate($startDateObject, array('size' => 'long')),
			            $this->locale()->toTime($startDateObject)
		          	);
					?>
	    		</div>
	    		<div class="events_members">
	    			<?php echo $this->translate(array('%s guest', '%s guests', $this->event->member_count), $this->locale()->toNumber($this->event->member_count)) ?> | <?php echo $this->translate(array('%s view', '%s views', $this->event->view_count), $this->locale()->toNumber($this->event->view_count)) ?>
	    		</div>
    		</li>
    	</ul>
    </div>
  </div>
