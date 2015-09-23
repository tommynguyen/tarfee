<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Request.js');
?>

<?php echo $this -> form -> render($this); ?>

<script type="text/javascript">
	 
	 function checkValid() {
	 	
	 	//clear error before check agin
	 	$$('.create-campaign-error').destroy();
	 	
	 	//check empty start time & end time
	 	var startDate = $('start_date-date').get('value');
	 	var endDate = $('end_date-date').get('value');
	 	
	 	var startHour = $('start_date-hour').get('value');
	 	var startMinute = $('start_date-minute').get('value');
	 	if($('start_date-ampm'))
	 	{
	 		var startAmpm = $('start_date-ampm').get('value');
	 		if(startAmpm == 'PM' && startHour != 12)
	 		{
	 			startHour = startHour + 12;
	 		}
	 		else if(startAmpm == 'AM' && startHour == 12)
	 		{
	 			startHour = 0;
	 		}
	 	}
	 	
	 	if(startDate == "") {
	 		var message = "<?php echo $this -> translate('start date is required');?>";
	 		var div = new Element('div', {
		       'html': message,
		       'class': 'create-campaign-error',
		        styles: {
			        'color': 'red',
			        'font-weight': 'bold',
			    },
		    });
	 		$('start_date-wrapper').grab(div,'before');
	 		return false;
	 	}
	 	if(endDate == "") {
	 		var message = "<?php echo $this -> translate('end date is required');?>";
	 		var div = new Element('div', {
		       'html': message,
		       'class': 'create-campaign-error',
		        styles: {
			        'color': 'red',
			        'font-weight': 'bold',
			    },
		    });
	 		$('end_date-wrapper').grab(div,'before');
	 		return false;
	 	}
	 	if((startDate != "") && (endDate!= "")) {
	 		var startDateObject  = new Date(startDate);
	 		var endDateObject  = new Date(endDate);
	 		var todayObject = new Date();
	 		
	 		//check startDate greater than now
	 		if(todayObject.getTime() > (startDateObject.getTime() + startHour*3600*1000 + startMinute*60*1000))
			{
		 		var message = "<?php echo $this -> translate('start date must greater than today');?>";
		 		var div = new Element('div', {
			       'html': message,
			       'class': 'create-campaign-error',
			        styles: {
				        'color': 'red',
				        'font-weight': 'bold',
				    },
			    });
		 		$('start_date-wrapper').grab(div,'before');
		 		return false;
		 	} 
	 		
	 		//check period from admin
		 	<?php 
				$settings = Engine_Api::_()->getApi('settings', 'core');
				$period = $settings->getSetting('tfcampaign_max_period', "20");
		 	?>
	 		
		 	//miniseconds
		 	var period = (endDateObject - startDateObject); 
		 	//seconds per hour*hours per day*milisecond
		 	var period_day = (period/(3600*24*1000));
		 	var max_period = "<?php echo $period;?>";
		 	if(period_day > max_period || period_day <= 0) {
		 		var message = "<?php echo $this -> translate('end date must greater than is start date (maximum %s days)', $period);?>";
		 		var div = new Element('div', {
			       'html': message,
			       'class': 'create-campaign-error',
			        styles: {
				        'color': 'red',
				        'font-weight': 'bold',
				    },
			    });
		 		$('end_date-wrapper').grab(div,'before');
		 		return false;
		 	} 
	 	}
	 	
	 	//check age
	 	var from_age = $('from_age').get('value');
	 	var to_age = $('to_age').get('value');
	 	if(from_age != "" && to_age != ""){
	 		if(parseInt(from_age) > parseInt(to_age)) {
	 			var message = "<?php echo $this -> translate('to age must be greater than from age');?>";
		 		var div = new Element('div', {
			       'html': message,
			       'class': 'create-campaign-error',
			        styles: {
				        'color': 'red',
				        'font-weight': 'bold',
				    },
			    });
		 		$('from_age-wrapper').grab(div,'before');
		 		return false;
	 		}
	 	}
	 	
	 	return true;
	 }
	 
	 function removeToValue(id, toValueArray, hideLoc){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById(hideLoc).value = toValueArray.join();
     }
	
	 function removeFromToValue(id, hideLoc, elem) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById(hideLoc).value;
        var toValueArray = toValues.split(",");
        var toValueIndex = "";

        var checkMulti = id.search(/,/);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(recipientsArray[i], toValueArray, hideLoc);
            }
        }
        else{
            removeToValue(id, toValueArray, hideLoc);
        }

        // hide the wrapper for usernames if it is empty
        if (document.getElementById(hideLoc).value==""){
            document.getElementById(hideLoc+'-wrapper').style.height = '0';
            document.getElementById(hideLoc+'-wrapper').hide();
        }

        document.getElementById(elem).style.display = 'block';
    }
    
    // Populate data
    var maxRecipients = 0;
    var to = {
        id : false,
        type : false,
        guid : false,
        title : false
    };
    
    window.addEvent('domready', function() {
    	
        //for owners autocomplete
        new Autocompleter2.Request.JSON('user', '<?php echo $this->url(array('action' => 'suggest-user'), 'user_general', true) ?>', {
            'toValues': 'user_ids',
            'minLength': 1,
            'delay' : 250,
            'autocompleteType' : 'message',
            'multiple': true,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token)
            {
                if(token.type == 'user')
                {
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo,
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
            },
            onPush : function(){
                if((maxRecipients != 0) && (document.getElementById('user_ids').value.split(',').length >= maxRecipients) ){
                    document.getElementById('user').style.display = 'none';
                }
            }
        });
        
        <?php if($this  -> error) :?>
        	$('user_ids').set('value', "");
        <?php endif;?>
        <?php foreach ($this->userViewRows as $userViewRow) : ?>
        	<?php 
        		$user_id = $userViewRow -> user_id;
				$user = Engine_Api::_() -> getItem('user', $user_id);
        	?>
        	<?php if($user -> getIdentity()) :?>
		        var value = $('user_ids').get('value');
		        if(value.trim() == ""){
		        	value += '<?php echo $user->getIdentity()?>';
		        } else {
		        	value += ','+'<?php echo $user->getIdentity()?>';
		        }
		        $('user_ids').set('value', value);
		        
		        var myElement = new Element("span", {
		            'id' : 'user_ids_tospan_' + '<?php echo $user->getIdentity()?>',
		            'class': 'user_tag',
		            'html' :  "<a target='_blank' href='<?php echo $user->getHref()?>'>" + '<?php echo $this->itemPhoto($user, 'thumb.icon')?><?php echo $user->getTitle()?>' + "</a> <a class = 'club_preferred_remove' href='javascript:void(0);' onclick='this.parentNode.destroy();removeFromToValue(\"<?php echo $user->getIdentity()?>\", \"user_ids\",\"user\");'>x</a>"
		        });
		        document.getElementById('user_ids-element').appendChild(myElement);
		        document.getElementById('user_ids-wrapper').show();
		        document.getElementById('user_ids-wrapper').style.height = 'auto';
	        <?php endif;?>
        <?php endforeach; ?>
        
    });
 </script>

<script type="text/javascript">
	
	function privacyChange()
	{
		if($('auth_view'))
		{
			if ($('auth_view').value == 'everyone')
			{
				$('user-wrapper').hide();
				$('user_ids-wrapper').hide();
			}
			else
			{
				$('user-wrapper').show();
				$('user_ids-wrapper').show();
			}
		}
	}
	
	function subCategories()
	{
		if ($('category_id').value == 2)
		{
			$('referred_foot-wrapper').show();
		}
		else
		{
			$('referred_foot-wrapper').hide();
		}
		
		if ($('category_id').value > 0)
		{
			var cat_id = $('category_id').value;
			var makeRequest = new Request(
			{
				url : "user/player-card/subcategories/cat_id/" + cat_id,
				onComplete : function(respone)
				{
					respone = respone.trim();
					if (respone != "")
					{
						$('position_id-wrapper').show();
						document.getElementById('position_id-element').innerHTML = '<select id= "position_id" name = "position_id"><option value="0" label="" selected= "selected"></option>' + respone + '</select>';
					}
					else
						$('position_id-wrapper').hide();
				}
			})
			makeRequest.send();
		}
		else
		{
			$('position_id-wrapper').hide();
		}
	}
	window.addEvent('domready', function() 
	{
		<?php if(!$this -> showPreferredFoot):?>
			$('referred_foot-wrapper').hide();
		<?php endif;?>
		<?php if(!$this -> showPosition):?>
			$('position_id-wrapper').hide();
		<?php endif;?>
		
		if($('auth_view'))
		{
			if ($('auth_view').value == 'everyone')
			{
				$('user-wrapper').hide();
				$('user_ids-wrapper').hide();
			}
			else
			{
				$('user-wrapper').show();
				$('user_ids-wrapper').show();
			}
		}
		
		if ($$('#province_id option').length <= 1)
		{
			$('province_id-wrapper').hide();
		}
		
		if ($$('#city_id option').length <= 1) 
		{
			$('city_id-wrapper').hide();
		}
		
		$('country_id').addEvent('change', function() 
		{
			var id = this.value;
			var makeRequest = new Request({
      			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
      			onComplete: function (respone){
					respone  = respone.trim();
					var options = Elements.from(respone);
                  	if(options.length > 0) {
                  		var option = new Element('option', {
							'value': '0',
							'text': ''
						})  
                    	$('province_id').empty();
                    	$('province_id').grab(option);  
                    	$('province_id').adopt(options);
      					$('province_id-wrapper').show();
      				}
      				else {
      					$('province_id').empty();
      					$('province_id-wrapper').hide();
      				}
      				$('city_id').empty();
  					$('city_id-wrapper').hide();
      			}
      		})
      		makeRequest.send();
		});
		
		$('province_id').addEvent('change', function() 
		{
			var id = this.value;
			var makeRequest = new Request({
      			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
      			onComplete: function (respone){
					respone  = respone.trim();
					var options = Elements.from(respone);
                  	if(options.length > 0) {
                  		var option = new Element('option', {
							'value': '0',
							'text': ''
						})  
                    	$('city_id').empty();
                    	$('city_id').grab(option);
                    	$('city_id').adopt(options);
      					$('city_id-wrapper').show();
      				}
      				else {
      					$('city_id').empty();
      					$('city_id-wrapper').hide();
      				}
      			}
      		})
      		makeRequest.send();
		});
	});
</script>