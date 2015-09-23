<?php ?>

<?php if ($this->viewer_id): ?>

<script type="text/javascript">
          en4.core.runonce.add(function(){
               $$('#follow_options input[type=radio]').addEvent('click', function(){
                    var option_id = this.get('value');
                    $('ynevent_follow_radio_' + option_id).className = 'ynevent_radio_loading';
                    new Request.JSON({
                        url: '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'widget', 'action' => 'profile-follow', 'subject' => $this->subject()->getGuid()), 'default', true); ?>',
                        method: 'post',
                        data : {
                        	format: 'json',
                            'event_id': <?php echo $this->subject()->event_id ?>,
                            'option_id' : option_id
                        },
                        onComplete: function(responseJSON, responseText) {
                            $('ynevent_follow_radio_' + option_id).className = 'ynevent_radio';
                            $$('#follow_options input').each(function(radio){
                            	if (radio.type == 'radio') {
                                	radio.style.display = null;
                                    radio.blur();
								}
							});
                            if (responseJSON.error) {
                            	alert(responseJSON.error);
							}
                         }
                    }).send();
               });
          });
     </script>
<h3>
	<?php echo $this->translate('Follow'); ?>
</h3>
<form class="ynevent_follow_form" action="<?php echo $this->url() ?>"
	method="post" onsubmit="return false;">
	<div class="ynevents_follow" id="follow_options">
		<div class="ynevent_follow_radio" id="ynevent_follow_radio_1">
			<input id="follow_option_1" type="radio" class="follow_option"
				name="follow_options" <?php if ($this->follow == 1): ?>
				checked="true" <?php endif; ?> value="1" />
			<?php echo $this->translate('Follow'); ?>
		</div>
		<div class="ynevent_follow_radio" id="ynevent_follow_radio_0">
			<input id="follow_option_0" type="radio" class="follow_option"
				name="follow_options" <?php if ($this->follow == 0): ?>
				checked="true" <?php endif; ?> value="0" />
			<?php echo $this->translate('Unfollow'); ?>
		</div>
	</div>
</form>

<?php endif; ?>
