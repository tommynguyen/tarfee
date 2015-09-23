Date.prototype.getWeekOfMonth = function() {
	return Math.ceil((new Date(this.getFullYear(), this.getMonth(), 1).getDay() + this.getDate()) / 7.0);
}
Date.prototype.getDateCellId = function() {
	return 'yndc-' + this.getFullYear() + '-' + (this.getMonth() + 1) + '-' + this.getDate();
}
String.prototype.toTimestamp = function() {
	var ar = this.split(/(\-|\s|\:)/ig), len= ar.length, d;
	if(len>6){
		d = new Date(Date.UTC(parseInt(ar[0], 10), parseInt(ar[2], 10) - 1, parseInt(ar[4], 10), parseInt(ar[6], 10), parseInt(ar[8], 10), parseInt(ar[10], 10)));
	}else{
		d = new Date(Date.UTC(parseInt(ar[0], 10), parseInt(ar[1], 10) - 1, parseInt(ar[2], 10), parseInt(ar[3], 10), parseInt(ar[4], 10), parseInt(ar[5], 10)));
	}
	return d.getTime();
}

en4.ynevent = {
	updateEventsToCalendar : function (events){
		if (events.length > 0){
			events.each(function(e){
				if ($('yndc-' + e.day) != null) {
					if (!($('yndc-' + e.day).hasClass("otherMonth"))){
						$('yndc-' + e.day).addClass("selected");
						$('yndc-' + e.day).set('title', e.event_count);
					}
				}
			});
		}
	},
	updateGeneralCalendar : function(m, y) {
		(new Request.JSON({
			'format' : 'json',
			'url' : en4.core.baseUrl + '?m=lite&module=ynevent&name=calendar',
			'data' : {
				'month' : m,
				'year' : y
			},
			'onSuccess' : function(json, text) {
				
				en4.ynevent.attachTocalendar(json, m, y);
			}
		})).send();
	},
	updateEventOfDayGeneralCalendar : function(m, y) {
		(new Request.JSON({
			'format' : 'json',
			'url' : en4.core.baseUrl + 'admin/ynevent/manage/get-event-day',
			'data' : {
				'month' : m,
				'year' : y
			},
			'onSuccess' : function(json, text) {
				
				en4.ynevent.attachEventOfDayTocalendar(json, m, y);
			}
		})).send();
	},
	attachEventOfDayTocalendar : function(json, m, y) {
		
		var arr = new Array();
		if (json == undefined || !json.month || !json.year) {
			return;
		}
		m = json.month;
		y = json.year;

		if (json.events == null) {
			return;
		}
		if (json.total == 0) {
			return;
		}
		var edvs = {};
		var kds = new Array();
		var offset = en4.ynevent.getDateOffset(m, y);
		var tds = $$('.ynevent-cal-day');
		var selected = new Array();
		var endofday = new Array();
		for (var i = 0; i < json.total; ++i) {
			var evt = json.events[i];
			var s1 = evt.event_of_date.toTimestamp();
			var ds = new Date(s1);
			var id = ds.getDateCellId();
			index = selected.indexOf(id);
			
			selected.push(id);
			endofday.push(evt.title);
		}
		for (var i = 0; i < selected.length; i++) {
			var td = $(selected[i]);
			if (td != null) {
				if (!(td.hasClass("otherMonth"))){
					td.set('title', endofday[i]);
					td.addClass('selected');
				}
			}
		}
	},
	
	
	getDateOffset : function(m, y) {
		var d1 = new Date(y, m - 1, 1);
		return d1.getDay();
	},
	attachTocalendar : function(json, m, y) {
		console.log(json);
		var arr = new Array();
		if (json == undefined || !json.month || !json.year) {
			return;
		}
		m = json.month;
		y = json.year;

		if (json.events == null) {
			return;
		}
		if (json.total == 0) {
			return;
		}
		var edvs = {};
		var kds = new Array();
		var offset = en4.ynevent.getDateOffset(m, y);
		var tds = $$('.ynevent-cal-day');
		var selected = new Array(), numberOfEvent = new Array();
		for (var i = 0; i < json.total; ++i) {
			var evt = json.events[i];
			var s1 = evt.starttime.toTimestamp();
			var s0 = s1;
			var s2 = evt.endtime.toTimestamp();
			var ds = new Date(s1);
			var de = new Date(s2);
			var id = ds.getDateCellId();
			index = selected.indexOf(id);
			if (index < 0)
			{
				numberOfEvent[selected.length] = 1;
				selected.push(id);
			}
			else
			{
				numberOfEvent[index] = numberOfEvent[index] + 1;
			}
		}
		for (var i = 0; i < selected.length; i++) {
			var td = $(selected[i]);
			if (td != null) {
				if (!(td.hasClass("otherMonth"))){
					td.set('title', numberOfEvent[i] + ' ' + en4.core.language.translate(numberOfEvent[i] > 1 ? 'events' : 'event'));
					td.addClass('selected');
				}
			}
		}
	},
	removeSelectedClass : function(){
		$$(".selected").removeClass("selected");
	},
    changeCategory : function(element, name, model, route, isSearch, isFirst) {
    	var value = element.value;
    	var e = element.name;
        var prefix = 'id_wrapper_' + name + '_';
        var level = element.name.replace(name + '_', '');
        level = parseInt(level);
        
    	if( value == undefined || value == null || value == ''){
    		if(level>0){
    			var pre = name + '_'+ (level-1).toString();
    			pre = document.getElementById(pre);
    			if(pre != null && pre != undefined && pre){
    				value = $(pre).value;
    			}
    		}	
    	}
        element.form[name].value = value;
        
        if(isFirst == 0 && isSearch) {
            element.form.submit();
        }
        
        var ne = $(prefix + (level + 1));
        if(name == 'location_id') {
            var max = 3;
        } else {
            var max = 9;
        }
        for( i = level; i < max; i++) {
            if((document.getElementById(prefix + (i + 1)))) {
            document.getElementById(prefix + (i + 1)).style['display']= 'none';
            }
        }
        ;
        var request = new Request({
            'url' : en4.core.baseUrl + route + '/multi-level/change',
            'data' : {
                'format' : 'html',
                'id' : element.value,
                'name' : name,
                'level' : level,
                'model' : model,
                'isSearch' : isSearch
            },
            'onComplete' : function(a) {
                if(a != null && a!= undefined && a != '') {
                    ne.setStyles({'margin-top' : '8px'});
                    ne.setStyles({'display' : 'block'});
                    ne.innerHTML = a;
                }
            }
        });
        request.send();
    }, 
    specify : function(obj){
    	
		if(obj.value == 99)
		{
			$('repeatenddate-wrapper').style.display = 'none';
			$('repeatstartdate-wrapper').style.display = 'none';
			$('repeatstarttime-wrapper').style.display = 'none';
			$('repeatendtime-wrapper').style.display = 'none';
			
			$('spec_start_date-wrapper').style.display = 'block';
			$('spec_end_date-wrapper').style.display = 'block';
			$('specifys-wrapper').style.display = 'block';
		}			
		else
		{
			$('repeatenddate-wrapper').style.display = 'block';
			$('repeatstartdate-wrapper').style.display = 'block';
			$('repeatstarttime-wrapper').style.display = 'block';
			$('repeatendtime-wrapper').style.display = 'block';
			
			$('spec_start_date-wrapper').style.display = 'none';
			$('spec_end_date-wrapper').style.display = 'none';
			$('specifys-wrapper').style.display = 'none';
		}			
	}  
};

var initMap = function() {
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	if ($('longitude') && $('longitude').value) {
		position.lat = parseFloat($('latitude').value);
		position.lng = parseFloat($('longitude').value);
	}
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var mapEle = document.createElement('DIV');
	mapEle.id = 'map_canvas_edit';
	$('latitude-element').appendChild(mapEle);
	function deleteMarker() {
		if (marker) {
			marker.setMap(null);
			marker = null;
		}
	}

	function resetMarker(pos) {
		deleteMarker();
		marker = new google.maps.Marker({
			position : pos,
			animation : google.maps.Animation.DROP,
			draggable : true,
			map : map,
			title : "Drag this marker to set position of your Event!"
		});
		updatePosition(pos);
		return marker;
	}

	var map = new google.maps.Map(document.getElementById('map_canvas_edit'),
			myOptions);

	var input = document.getElementById('address');
	var autocomplete = new google.maps.places.Autocomplete(input);

	var marker = resetMarker(myLatlng);

	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		// infowindow.close();
		var place = autocomplete.getPlace();
		if (place.geometry.viewport) {
			deleteMarker();
			map.fitBounds(place.geometry.viewport);
		} else {
			var pos = place.geometry.location;
			marker = resetMarker(pos);
			map.setCenter(pos);
			map.setZoom(17);
			// Why 17? Because it looks good.
		}
	});
	function showInfo(msg) {

	}

	// Add dragging event listeners.
	google.maps.event.addListener(marker, 'dragstart', function() {
		showInfo('Dragging...');
	});
	google.maps.event.addListener(map, "rightclick", function(event) {
		resetMarker(event.latLng);
	});
	function updatePosition(pos) {
		if (pos && pos.lat && pos.lng) {
			$('latitude').value = pos.lat();
			$('longitude').value = pos.lng();
		}
	}
	;

	google.maps.event.addListener(marker, 'dragend', function() {
		updatePosition(marker.getPosition());
	});
}
function viewGoogleMap(canvasId) {
	var ele = $(canvasId);
	if (ele == null || ele == undefined) {
		return;
	}
	var lat = ele.getAttribute('latitude');
	var lng = ele.getAttribute('longitude');
	if (lat) {
		lat = parseFloat(lat)
	}
	if (lng) {
		lng = parseFloat(lng)
	}
	if (!lat || !lng) {
		ele.innerHTML = "no position associate with this event!";
		return;
	}
	var myLatlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
		zoom : 13,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById(canvasId), myOptions);
	var marker = new google.maps.Marker({
		position : myLatlng,
		map : map,
		title : "Event Position"
	});
}

function viewGoogleMapFromAddress(canvasId) {
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	var ele = $(canvasId);
	if (ele == null || ele == undefined) {
		return;
	}
	var request = {
		address : ele.getAttribute('title') + ' - '
				+ ele.getAttribute('location')
	};
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = null;
	geocoder = new google.maps.Geocoder();

	function matchGeoCoder(request) {
		geocoder.geocode(request, showResults);
	}
	function showResults(results, status) {
		if (status == google.maps.GeocoderStatus.OK && results
				&& results.length) {
			var result = results[0];
			var latlng = result.geometry.location;
			map = new google.maps.Map(document.getElementById(canvasId), {
				zoom : 15,
				center : latlng,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			});
			var marker = new google.maps.Marker({
				position : latlng,
				map : map,
				title : result.formatted_address
			});
			var infowindow = new google.maps.InfoWindow({
				content : $('company_component_inforbox').innerHTML
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
			});
			$('ynevent_loading_google_map').style.display = 'none';
		} else {
			$('ynevent_loading_google_map').innerHTML = 'Invalid Address!';
		}

	}
	matchGeoCoder(request);
}
