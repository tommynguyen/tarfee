<?php echo $this->form->setAttrib('id', 'user_form_settings_active')->render($this) ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		$('cancel').set('onclick', '');
		var url = '<?php echo $this->url(array(), 'user_logout', true)?>';
		$('cancel').set('href', url);	
	})	
</script>