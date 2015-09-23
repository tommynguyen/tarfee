<?php echo $this -> form -> render() ;?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		var type = $('move_type').get('value');
		if (type == 'group') $('move_to-wrapper').hide();
		else $('move_to-wrapper').show();
		
		$('move_type').addEvent('change', function() {
			var type = this.get('value');
			if (type == 'group') $('move_to-wrapper').hide();
			else $('move_to-wrapper').show();
		});	
	});
	
	function removeSubmit()
	  {
	    $('buttons-wrapper').hide();
	  }
</script>