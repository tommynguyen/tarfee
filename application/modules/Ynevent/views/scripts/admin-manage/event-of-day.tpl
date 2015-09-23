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
				d = date.getDate();
				m = date.getMonth() + 1;
				y = date.getFullYear();
				url = en4.core.baseUrl +'admin/ynevent/manage/get-day/event_id/'+ <?php echo $this->event_id?>;
				
				getDay = new Request.JSON({
					'format' : 'json',
					'url' : url,
					'data' : {
						d : d,
						m : m,
						y : y
					},
					'onSuccess' : function(json, text) 
					{
						alert('<?php echo $this -> translate("This event was added to event of the day!")?>');
						parent.location.reload();
						parent.Smoothbox.close();	
					}
				});
				getDay.send();
			},
			
			inject : 'ynevent_calendar_input',
			fnAfterRender: function(date)
			{
				if (!((date.getMonth() + 1) == <?php echo $this -> month?> && date.getFullYear() == <?php echo $this->year;?>) )
				{
					var td = $('yndc-' + date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate());
					if (td != null)
						td.removeClass("selected");
				}
			    en4.ynevent.updateEventOfDayGeneralCalendar(date.getMonth() + 1, date.getFullYear());
				
			    return false;
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
	});
</script>