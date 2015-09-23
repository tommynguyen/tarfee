window.addEvent('load', function() {
	var type = $('type').get('value');
	if (type == 'week') {
		$('weekDay-wrapper').show();
		$('dayHour-wrapper').hide();
	}
	else {
		$('weekDay-wrapper').hide();
		$('dayHour-wrapper').show();
	}
	
	$('type').addEvent('change', function () {
		var type = this.get('value');
		if (type == 'week') {
			$('weekDay-wrapper').show();
			$('dayHour-wrapper').hide();
		}
		else {
			$('weekDay-wrapper').hide();
			$('dayHour-wrapper').show();
		}
	});
});