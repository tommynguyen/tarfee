<?php
$location = Ynevent_Model_DbTable_Countries::getCountryName(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynevent.country', 'VNM'));
$settings = Engine_Api::_()->getApi('settings', 'core');
$googleApiKey = $settings->getSetting('ynevent.google.api','');
$this->headScript()
		->appendFile("http://maps.google.com/maps?file=api&v=2&key=$googleApiKey");
?>
<br />
<table width="300">
  <tr>
    <td><b><?php echo $this->translate('Latitude')?></b></td>
    <td><b><?php echo $this->translate('Longitude')?></b></td>
  </tr>
  <tr>
    <td id="lat"></td>
    <td id="lng"></td>
  </tr>
</table>
<br />
<span class="ynevent_refresh_map">
<a href="javascript:;" onclick="refresh_map()"><?php echo $this->translate("Refresh map")?></a>
</span>
<span class="ynevent_add_location">
	<a href="javascript:;" onclick="action_add()"><?php echo $this->translate("Add this location");?></a>
</span>
<br />
<!--  <div id="ynevent_google_map_component"></div> -->
<div align="center" id="map" class="ynevent_googlemaps"><br/></div>


<script type="text/javascript">

	var getCurrentPosition = function()
	{
  		if (navigator.geolocation)
    	{
    		navigator.geolocation.getCurrentPosition(function(position){
    			if (position.coords.latitude)
    			{
    				showMapByLatLong(position.coords.latitude, position.coords.longitude);
            	}
    			else
    			{
    				showMapByLatLong(-33.8688, 151.2195);
        		}
    			
        	});
    	}
  		else
		{
  			showMapByLatLong(-33.8688, 151.2195);
		}
  	}

	var showAddress = function (address) 
	{
		var map = new GMap2(document.getElementById("map"));
	    map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
		geocoder = new GClientGeocoder();
		if (geocoder) {
		     geocoder.getLatLng(
		       	   address,
			       function(point) {
			         if (!point) {
				        if (address == '')
				        	alert("<?php echo $this->translate('Please enter the address'); ?>");
				        else
			           		alert(address + " <?php echo $this->translate('not found'); ?>");
			         } else {
					  	document.getElementById("lat").innerHTML = point.lat().toFixed(5);
				   		document.getElementById("lng").innerHTML = point.lng().toFixed(5);
					 	map.clearOverlays()
						map.setCenter(point, 14);
						var marker = new GMarker(point, {draggable: true});  
					 	map.addOverlay(marker);
			
						GEvent.addListener(marker, "dragend", function() {
				   			var pt = marker.getPoint();
					     	map.panTo(pt);
				   			document.getElementById("lat").innerHTML = pt.lat().toFixed(5);
					     	document.getElementById("lng").innerHTML = pt.lng().toFixed(5);
			     		});
			
			
						GEvent.addListener(map, "moveend", function() {
							  	map.clearOverlays();
								var center = map.getCenter();
							 	var marker = new GMarker(center, {draggable: true});
							  	map.addOverlay(marker);
							  	document.getElementById("lat").innerHTML = center.lat().toFixed(5);
						   		document.getElementById("lng").innerHTML = center.lng().toFixed(5);
					
						 		GEvent.addListener(marker, "dragend", function() {
					  				var pt = marker.getPoint();
						    		map.panTo(pt);
					 				document.getElementById("lat").innerHTML = pt.lat().toFixed(5);
						   			document.getElementById("lng").innerHTML = pt.lng().toFixed(5);
					     		});
					    });
			         }
			     }
		    );
		}
 	}
 	
	var showMapByLatLong = function (latitude, longtitude){
		var map = new GMap2(document.getElementById("map"));
	  	
	  	map.addControl(new GSmallMapControl());
		map.addControl(new GMapTypeControl());
	  	geocoder = new GClientGeocoder();
	  	//GEvent.addListener(map, "click", getAddress);
		
		map.clearOverlays();
		map.setCenter(new GLatLng(latitude, longtitude), 15);
		var marker = new GMarker(new GLatLng(latitude, longtitude), {draggable: true});  
	 	map.addOverlay(marker);
		
	  	GEvent.addListener(marker, "dragend", function() {
   			var pt = marker.getPoint();
	     	map.panTo(pt);
   			document.getElementById("lat").innerHTML = pt.lat().toFixed(5);
	     	document.getElementById("lng").innerHTML = pt.lng().toFixed(5);
 		});


		GEvent.addListener(map, "moveend", function() {
			  	map.clearOverlays();
				var center = map.getCenter();
			 	var marker = new GMarker(center, {draggable: true});
			  	map.addOverlay(marker);
			  	document.getElementById("lat").innerHTML = center.lat().toFixed(5);
		   		document.getElementById("lng").innerHTML = center.lng().toFixed(5);
	
		 		GEvent.addListener(marker, "dragend", function() {
	  				var pt = marker.getPoint();
		    		map.panTo(pt);
	 				document.getElementById("lat").innerHTML = pt.lat().toFixed(5);
		   			document.getElementById("lng").innerHTML = pt.lng().toFixed(5);
	     		});
	    });
	}

	var update_map_location = function(title)
	{
		title = "," + title;
		var q = title.replace(/\,+/,'');		
		var src = "https://maps.google.com/maps?q="+ q +"&amp;hnear="+ q +"&amp;t=m&amp;ie=UTF8&amp;z=12&amp;output=embed";
		var html = '<iframe id="gmap" name="gmap" width="425" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'+src+'"></iframe>';
		document.getElementById('ynevent_google_map_component').innerHTML  = html;
	}
	 
	var refresh_map = function()
	{
		var country = $('country').value;
		//if(country == "")
			//country = $('location').value;
		//var title = $('address').value + "," + $('city').value + "," + country + "," + $('zip_code').value;
		//update_map_location(title);
		
		st_address = "";
		if ($('address').value != '')
			st_address += $('address').value;
		
		if ($('city').value != '')
			st_address += "," + $('city').value;
		
		if (country != '')
			st_address += "," + country;
		
		if ($('zip_code').value != '')
			st_address += "," + $('zip_code').value;

		n = st_address.indexOf(",");
		if (n == 0)
			st_address = st_address.substring(1);
		
		showAddress(st_address);	
	}
	
	var action_add = function()
	{
		//parent.$('location').value = $('location').value;
		var country = $('country').value;
		
		st_address = "";
		//if ($('location').value != '')	
			//st_address += $('location').value;
		
		if ($('address').value != '')
			st_address += $('address').value;
		
		if ($('city').value != '')
			st_address += "," + $('city').value;
		
		if (country != '')
			st_address += "," + country;
		
		if ($('zip_code').value != '' && $('zip_code').value != '0')
			st_address += "," + $('zip_code').value;

		n = st_address.indexOf(",");
		if (n == 0)
			st_address = st_address.substring(1);
		
		//parent.$('address').value = $('location').value + "," + $('address').value + "," + $('city').value + "," + country + "," + $('zip_code').value;
		parent.$('full_address').value = st_address;
		parent.$('address').value = $('address').value;
		parent.$('city').value = $('city').value;
		parent.$('country').value = $('country').value;
		parent.$('zip_code').value = $('zip_code').value;
		
		parent.$('latitude').value = document.getElementById("lat").innerHTML;
		parent.$('longitude').value = document.getElementById("lng").innerHTML;
		parent.Smoothbox.close();
	}
	
	$(window).addEvent('domready', function() {
		var googleApiKey = '<?php echo $googleApiKey;?>';
		if (googleApiKey == '')
		{
			alert('<?php echo $this->translate('Please contact admin to set up the Google Api in back-end');?>');
			parent.Smoothbox.close();	
		}	
		$('address').value = parent.$('address').value;
		$('city').value = parent.$('city').value;
		$('country').value = parent.$('country').value;
		$('zip_code').value = parent.$('zip_code').value;
		
		document.getElementById("lat").innerHTML = parent.$('latitude').value;
     	document.getElementById("lng").innerHTML = parent.$('longitude').value;
     	
     	var latitude = parent.$('latitude').value; 
     	var longitude = parent.$('longitude').value; 
     	if (latitude == '' || longitude == '')
     	{
     		getCurrentPosition();
        }
     	else
     	{
     		showMapByLatLong(latitude, longitude);
        }
     	
	});
</script>