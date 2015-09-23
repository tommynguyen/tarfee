
<?php if ( !$this->event->latitude && !$this->fullAddress) : ?>
	<div class="tip"><span><?php echo $this->translate('No location was set'); ?></span></div>
<?php else :?>
	<?php if($this->fullAddress) : ?>
		<?php echo $this->translate("Address")?>: <?php echo $this->fullAddress; ?>
	<?php endif; ?>

	<?php echo $this->htmlLink(
		array('route' => 'event_specific','action'=> 'direction', 'event_id' => $this->event->getIdentity(), 'tab' => $this->identity), 
		$this->translate("Get Direction"), 
		array('class' => 'buttonlink smoothbox')); ?>

	<br /><br />
	
	
	<?php if ($this->event->latitude && $this->event->longitude ): ?>
		<div id="ynevent_google_map_component" style="height: 500px;"></div>
		<iframe id='ynevent_google_map_component_iframe'style="max-height: 500px; display: none;" > </iframe>
		<script type="text/javascript">
		   
		   var ynevent_view_map_time = function()
	       {
	       		var html =  '<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->event->getIdentity()), 'event_general') ?>';
	       		document.getElementById('ynevent_google_map_component_iframe').dispose();
	       		var iframe = new IFrame({
	       			id : 'ynevent_google_map_component_iframe',
	       			src: html,
				    styles: {			       
				        'height': '500px',
				        'width' : '100%'
				    },
				});
	       		iframe.inject($('ynevent_google_map_component'));
	       		document.getElementById('ynevent_google_map_component_iframe').style.display = 'block';
	        }
			 en4.core.runonce.add(function()
			 {
				$$('li.tab_layout_ynevent_profile_map a').addEvent('click', function(){
					ynevent_view_map_time();
				});
			});
    	   ynevent_view_map_time();
		   
		</script>
	<?php endif;?>
<?php endif;?>

