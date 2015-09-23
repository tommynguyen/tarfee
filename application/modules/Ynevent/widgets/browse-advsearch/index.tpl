<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
<link type="text/css" href="application/modules/Ynevent/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script src="application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js"></script>
<script src="application/modules/Ynevent/externals/scripts/jquery-ui-1.8.17.custom.min.js"></script>
<?php
	$settings = Engine_Api::_()->getApi('settings', 'core');
	$googleApiKey = $settings->getSetting('ynevent.google.api','');
	$this->headScript()
		->appendFile("http://maps.google.com/maps?file=api&v=2&key=$googleApiKey");
?>
<script type="text/javascript">
    jQuery.noConflict();
    var ynEventCalendar= {        
            currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
            monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>', 
                '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>', 
                '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
            ],
            monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
            ],
            dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',            
            ],
            dayNamesShort: ['<?php echo $this->translate('Su') ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
                ],
            dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
                '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
                ],        
            firstDay: 0,
            //isRTL:yneventIsRightToLeft,
            isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
            showMonthAfterYear: false,
            yearSuffix: ''
    };
	
    jQuery(document).ready(function(){
    	jQuery.datepicker.setDefaults(ynEventCalendar);	
        // Datepicker
        jQuery('#advstart_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });
        jQuery('#advend_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Ynevent/externals/images/calendar.png',
            buttonImageOnly: true
        });
		
    });

    var displaySetting = function()
    {			
		document.getElementById('location-wrapper').style.display = 'block';
		document.getElementById('radius-wrapper').style.display = 'block';
		document.getElementById('advstart_date-wrapper').style.display = 'block';
		document.getElementById('advend_date-wrapper').style.display = 'block';
		document.getElementById('is_setting').value = 1;		
    }
     
     en4.core.runonce.add(function()
	  {
	    if($('title'))
	    {
	      new OverText($('title'), {
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
	    if($('radius'))
	    {
	      new OverText($('radius'), {
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
	 });
	 
	 
</script>


<ul id="ynevent_search_form_mileof_zipcode" class="form-errors" <?php echo ($this->mile_of_error)? '' : 'style="display: none;"';  ?>><li><?php echo $this->translate('Please enter the <b>Zip/Postal code</b> to search with <b>Mile(s) from Zip/Postal Code</b>'); ?></li></ul>
<?php echo $this->form->render($this); ?>

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
			
			document.getElementById('advlat').value = place.geometry.location.d;		
			document.getElementById('advlong').value = place.geometry.location.e;
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
						latitude : pos.d,
						longitude : pos.e,
					},
					'onSuccess' : function(json, text) {
						if(json.status == 'OK')
						{
							document.getElementById('location').value = json.results[0].formatted_address;
							document.getElementById('advlat').value = json.results[0].geometry.location.lat;		
							document.getElementById('advlong').value = json.results[0].geometry.location.lng; 		
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






