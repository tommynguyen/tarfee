<script type="text/javascript">
<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
</script>
<?php echo $this->form->render($this); ?>

<script type="text/javascript">

 var clickSubmit = false;
 function removeSubmit()
 {
   if(document.getElementById('location_address').value.length > 5)
   {
   		clickSubmit = true;
   		$('buttons-wrapper').hide();
   		$('add_place').submit();
   }
 }
 function submitForm()
 {
   return clickSubmit;
 }
 
  function initialize() {
	 	var input = /** @type {HTMLInputElement} */(
			document.getElementById('location'));
	
	  	var autocomplete = new google.maps.places.Autocomplete(input);
	
	  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
	    	var place = autocomplete.getPlace();
		    if (!place.geometry) {
		     	return;
		    }
			document.getElementById('location_address').value = place.formatted_address;		
			document.getElementById('lat').value = place.geometry.location.lat();		
			document.getElementById('long').value = place.geometry.location.lng();
	    });
	}
  
   google.maps.event.addDomListener(window, 'load', initialize); 
  
  var getCurrentLocation = function(obj)
	{	
		if(navigator.geolocation) {
			
	    	navigator.geolocation.getCurrentPosition(function(position) {
	    			
	      	var pos = new google.maps.LatLng(position.coords.latitude,
	                                       position.coords.longitude);
	        
			if(pos)
			{
				
				current_posstion = new Request.JSON({
					'format' : 'json',
					'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'ynmember_extended') ?>',
					'data' : {
						latitude : pos.lat(),
						longitude : pos.lng(),
					},
					'onSuccess' : function(json, text) {
						
						if(json.status == 'OK')
						{
							document.getElementById('location').value = json.results[0].formatted_address;
							document.getElementById('location_address').value = json.results[0].formatted_address;
							document.getElementById('lat').value = json.results[0].geometry.location.lat;		
							document.getElementById('long').value = json.results[0].geometry.location.lng; 		
						}
						else{
							handleNoGeolocation(true);
						}
					}
				});	
				current_posstion.send();
				
			}
	      	
	    	}, function() {
	      		handleNoGeolocation(true);
	    	});
	  	}
	  	else {
    		// Browser doesn't support Geolocation
    		handleNoGeolocation(false);
  		}
		return false;
	}
	
	function handleNoGeolocation(errorFlag) {
		if (errorFlag) {
    		document.getElementById('location').value = 'Error: The Geolocation service failed.';
  		} 
  		else {
   			document.getElementById('location').value = 'Error: Your browser doesn\'t support geolocation.';
   		}
 	}
</script>
