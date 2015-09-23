/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

en4.core.runonce.add(function(){
	$('updates_toggle').getParent().addEvent('click', function()
	{
		if($('updates_toggle'))
		{
			var ele = $('updates_toggle').getParent();
			if( ele.className=='updates_pulldown' ) 
			{
	      		var elements = document.getElements('video');
			  elements.each(function(e)
			  {
			  	e.style.display = 'block';
			  });
			  var elements = document.getElements('object');
			  elements.each(function(e)
			  {
			  	e.style.display = 'block';
			  });
			  var elements = document.getElements('img.thumb_video');
			  elements.each(function(e)
			  {
			  	e.style.display = 'none';
			  });
	    } 
	    else 
	    {
	      var elements = document.getElements('video');
			  elements.each(function(e)
			  {
			  	e.style.display = 'none';
			  });
			  var elements = document.getElements('object');
			  elements.each(function(e)
			  {
			  	e.style.display = 'none';
			  });
			  var elements = document.getElements('img.thumb_video');
			  elements.each(function(e)
			  {
			  	e.style.display = 'block';
			  });
			}
		}
	});
});
//Check moothbox open when have video.
Smoothbox.Modal.Iframe.prototype.onClose=function()
{
 try{
  /**
   * put your code here
   */ 
  var elements = document.getElements('video');
  elements.each(function(e)
  {
  	e.style.display = 'block';
  });
  var elements = document.getElements('object');
  elements.each(function(e)
  {
  	e.style.display = 'block';
  });
  var elements = document.getElements('img.thumb_video');
  elements.each(function(e)
  {
  	e.style.display = 'none';
  });
 }
 catch(ex)
 {
  
 }
 
 this.fireEvent('closeafter', this);
};
Smoothbox.Modal.Iframe.prototype.onOpen=function()
{
 try{
  /**
   * put your code here
   */ 
  var elements = document.getElements('video');
  elements.each(function(e)
  {
  	e.style.display = 'none';
  });
  var elements = document.getElements('object');
  elements.each(function(e)
  {
  	e.style.display = 'none';
  });
  var elements = document.getElements('img.thumb_video');
  elements.each(function(e)
  {
  	e.style.display = 'block';
  });
 }
 catch(ex)
 {
  
 }
 
 this.fireEvent('openbefore', this);
};

if (typeof(ynvideo_types) == 'undefined') {
    ynvideo_types = [];
}


if (typeof(ynvideo_extract_code) == 'undefined') {
    ynvideo_extract_code = {};
}

ynvideo_extract_code.youtube = function (url) {
    var myURI = new URI(url);
    var youtube_code = myURI.get('data')['v'];
    if( youtube_code === undefined ) {
        youtube_code = myURI.get('file');
    }
    return youtube_code;
}

ynvideo_extract_code.vimeo = function (url) {
    var myURI = new URI(url);
    var vimeo_code = myURI.get('file');
    return vimeo_code;
}

ynvideo_extract_code.dailymotion = function (url) {
    var myURI = new URI(url);
    var dailymotion_file = myURI.get('file');
    var dailymotion_code = dailymotion_file.split('_')[0];
    return dailymotion_code;
}

ynvideo_extract_code.videoURL = function (url) {
    var myURI = new URI(url);
    file = myURI.get('file');
    return file;
}