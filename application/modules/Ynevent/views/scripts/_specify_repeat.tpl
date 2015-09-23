<?php
$localeObject = Zend_Registry::get('Locale');
$dateLocaleString = $localeObject->getTranslation('short', 'Date', $localeObject);
$dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
$dateLocaleString = strtolower($dateLocaleString);
$dateLocaleString = preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('Y', 'm', 'd'), $dateLocaleString);
$dateLocaleString = str_replace('  ', '/', $dateLocaleString);
?>
<div id="specifys-wrapper" class="form-wrapper">
	<div id="specifys-label" class="form-label">
		<label for="specifys" class="optional"></label>
	</div>
	<div id="specifys-element" class="form-element">
		<button onclick="return addInput();" id="addOptionLink"><?php echo $this->translate("Add") ?></button>
		<div id="specifys" style="padding-top: 10px">
			<?php echo $this->element->getAttrib('contentHtml');?>
		</div>
		<p class="description"><?php echo $this->translate("(Enter up to specifies %s donation amount to available on the page)",Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.instance', 50))?></p>
	</div>
</div>
<script type="text/javascript">
	var ynevent_instance = $$('.amount_input').length;
	var optionParent = $('specifys');
	var addInput = function()
	{
		var s_date = $('spec_start_date-date').value;	
		var s_hour = $('spec_start_date-hour').value;	
		var s_min = $('spec_start_date-minute').value;
		if($('spec_start_date-ampm'))
		{
			var s_ampm = $('spec_start_date-ampm').value;
			if(s_ampm == 'AM')
			{
				if(s_hour == 12)
				{
					s_hour = 0;
				}
			}
			else
			{
				if(s_hour < 12)
				{
					s_hour = parseInt(s_hour) + 12;
				}
			}
		}
		if( s_date =="")
		{
			alert(en4.core.language.translate("Start Date. Please select a date from the calendar."));
			return false;
		}
		var e_date = $('spec_end_date-date').value;
		var e_hour = $('spec_end_date-hour').value;	
		var e_min = $('spec_end_date-minute').value;
		if($('spec_end_date-ampm'))
		{
			var e_ampm = $('spec_end_date-ampm').value;
			if(e_ampm == 'AM')
			{
				if(e_hour == 12)
				{
					e_hour = 0;
				}
			}
			else
			{
				if(e_hour < 12)
				{
					e_hour = parseInt(e_hour) + 12;
				}
			}	
		}
		if( e_date =="")
		{
			alert(en4.core.language.translate("End Date. Please select a date from the calendar."));
			return false;
		}
		if(s_min == 0)
			s_min = "00";
		if(e_min == 0)
			e_min = "00";
		if(s_hour < 10)
		{
			s_hour = "0" + s_hour;
		}
		if(e_hour < 10)
		{
			e_hour = "0" + e_hour;
		}
		
		var dateLocaleString = '<?php echo $dateLocaleString?>';
		var arr_locale = dateLocaleString.split('/');
		var s_arr_date = s_date.split('/');
		var e_arr_date = e_date.split('/');
		
		var s_day = s_arr_date[arr_locale.indexOf('d')];
		var s_month = s_arr_date[arr_locale.indexOf('m')];
		var s_year = s_arr_date[arr_locale.indexOf('Y')];
		
		var e_day = e_arr_date[arr_locale.indexOf('d')];
		var e_month = e_arr_date[arr_locale.indexOf('m')];
		var e_year = e_arr_date[arr_locale.indexOf('Y')];
		
		var str_time_start = s_date + " " + s_hour + ":" + s_min + ":00";
		var str_time_end = e_date + " " + e_hour + ":" + e_min + ":00";
		
		var r_str_time_start = s_day + "/" + s_month + "/" + s_year + " " + s_hour + ":" + s_min + ":00";
		var r_str_time_end = e_day + "/" + e_month + "/" + e_year + " " + e_hour + ":" + e_min + ":00";
		
		var s_time = new Date(s_year, s_month - 1, s_day, s_hour, s_min, 0, 0).getTime();
		var e_time = new Date(e_year, e_month - 1, e_day, e_hour, e_min, 0, 0).getTime();
		
		if(s_time > e_time)
		{
			alert(en4.core.language.translate("End Date must be greater than or equal to Start Date."));
			return false;
		}
		
		var value_specify_date = en4.core.language.translate("From %s - To %s", str_time_start, str_time_end);
		
		if (ynevent_instance != <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.instance', 50)?>)
		{
			var divElement = new Element('div',{
				'id': 'div_' + ynevent_instance,
				'class': 'div_specify_date'
			});
			
			var inputElement = new Element('span', {
		          'id' : 'input_' + ynevent_instance,
		          'style': 'padding-right: 10px',
		          'name': 'specifys[]',
		          'class': 'amount_input',
		          'html': value_specify_date,		         
		        });
		    inputElement.inject(divElement);
		    
		    var inputHiddenElement = new Element('input', {
		          'type': 'hidden',
		          'id' : 'input_start_hidden_' + ynevent_instance,
		          'name': 'input_start_specifys[]',
		          'class': 'amount_input',
		          'value': r_str_time_start,		         
		        });
		    inputHiddenElement.inject(divElement);
		    
		    var inputHiddenElement = new Element('input', {
		          'type': 'hidden',
		          'id' : 'input_end_hidden_' + ynevent_instance,
		          'name': 'input_end_specifys[]',
		          'class': 'amount_input',
		          'value': r_str_time_end,		         
		        });
		    inputHiddenElement.inject(divElement);

		    var removeElement = new Element('a', {
		          'id': 'remove_' + ynevent_instance,
		          'class': 'buttonlink icon_event_delete',
		          'link': true,
		          'href': 'javascript:;',
		          'html': '<?php echo $this->translate("Remove");?>',
		          'onclick': 'removeInput(' + ynevent_instance + ')'
		        });
		    removeElement.inject(divElement);
		    
		    divElement.inject(optionParent);
		    
			ynevent_instance += 1;
			
			$('spec_start_date-date').value = "";
			$('spec_start_date-hour').selectedIndex  = 0;
			$('spec_start_date-minute').selectedIndex  = 0;
			if($('spec_start_date-ampm'))
			{
				$('spec_start_date-ampm').selectedIndex = 0;
			}
			$('calendar_output_span_spec_start_date-date').textContent = '<?php echo $this -> translate("Select a date")?>';
			$('spec_end_date-date').value = "";
			$('spec_end_date-hour').selectedIndex = 0;
			$('spec_end_date-minute').selectedIndex = 0;
			if($('spec_end_date-ampm'))
			{
				$('spec_end_date-ampm').selectedIndex = 0;
			}
			$('calendar_output_span_spec_end_date-date').textContent = '<?php echo $this -> translate("Select a date")?>';
		} 
		else 
		{
			$$('.addOptionLink').set('onclick','javascript:;');
			alert('<?php echo $this -> translate("You only can add ");?>' + '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.instance', 50)?>' + '<?php echo $this -> translate(" specify events!")?>');
		}
		return false;
	}
	var removeInput = function(id)
	{
		if(ynevent_instance > 0)
		{
			var element_input = $('div_' + id);
			
			optionParent.removeChild(element_input);
			ynevent_instance -= 1;
			$$('.addOptionLink').set('onclick','return addInput()');
		}
	}
</script>
