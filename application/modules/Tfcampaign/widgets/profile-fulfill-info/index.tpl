<?php
	$campaign = $this -> campaign;
	$type = $this -> type;
?>

<div id="tfcampaign-fulfill-section">

<div id="suggest-error"></div>

<!-- type == age  -->
<?php if($type == "age") :?>
	<h6><?php echo $this -> translate("Please choose the age limitation");?></h6>
	<label for="from_age"><?php echo $this->translate('From')?></label>
    <select id="from_age" name="from_age">
        <option value=""></option>
        <?php for ($i = 1; $i <= 100; $i++) { ?>
        <option value="<?php echo $i?>"><?php echo $i?></option>
        <?php } ?>    
    </select>
    <label for="to_age"><?php echo $this->translate('to')?></label>
    <select id="to_age" name="to_age">
        <option value=""></option>
        <?php for ($i = 1; $i <= 100; $i++) { ?>
        <option value="<?php echo $i?>"><?php echo $i?></option>
        <?php } ?>
    </select>
<?php endif;?>

<!-- type == gender  -->
<?php if($type == "gender") :?>
	<?php echo $this -> translate("Please choose the gender");?>
	<select name="gender" id="gender">
	    <option value="0"></option>
	    <option value="1"><?php echo $this -> translate('Male');?></option>
	    <option value="2"><?php echo $this -> translate('Female');?></option>
	</select>
<?php endif;?>

<!-- type == gender  -->
<?php if($type == "country") :?>
	<?php echo $this -> translate("Please choose the location");?>
	
	<?php 
		$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
		$countriesAssoc = array('0'=>'') + $countriesAssoc;
	?>
	<?php echo $this -> translate('Country');?>
	<select name="country_id" id="country_id">
		<?php foreach($countriesAssoc as $key => $value) :?>
	    	<option value="<?php echo $key;?>"><?php echo $value;?></option>
	    <?php endforeach;?>
	</select>
	
	<div id="province_id-wrapper">
		<?php echo $this -> translate('Province/State');?>
		<select name="province_id" id="province_id">
		</select>
	</div>
	
	<div id="city_id-wrapper">
		<?php echo $this -> translate('City');?>
		<select name="city_id" id="city_id">
		</select>
	</div>
	
	<script type="text/javascript">
		window.addEvent('domready', function() {
			
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
	
<?php endif;?>

<button id="tfcampaing-btn-suggest-save"><?php echo $this -> translate('Save');?></button>

<script type="application/javascript">
	
	
	$('tfcampaing-btn-suggest-save').addEvent('click', function(){
		//clear error before check agin
	 	$$('.suggest-campaign-error').destroy();
		var url = '<?php echo $this -> url(array('action' => 'save-suggest', 'campaign_id' => $campaign -> getIdentity()), 'tfcampaign_specific', true);?>'
		
		var data = new Object();
		data.type = '<?php echo $type;?>';
		
		//type == age
		<?php if($type == "age") :?>
			var from_age = $('from_age').get('value');
			var to_age = $('to_age').get('value');
			
			if(from_age == "" || to_age == "") {
				var message = "<?php echo $this -> translate('please select age');?>";
		 		var div = new Element('div', {
			       'html': message,
			       'class': 'suggest-campaign-error',
			        styles: {
				        'color': 'red',
				        'font-weight': 'bold',
				    },
			    });
		 		$('suggest-error').grab(div,'before');
		 		return false;
			}
			
			if(from_age != "" && to_age != ""){
		 		if(parseInt(from_age) > parseInt(to_age)) {
		 			var message = "<?php echo $this -> translate('to age must be greater than from age');?>";
			 		var div = new Element('div', {
				       'html': message,
				       'class': 'suggest-campaign-error',
				        styles: {
					        'color': 'red',
					        'font-weight': 'bold',
					    },
				    });
			 		$('suggest-error').grab(div,'before');
			 		return false;
		 		}
		    }
			data.from_age = from_age;
			data.to_age = to_age;
		<?php endif;?>
		
		//gender
		<?php if($type == "gender") :?>
			var gender = $('gender').get('value');
			if(gender == 0) {
				var message = "<?php echo $this -> translate('please select gender');?>";
			 		var div = new Element('div', {
				       'html': message,
				       'class': 'suggest-campaign-error',
				        styles: {
					        'color': 'red',
					        'font-weight': 'bold',
					    },
				    });
			 		$('suggest-error').grab(div,'before');
			 		return false;
			}
			data.gender = gender;
		<?php endif;?>
		
		//gender
		<?php if($type == "country") :?>
			var country_id = $('country_id').get('value');
			if($('province_id')) {
				var province_id = $('province_id').get('value')
			} else {
				var province_id = 0;
			}
			if($('city_id')) {
				var city_id = $('city_id').get('value')
			} else {
				var city_id = 0;
			}
			if(country_id == 0) {
				var message = "<?php echo $this -> translate('please select location');?>";
			 		var div = new Element('div', {
				       'html': message,
				       'class': 'suggest-campaign-error',
				        styles: {
					        'color': 'red',
					        'font-weight': 'bold',
					    },
				    });
			 		$('suggest-error').grab(div,'before');
			 		return false;
			}
			data.country_id = country_id;
			data.province_id = province_id;
			data.city_id = city_id;
			
		<?php endif;?>
		
		new Request.JSON({
	        url: url,
	        method: 'post',
	        data: data,
	        'onSuccess' : function(responseJSON, responseText)
	        {
	          $('tfcampaign-fulfill-section').innerHTML = 
	          	"<div class='tip'>" + 
	          	"<span><?php echo $this -> translate('The data has been saved');?></span>" +
	          	"</div>";
	        }
	    }).send();
	});
</script>

</div>