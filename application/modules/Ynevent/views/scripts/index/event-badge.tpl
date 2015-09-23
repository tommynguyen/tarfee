<div class="ynevent_review">
		<div class ='ynevent_photo_col_right'>
			<a target="_blank" href="<?php echo $this->event->getHref()?>"><?php echo $this->itemPhoto($this->event, 'thumb.profile') ?></a>
		</div>
		<?php if ($this->name == 1) : ?>
			<?php echo $this->htmlLink($this->event->getHref(), $this->string()->truncate($this->event->getTitle(), 28), array('title' => $this->string()->stripTags($this->event->getTitle()), 'target'=> '_blank', 'id' => 'promote_event_name', 'class' => 'ynevent_title')) ?>
		<?php endif;?>
		<?php if ($this->attending == 1) : ?>
		<p class="ynevent_owner_stat" id="promote_event_attending">
				<?php 
					echo $this->translate(array('%s attendee','%s attendees',$this->event->member_count),$this->event->member_count);
				?>
		</p>
		<?php endif;?>
		<?php if ($this->led == 1) : ?>
		<p class="ynevent_owner_stat" id="promote_event_led">
			<?php echo $this->translate("Led by");?>
			<a target="_blank" href="<?php echo $this->event->getOwner()->getHref()?>"><?php echo $this->event->getOwner()->getTitle();?> </a>
		</p>
		<?php endif;?>
		<p class="ynevent_description">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->event->brief_description), 115);?>
		</p>
</div>