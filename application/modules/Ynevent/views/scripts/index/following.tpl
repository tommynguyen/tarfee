<script type="text/javascript">
  en4.core.runonce.add(function()
  {
   if($('text'))
    {
      new OverText($('text'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
</script>

<div class='generic_layout_container layout_middle'>
    <div class='generic_layout_container layout_ynevent_my_manage'>
	<?php if (count($this->paginator) > 0): ?>
    <ul class='ynevents_browse events_browse'>
        <?php foreach ($this->paginator as $event): if(!$event->getIdentity()){continue;}?>
        <li>
            <div class="ynevents_photo events_photo">
                <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
            </div>
            <div class="ynevents_info events_info">
                <div class="ynevents_title events_title">
                    <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
                </div>
                <div class="ynevents_members events_members">
                    <?php echo $this->locale()->toDateTime($event->starttime) ?>
                </div>
                <div class="ynevents_members events_members">
                    <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
                    <?php echo $this->translate('led by') ?>
                    <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
                </div>
                <?php if ($event->repeat_group > 0) : ?>
		          <div class="ynevents_members ynevents_title_repeat_type">
		          		<?php
		          		$nextEvent = $event->getNextEvent();
		          		if (is_object($nextEvent)) : ?>
		          			<img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/types/event.png" style="margin-top: 3px;"/>
		          			<a href="<?php echo $nextEvent->getHref();?>" title="<?php echo $this->translate('go to next event'); ?>" target="blank" style="margin-right: 10px;"><?php echo $this->translate('Next event'); ?></a>
						<?php endif;?>   
						<span title="<?php echo $this->translate('repeat type');?>"><?php echo $repeateType[$event->repeat_type]; ?></span>       	
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
    </ul>

    <?php if ($this->paginator->count() > 1): ?>
    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
    'query' => array('view' => $this->view, 'text' => $this->text)
    ));
    ?>
    <?php endif; ?>
	<?php else: ?>
<div class="tip">
    <span>
    	<?php if ($this->view == '2'): ?>
    		<?php echo $this->translate('You have not led any events yet.'); ?>
    	<?php else: ?>
        	<?php echo $this->translate('You have not joined any events yet.'); ?>
        <?php endif;?>
        
        <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>') ?>
        <?php endif; ?>
    </span>
</div>
<?php endif; ?>
</div>


</div>


