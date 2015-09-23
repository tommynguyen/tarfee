<div class="ynevent_promote_wrapper">
	<div class="ynevent_promote_code">
		<h3><?php echo $this->translate("Event Box Code")?></h3>
		<textarea readonly="readonly" class="ynevent_box_code" id="box_code"><iframe src="<?php echo Engine_Api::_()->ynevent()->getCurrentHost() . $this->url(array('action' => 'event-badge', 'event_id' => $this->event->getIdentity(), 'status' => 111), 'event_general'); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:245px;" allowTransparency="true"></iframe></textarea>
		<h3><?php echo $this->translate("Options to show")?>:</h3>
		<input checked="true" type="checkbox" onchange="changeName(this)" onclick="changeName(this)" /> <?php echo $this->translate("Event Name")?> <br />
		<input checked="true" type="checkbox" onchange="changeAttending(this)" onclick="changeAttending(this)" /> <?php echo $this->translate("Attending")?> <br />
		<input checked="true" type="checkbox" onchange="changeLedName(this)" onclick="changeLedName(this)" /> <?php echo $this->translate("Led Name")?>
	</div>

	<div class="ynevent_review">
		<div class ='ynevent_photo_col_right'>
			<a target="_blank" href="<?php echo $this->event->getHref()?>"><?php echo $this->itemPhoto($this->event, 'thumb.profile') ?></a>
		</div>
		<?php echo $this->htmlLink($this->event->getHref(), $this->string()->truncate($this->event->getTitle(), 28), array('title' => $this->string()->stripTags($this->event->getTitle()), 'target'=> '_blank', 'id' => 'promote_event_name', 'class' => 'ynevent_title')) ?>
		<p class="ynevent_owner_stat" id="promote_event_attending">
				<?php 
					echo $this->translate(array('%s attendee','%s attendees',$this->event->member_count),$this->event->member_count);
				?>
		</p>
		<p class="ynevent_owner_stat" id="promote_event_led">
			<?php echo $this->translate("Led by");?>
			<a target="_blank" href="<?php echo $this->event->getOwner()->getHref()?>"><?php echo $this->event->getOwner()->getTitle();?> </a>
		</p>
		
		<p class="ynevent_description">
			<?php echo $this->string()->truncate($this->string()->stripTags($this->event->brief_description), 115);?>
		</p>
	</div>
</div>


<script type="text/javascript">
    var name = '1';
    var attending = '1';
    var led = '1';
    var status = '111';
    
	var changeName = function(obj)
	{
		if($('promote_event_name') !== null && $('promote_event_name') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_event_name').hide();
				name = '0';
			}
			else
			{
				$('promote_event_name').show();
				name = '1';
			}
		}
		status = name + attending + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynevent()->getCurrentHost() . $this->url(array('action' => 'event-badge', 'event_id' => $this->event->getIdentity()), 'event_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
	
	var changeAttending = function(obj)
	{
		if($('promote_event_attending') !== null && $('promote_event_attending') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_event_attending').hide();
				attending = '0';
			}
			else
			{
				$('promote_event_attending').show();
				attending = '1';
			}
		}
		status = name + attending + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynevent()->getCurrentHost() . $this->url(array('action' => 'event-badge', 'event_id' => $this->event->getIdentity()), 'event_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};

	var changeLedName = function(obj)
	{
		if($('promote_event_led') !== null && $('promote_event_led') !== undefined)
		{
			if(obj.checked == false)
			{
				$('promote_event_led').hide();
				led = '0';
			}
			else
			{
				$('promote_event_led').show();
				led = '1';
			}
		}
		status = name + attending + led;
		var html = '<iframe src="<?php echo Engine_Api::_()->ynevent()->getCurrentHost() . $this->url(array('action' => 'event-badge', 'event_id' => $this->event->getIdentity()), 'event_general'); ?>/status/' + status + '" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:215px; height:490px;" allowTransparency="true"></iframe>';
		$('box_code').value = html;
	};
</script>