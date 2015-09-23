<?php if ($this->message):?>
	<div class="tip">
    	<span>
    		<?php echo $this->message;?>
    	</span>
	</div>
<?php endif;?>
<button style="width: 100%; margin-bottom: 20px;" onclick="window.location.assign('<?php echo '?'. http_build_query(array('m'=>'lite','module'=>'ynevent','name'=>'googlecal', 'event_id' => $this->event->getIdentity())); ?>')">
	<?php echo $this->translate("Add to Google Calendar"); ?>
</button>
