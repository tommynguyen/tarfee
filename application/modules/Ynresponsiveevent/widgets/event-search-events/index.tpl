<?php
    $this -> headLink() -> appendStylesheet($this->baseUrl() . '/application/modules/Ynresponsiveevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');
	$this->headScript()->appendFile("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
?>
<div class="yntheme-event-container">
	<script type="text/javascript">
	    jQuery.noConflict();
	     window.addEvent('domready', function()
	     {
			document.getElementById("start_date-date").setAttribute("placeholder", "<?php echo $this -> translate("Start time")?>");
			document.getElementById("end_date-date").setAttribute("placeholder", "<?php echo $this -> translate("End time")?>");
	     });
	     en4.core.runonce.add(function()
		  {
		    if($('keyword'))
		    {
		      new OverText($('keyword'), {
		        poll: true,
		        pollInterval: 500,
		        positionOptions: {
		          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          offset: {
		            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
		            y: 2
		          }
		        }
		      });
		    }
		    if($('start_date'))
		    {
		      new OverText($('start_date'), {
		        poll: true,
		        pollInterval: 500,
		        positionOptions: {
		          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          offset: {
		            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
		            y: 2
		          }
		        }
		      });
		    }
		    if($('end_date'))
		    {
		      new OverText($('end_date'), {
		        poll: true,
		        pollInterval: 500,
		        positionOptions: {
		          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
		          offset: {
		            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
		            y: 2
		          }
		        }
		      });
		    }
            
            jQuery('.btn-advance_search').click(function(){
                if (jQuery('#ynevent_form_browse_filter').hasClass('advance_search')) {
                    jQuery('#ynevent_form_browse_filter').removeClass('advance_search');
                } else {
                    jQuery('#ynevent_form_browse_filter').addClass('advance_search');
                }
            });
		 });
	</script>
	
	<div class="btn-advance_search"><span><?php echo $this -> translate("Advanced search") ?></span> <i class="ynicon-setting-white"></i></div>
    <div id="ynevent_form_browse_filter" class="">
		<?php echo $this->form->render($this); ?>
	</div>
    
	<script type="text/javascript">
		function initialize() {
		 	var input = /** @type {HTMLInputElement} */(
				document.getElementById('location'));
		
		  	var autocomplete = new google.maps.places.Autocomplete(input);
		
		  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		    	var place = autocomplete.getPlace();
			    if (!place.geometry) {
			     	return;
			    }
				
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
						'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'event_general') ?>',
						'data' : {
							latitude : pos.lat(),
							longitude : pos.lng(),
						},
						'onSuccess' : function(json, text) {
							if(json.status == 'OK')
							{
								document.getElementById('location').value = json.results[0].formatted_address;
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
</div>