<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/AutocompleterExtend.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Local.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/Autocompleter.Request.js');
?>

<?php echo $this -> form -> render($this); ?>

<script type="text/javascript">
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
                    document.getElementById('group').style.display = 'none';
                }
            }
        });
        
    });
 </script>

<script type="text/javascript">
	function showOther()
	{
		if ($('relation_id').value == 0)
		{
			$('relation_other-wrapper').show();
		}
		else
		{
			$('relation_other-wrapper').hide();
		}
	}
	
	function privacyChange()
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

	function subCategories()
	{
		if ($('category_id').value == 18)
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
		<?php if(!$this -> showOther):?>
		$('relation_other-wrapper').hide();
		<?php endif;?>
		<?php if(!$this -> showPreferredFoot):?>
			$('referred_foot-wrapper').hide();
		<?php endif;?>
		<?php if(!$this -> showPosition):?>
			$('position_id-wrapper').hide();
		<?php endif;?>
		
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
