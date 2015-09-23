<?php echo $this->form->render($this) ?>
<script type="text/javascript">
window.addEvent('domready', function() 
{
	if ($$('#province_id option').length <= 1)
	{
		$('province_id-wrapper').hide();
	}
	
	if ($$('#city_id option').length <= 1) 
	{
		$('city_id-wrapper').hide();
	}
	
	$('country_id').addEvent('change', function() 
	{
		var id = this.value;
		var makeRequest = new Request({
  			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
  			onComplete: function (respone){
				respone  = respone.trim();
				var options = Elements.from(respone);
              	if(options.length > 0) {
              		var option = new Element('option', {
						'value': '0',
						'text': ''
					})  
                	$('province_id').empty();
                	$('province_id').grab(option);  
                	$('province_id').adopt(options);
  					$('province_id-wrapper').show();
  				}
  				else {
  					$('province_id').empty();
  					$('province_id-wrapper').hide();
  				}
  				$('city_id').empty();
				$('city_id-wrapper').hide();
  			}
  		})
  		makeRequest.send();
	});
	
	$('province_id').addEvent('change', function() 
	{
		var id = this.value;
		var makeRequest = new Request({
  			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
  			onComplete: function (respone){
				respone  = respone.trim();
				var options = Elements.from(respone);
              	if(options.length > 0) {
              		var option = new Element('option', {
						'value': '0',
						'text': ''
					})  
                	$('city_id').empty();
                	$('city_id').grab(option);
                	$('city_id').adopt(options);
  					$('city_id-wrapper').show();
  				}
  				else {
  					$('city_id').empty();
  					$('city_id-wrapper').hide();
  				}
  			}
  		})
  		makeRequest.send();
	});
});
</script>