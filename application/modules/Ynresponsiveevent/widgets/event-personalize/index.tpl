<div class="event-personalize-title">
    <span data-toggle="collapse" data-target=".event-personalize-main"></span>
    <?php echo $this -> translate("Me")?>
</div>
<ul class="event-personalize-main in">
	<li <?php if($this -> owner) echo "class='active'"; ?>>
		<a href="<?php echo $this -> url(array('owner' => $this -> viewer_id), 'ynresponsive_event_listtng', true)?>"><?php echo $this -> translate("Organizing")?></a>
	</li>
	<li <?php if($this -> type == 'attending') echo "class='active'"; ?>>
		<a href="<?php echo $this -> url(array('type' => 'attending'), 'ynresponsive_event_listtng', true)?>"><?php echo $this -> translate("Attending")?></a>
	</li>
	<li <?php if($this -> type == 'maybe-attending') echo "class='active'"; ?>>
		<a href="<?php echo $this -> url(array('type' => 'maybe-attending'), 'ynresponsive_event_listtng', true)?>"><?php echo $this -> translate("Maybe Attending")?></a>
	</li>
	<li <?php if($this -> type == 'invited') echo "class='active'"; ?>>
		<a href="<?php echo $this -> url(array('type' => 'invited'), 'ynresponsive_event_listtng', true)?>"><?php echo $this -> translate("Invited")?></a>
	</li>
</ul>