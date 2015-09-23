<?php if ($this->numberOfEvents > 0) : ?>
<div class="ynevent_promote_code">
	<h3 class="ynevent_promote_calendar_box_code"><?php echo $this->translate("Calendar Box Code")?></h3>
	<textarea readonly="readonly" class="ynevent_box_code ynevent_promote_calendar_box_code" id="box_code"><iframe src="<?php echo Engine_Api::_()->ynevent()->getCurrentHost() . $this->url(array('action' => 'calendar-badge', 'month' => $this->month, 'year' => $this->year), 'event_general'); ?>" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:220px; height:180px;" allowTransparency="true"></iframe></textarea>
</div>
<?php else :?>
	<div class="tip" style="margin-top: 24px; margin-left: 15px;"><span><?php echo $this->translate('There is no event in this month'); ?></span></div>
<?php endif;?>