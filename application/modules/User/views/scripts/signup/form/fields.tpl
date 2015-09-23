<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: fields.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'fields', array(
    'topLevelId' => $this->form->getTopLevelId(),
    'topLevelValue' => $this->form->getTopLevelValue(),
  ));
?>

<?php echo $this->form->render($this) ?>

<script>
	window.addEvent('domready', function() {
		if ($$('#province_id option').length <= 1) {
			$('province_id-wrapper').hide();
		}
		
		if ($$('#city_id option').length <= 1) {
			$('city_id-wrapper').hide();
		}
		
		if ($('continent').value == '') {
			$('continent-wrapper').hide();
		}
		
		$('country_id').addEvent('change', function() {
			var id = this.value;
			
			var makeRequest1 = new Request({
      			url: "<?php echo $this->url(array('action'=>'get-continent'),'user_general', true)?>/location_id/"+id,
      			onComplete: function (respone){
					respone  = respone.trim();
                  	if(respone != '') {
                    	$('continent').value = respone;
      					$('continent-wrapper').show();
      				}
      				else {
      					$('continent').empty();
      					$('continent-wrapper').hide();
      				}
      			}
      		})
      		makeRequest1.send();
      		
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
		
		$('province_id').addEvent('change', function() {
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