var navSlideShow;
document.addEvent('domready', function(){
	// cache the navigation elements
	var navs = $('ynevent_pagination').getElements('a');

	// create a basic slideshow
	navSlideShow = new SlideShow('ynevent_navigation_slideshow', {
		autoplay: true,
		delay: 5000,
		//transition: 'pushLeft',
		transition: 'fade', 
		duration: 500,
		selector: '.ynevent_slideshow_slide',
		onShow: function(data){
			// update navigation elements' class depending upon the current slide
			navs[data.previous.index].removeClass('current');
			navs[data.next.index].addClass('current');
		}
	});

	navs.each(function(item, index){
		// click a nav item ...
		item.addEvent('click', function(event){
			event.stop();
			// pushLeft or pushRight, depending upon where
			// the slideshow already is, and where it's going
			
			//var transition = (navSlideShow.index < index) ? 'pushLeft' : 'pushRight';
			var transition = 'fade';
			// call show method, index of the navigation element matches the slide index
			// on-the-fly transition option
			navSlideShow.show(index, {transition: transition});
		});
	});
	var list_middle = $$('.layout_main .layout_middle');
	var position = list_middle[0].getCoordinates();
    var slideWidth = position.width;
    ///$('ynevent_navigation_slideshow').style.width = slideWidth + "px";
});