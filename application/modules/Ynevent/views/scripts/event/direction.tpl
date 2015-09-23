<?php
/**
 * Younetco
 *
 * @category   Application_Extensions
 * @package    Adv Event
 * @copyright  Copyright 2014 Younetco
 * @license    http://www.younetco.com
 * @author     Long Le
 */
?>
<?php 
$this->headTranslate(array(
		"Missing location from",
		"Missing location to",
		"Browser doesn't support Geolocation",
		"Current location..."
));
?>

<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"); ?>
<style>
      .controls {
        border: 1px solid transparent;
        border-radius: 2px 0 0 2px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }

      #pac-input {
        background-color: #fff;
        padding: 0 11px 0 13px;
        width: 320px;
        font-size: 15px;
        font-weight: 300;
        text-overflow: ellipsis;
      }

      #pac-input:focus {
        border-color: #4d90fe;
        margin-left: -1px;
        padding-left: 14px;  /* Regular padding-left + 1. */
        width: 321px;
      }

      .pac-container {
        font-family: Roboto;
      }

      #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
      }

      #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
      }
}

</style>


<h4><?php echo $this->translate("Get Direction");?></h4>
<div class="ynevent_widget_location_direction">
	<label for="starting_location"><?php echo $this->translate("Starting location");?></label>
	<input id="pac-input" class="controls" placeholder="<?php echo $this->translate("Enter a location"); ?>" type="text" name="starting_location" id="starting_location" />
    <span class="close-btn" id="ynevent_location_direction_close_btn">x</span>
</div>
<div class="ynevent_widget_location_direction get_current_position">
	<label for="starting_current_position">&nbsp;</label>
	<a href="javascript:void(0);" onclick="getCurrentPosition()" class="buttonlink"><?php echo $this->translate("Get current position");?></a>
</div>

<div class="ynevent_widget_location_direction ynevent_widget_location_destination">
	<label for="destination"><?php echo $this->translate("Destination");?></label>
	<img src="application/modules/Ynevent/externals/images/location_pin.png" />
	<?php echo $this->fullAddress;?>
</div>
<div class="ynevent_widget_location_direction ynevent_widget_location_direction_button">
    <label>&nbsp;</label>
	<button onclick="calcRoute();"><?php echo $this->translate("Get Direction"); ?></button>
	<button onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel"); ?></button>
</div>

<div id="map-canvas" style="height: 400px; margin-top: 20px;"></div>
    
<script>
var fromPoint;
var endPoint = new  google.maps.LatLng(<?php echo $this->event->latitude;?>, <?php echo $this->event->longitude;?>);
var directionsService = new google.maps.DirectionsService();
var directionsDisplay;
var map;
var marker;

function getCurrentPosition()
{
	if(navigator.geolocation) {
	    navigator.geolocation.getCurrentPosition(function(position) {
	    	fromPoint = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	      	map.setCenter(fromPoint);
	      	var marker = new google.maps.Marker({
	      	  	map:map,
	      	  	draggable:true,
	      	  	animation: google.maps.Animation.DROP,
	      	  	position: fromPoint
	      	});

	      	//Get location title
			current_posstion = new Request.JSON({
				'format' : 'json',
				'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'event_general') ?>',
				'data' : {
					latitude : position.coords.latitude,
					longitude : position.coords.longitude,
				},
				'onSuccess' : function(json, text) {
					if(json.status == 'OK')
					{
						if (json.results.length > 0)
						{
							$("pac-input").set("value", json.results[0].formatted_address);
						}
						else
						{
							$("pac-input").set("value", en4.core.language.translate("Current location..."));
						}
					}
					else{
						alert(en4.core.language.translate("Browser doesn't support Geolocation"));
					}
				}
			});
			current_posstion.send();


	      	
	    }, function() {});
	}
	// Browser doesn't support Geolocation
	else {
	    alert(en4.core.language.translate("Browser doesn't support Geolocation"));
	}
}

function initialize() {
  var center =  new google.maps.LatLng(-33.8688, 151.2195);
  var mapOptions = {
    center: center,
    zoom: 13
  };

  directionsDisplay = new google.maps.DirectionsRenderer();
  map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
  directionsDisplay.setMap(map);
  
  var input = /** @type {HTMLInputElement} */(
      document.getElementById('pac-input'));

  var types = document.getElementById('type-selector');
  //map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  //map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();

  marker = new google.maps.Marker({
  	map:map,
  	draggable:true,
  	animation: google.maps.Animation.DROP,
  	position: center
  });
	
  google.maps.event.addListener(marker, 'dragend', toggleBounce);

  google.maps.event.addListener(autocomplete, 'place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17);  // Why 17? Because it looks good.
    }
    marker.setIcon(/** @type {google.maps.Icon} */({
      url: place.icon,
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }

    infowindow.setContent('<strong>' + place.name + '</strong><br>' + address);
    infowindow.open(map, marker);

    fromPoint = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
  });

  // Sets a listener on a radio button to change the filter type on Places
  // Autocomplete.
  function setupClickListener(id, types) {
    var radioButton = document.getElementById(id);
    google.maps.event.addDomListener(radioButton, 'click', function() {
      autocomplete.setTypes(types);
    });
  }

  function toggleBounce() {
		if (marker.getAnimation() != null) 
		{
  			marker.setAnimation(null);
		} 
		else 
		{
  			marker.setAnimation(google.maps.Animation.BOUNCE);
		}
		var point = marker.getPosition();
		fromPoint = new google.maps.LatLng(point.lat(), point.lng());
  }
  setupClickListener('changetype-all', []);
  setupClickListener('changetype-establishment', ['establishment']);
  setupClickListener('changetype-geocode', ['geocode']);
}


function calcRoute() {
	  if ( fromPoint == null )
	  {
		  alert(en4.core.language.translate("Missing location from"));
		  return;
	  }
	  if (!fromPoint.lat())
	  {
		  alert(en4.core.language.translate("Missing location from"));
		  return;
	  }
	  if (!endPoint.lat())
	  {
		  alert(en4.core.language.translate("Missing location to"));
		  return;
	  }
	  
	  var request = {
	      origin: fromPoint,
	      destination: endPoint,
	      travelMode: google.maps.TravelMode.DRIVING
	  };
	  directionsService.route(request, function(response, status) {
	    if (status == google.maps.DirectionsStatus.OK) {
	      directionsDisplay.setDirections(response);
	    }
	  });
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<script>
en4.core.runonce.add(function() 
{
	$('ynevent_location_direction_close_btn').addEvent('click', function(e) {
		$('pac-input').set('value','');
		fromPoint = null;
    });
});
</script>

