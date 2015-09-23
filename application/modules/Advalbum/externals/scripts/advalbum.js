if (!window.initializeAdvAlbumJS) {
	var initializeAdvAlbumJS = true;

	var cmd_down = false;
	var ctrl_down = false;

	window.addEvent('keydown', function(e) {
		if (e.code == 224) {
			cmd_down = true;
		}
		if (e.code == 17) {
			ctrl_down = true;
		}
	});

	window.addEvent('keyup', function(e) {
		if (e.code == 224) {
			cmd_down = false;
		}
		if (e.code == 17) {
			ctrl_down = false;
		}
	});

	function photoPopupView(linkItem) {
		if (cmd_down || ctrl_down || !linkItem || !linkItem.href)
			return true;
		if (linkItem.href.indexOf('/photo/view/album_id/') == -1)
			return true;
		photoViewURL = linkItem.href.replace('/photo/view/album_id/', '/photo/popupview/album_id/');
		TINY.box.show({
			iframe : photoViewURL,
			width : 728,
			height : 1062,
			fixed : false,
			maskid : 'bluemask',
			maskopacity : 40,
			closejs : function() {
			}
		});
		return false;
	}

	function popupSlideshow(slideshowURL) 
	{
		TINY.box.show({
			iframe : slideshowURL,
			width : 720,
			height : 572,
			fixed : false,
			maskid : 'bluemask',
			maskopacity : 40,
			closejs : function() {
			}
		});
		return false;
	}

	function popupMobileSlideshow(slideshowURL) {
		TINY.box.show({
			iframe : slideshowURL,
			fixed : false,
			maskid : 'bluemask',
			maskopacity : 40,
			closejs : function() {
			}
		});
		return false;
	}

	function featureSlideshow(slideshowURL) {
		TINY.box.show({
			iframe : slideshowURL,
			width : 1100,
			height : 505,
			fixed : false,
			maskid : 'bluemask',
			maskopacity : 40,
			closejs : function() {
			}
		});
		return false;
	}

	function addSmoothboxEvents() {
		$$('a.advalbum_smoothbox').each(function(el) {
			el.removeEvent('click').addEvent('click', function(event) 
			{
				// remove click mobile version
				if (window.innerWidth < 1210 ) 
				{
					window.open( $(this).get('href'), '_self' );
					return false;
				}
				event.stop();
				Smoothbox.open(this);
				if (Smoothbox.instance) {
					//Smoothbox.instance.content.contentWindow.focus();
					Smoothbox.instance.addEvent('load', function(e) 
					{
						Smoothbox.instance.content.setStyles({
							'margin-bottom' : -9,
							'margin-top' : 0,
							'height': 505
						});
						window.parent.$('TB_window').setStyle('border', 'none');

					});
				}
			});
		});
	}


	window.addEvent('load', function(e) {
		addSmoothboxEvents();
	});

	function setAlbumCover(photo_id, album_id) {

	}

	function disableEnterKey(e) {
		var key;
		//if the users browser is internet explorer
		if (window.event) {
			//store the key code (Key number) of the pressed key
			key = window.event.keyCode;
			//otherwise
		}
		else {
			//store the key code (Key number) of the pressed key
			key = e.which;
		}
		//if key 13 is pressed (the enter key)
		if (key == 13) {
			return false;
		}
		else {
			//continue as normal (allow the key press for keys other than "enter")
			return true;
		}
	}

	function openLocation(url) {
		window.open(url, '_blank');
	}

}
