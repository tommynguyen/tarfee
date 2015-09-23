<div id="ynevent_calendar_input">
	<input type="hidden" value="<?php echo date('Y-m-d') ?>" id="ynevent_calendar_input_widget" name="ynevent_calendar_input_widget">
</div>
<script type="text/javascript">
	en4.core.runonce.add(function() {
	    var now =  new Date();
	    var y1  = now.getFullYear() - 9;
	    var y2  = now.getFullYear() + 9;
		var picker = new Picker.Date('ynevent_calendar_input_widget', {
			format : '%d-%m-%Y',
			pickerClass : 'datepicker_jqui',
			startView : 'days',
			draggable : false,
			yearPicker : false,
			startDay : 0,
			invertAvailable : true,
			showOnInit : true,
			minDate: new Date(y1,1,1),
			maxDate: new Date(y2,11,30),
			onSelect : function(date) {
				//window.location.href = en4.core.baseUrl + 'events/upcoming?selected_day=' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
				url = en4.core.baseUrl + 'events/upcoming?selected_day=' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
				window.open(url, '_blank');
				window.focus();
			},
			inject : 'ynevent_calendar_input',
			fnAfterRender: function(date){
				future = <?php echo ($this->future) ? 1: 0; ?>;
				if (future > 0)
					en4.ynevent.removeSelectedClass();
				en4.ynevent.updateEventsToCalendar(<?php echo $this->eventDates; ?>);
				$$(".datepicker_jqui .next").setStyle("display","none");
				$$(".datepicker_jqui .previous").setStyle("display","none");
				picker.titleText.removeEvents('click');
			}
		});
		picker.hide = function() {
		};
		picker.close = function() {
		};
		var date = picker.date = new Date(<?php echo $this->year;?>,<?php echo (int)$this->month-1; ?>,<?php echo (int) $this->day;?>);
		picker.show();
		picker.renderDays(date);
		var ele = picker.toElement();
		ele.style.position = 'relative';
		var t = setTimeout(function(){
			ele.style.position = 'absolute';
		},200);
	});
</script>