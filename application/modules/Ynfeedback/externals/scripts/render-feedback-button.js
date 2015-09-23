window.addEvent('domready', function() {
    var $params = {};
    $params['format'] = 'html';
    var request = new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/name/ynfeedback.feedback-button',
        data : $params,
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            var body = document.getElementsByTagName('body')[0];
            Elements.from(responseHTML).inject(body);
            eval(responseJavaScript);
            //preview click
		  	$('ynfeedback-feedback-button').addEvent('click', function() {
		  		loadDefault();
		  	}); 	
        }
    });
    request.send();
    
  	
});