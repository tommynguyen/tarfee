<?php $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");?>
<?php
  $this->headScript()
    	->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
    	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.Local.js')
		->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynevent/externals/scripts/Autocompleter.Request.js');
?>
<?php
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
// Populate data
  var maxRecipients = 1;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>
  
  function removeFromToValue(id) 
  {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(",");
    var toValueIndex = "";

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(",");
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==""){
      document.getElementById('toValues-wrapper').style.height = '0';
    }

    document.getElementById('host-wrapper').style.display = 'block';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
    document.getElementById('host').value ='';
  }

  en4.core.runonce.add(function() 
  {
  	new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    if( !isPopulated ) { // NOT POPULATED
      new Autocompleter2.Request.JSON('host', '<?php echo $this->url(array('controller' => 'event', 'action' => 'friends'), 'event_extended', true) ?>', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
        'multiple': false,
        'className': 'message-autosuggest',
        'filterSubset' : true,
        'tokenFormat' : 'object',
        'tokenValueKey' : 'label',
        'injectChoice': function(token){
          if(token.type == 'user'){
            var choice = new Element('li', {
              'class': 'autocompleter-choices',
              'html': token.photo,
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
          else {
            var choice = new Element('li', {
              'class': 'autocompleter-choices friendlist',
              'id':token.label
            });
            new Element('div', {
              'html': this.markQueryValue(token.label),
              'class': 'autocompleter-choice'
            }).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
          }
        },
        onPush : function(){
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
             document.getElementById('host-wrapper').style.display = 'none';
             document.getElementById('host').value = document.getElementById('toValues').value;
          }
        }
      });
      
      new Composer.OverText($document.getElementById('host'), {
        'textOverride' : '<?php echo $this->translate('Start typing...') ?>',
        'element' : 'label',
        'isPlainText' : true,
        'positionOptions' : {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });

    } 
    else { // POPULATED
      var myElement = new Element("span", 
      {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title
      });
      document.getElementById('to-element').appendChild(myElement);
      document.getElementById('to-wrapper').style.height = 'auto';

      // Hide to input?
      document.getElementById('host').style.display = 'none';
      document.getElementById('toValues-wrapper').style.display = 'none';
    }
  	});
</script>
<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <?php if( $this->parent_type !== 'group' ) { ?>
	<div class="headline">
	  <h2>
	    <?php echo $this->translate('Events') ?>
	  </h2>
	  <div class="tabs">
	    <?php
	      // Render the menu
	      echo $this->navigation()
	        ->menu()
	        ->setContainer($this->navigation)
	        ->render();
	    ?>
	  </div>
	</div>
<?php } ?>
  <?php }
  else
  {?>
  	<div id='tabs'>
	  	<ul class="ymb_navigation_more">
		  <?php 
		  $max = 3;
		  $count = 0;
		  foreach( $this->navigation as $item ): $count ++;
		  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
	        'reset_params', 'route', 'module', 'controller', 'action', 'type',
	        'visible', 'label', 'href'
	        )));
		    if($count <= $max):?>
		     <li<?php echo($item->active?' class="active"':'')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        	</li>	
		  <?php endif; endforeach; ?>
		  <?php if(count($this->navigation) > $max):?>
		  <li class="ymb_show_more_menus">
		  	<a href="javascript:void(0)" class="ymb_showmore_menus">
		  		<i class="icon_showmore_menus">
		  			<?php echo $this-> translate("Show more");?>
		  		</i>	  		  		
		  	</a>
		  	<div class="ymb_listmore_option">
		  		<div class="ymb_bg_showmore">
		  			<i class="ymb_arrow_showmore"></i>
		  		</div>	  		
			<?php 
			 	$count = 0;
				foreach( $this->navigation as $item ): $count ++;
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				if($count > $max):
			?>
				<div<?php echo($item->active?' class="active"':'')?>>
				     <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
				 </div>
				 <?php endif; endforeach; ?>
			</div>
		  </li>
		  <?php endif;?>
		</ul>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_menus').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
			})
		});
	</script>
  <?php  }?>
<?php echo $this->form->render($this) ?>

<script type="text/javascript">
function initialize() {
 	var input = /** @type {HTMLInputElement} */(
		document.getElementById('full_address'));

  	var autocomplete = new google.maps.places.Autocomplete(input);

  	google.maps.event.addListener(autocomplete, 'place_changed', function() {
    	var place = autocomplete.getPlace();
	    if (!place.geometry) {
	     	return;
	    }
	    console.log(document.getElementById('full_address').value);
	    document.getElementById('address').value = document.getElementById('full_address').value;	
		document.getElementById('latitude').value = place.geometry.location.lat();		
		document.getElementById('longitude').value = place.geometry.location.lng();
    });
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

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
    var maxDate = "<?php echo $this->gEndDate; ?>";    
    var cal_spec_start_date_onHideStart = function()
    {
        // check end date and make it the same date if it's too
        cal_spec_end_date.calendars[0].start = new Date( $('spec_start_date-date').value );        
        
        // redraw calendar
        cal_spec_end_date.navigate(cal_spec_end_date.calendars[0], 'm', 1);
        cal_spec_end_date.navigate(cal_spec_end_date.calendars[0], 'm', -1);
    }
    <?php if($this -> repeat_type == 0): ?>
	    en4.core.runonce.add(function()
	    {
	    	setValidDateForEndRepeat();
	    });
	    var cal_starttime_onHideStart = function()
	    {
	        // check end date and make it the same date if it's too
	        cal_endtime.calendars[0].start = new Date( $('starttime-date').value );        
	        
	        // redraw calendar
	        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
	        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
	    }
	    var cal_repeatstartdate_onHideStart = function()
	    {
	    	cal_repeatenddate.calendars[0].start = new Date( $('repeatstartdate-date').value ); 
	    	cal_repeatenddate.navigate(cal_repeatenddate.calendars[0], 'm', 1);
	        cal_repeatenddate.navigate(cal_repeatenddate.calendars[0], 'm', -1);
	
	    }
	    var cal_repeatenddate_onHideStart = function()
	    {        
	        // check start date and make it the same date if it's too
	        if(maxDate != "")
	        {           
	            cal_repeatenddate.calendars[0].end = new Date( maxDate );   
	        }            
	    }
	
	    var setValidDateForEndRepeat = function()
	    {
	    	if(maxDate != "")
	    	{ 
	            cal_repeatenddate.calendars[0].end = new Date( maxDate );
	            var validDays1 = cal_repeatenddate.values(cal_repeatenddate.calendars[0]).days;                        
	            cal_repeatenddate.calendars[0].days = validDays1;
	        }
	    } 
	    function isrepeat(obj)
	    {
	    	$('g_repeat_type').value = obj.value; 
			$('repeat_frequency').value = 1;
			if(obj.value == 0)
			{
				$('repeat_frequency-wrapper').style.display = 'none';
				$('repeatenddate-wrapper').style.display = 'none';
				$('repeatstartdate-wrapper').style.display = 'none';
				$('repeatendtime-wrapper').style.display = 'none';
				$('repeatstarttime-wrapper').style.display = 'none';
				$('starttime-wrapper').style.display = 'block';
				$('endtime-wrapper').style.display = 'block';
				$('spec_start_date-wrapper').style.display = 'none';
				$('spec_end_date-wrapper').style.display = 'none';
				$('specifys-wrapper').style.display = 'none';
			}			
			else
			{
				$('repeat_frequency-wrapper').style.display = 'block';
				$('repeatenddate-wrapper').style.display = 'block';
				$('repeatstartdate-wrapper').style.display = 'block';
				$('repeatendtime-wrapper').style.display = 'block';
				$('repeatstarttime-wrapper').style.display = 'block';
				$('starttime-wrapper').style.display = 'none';
				$('endtime-wrapper').style.display = 'none';
			}			
		}
	    window.addEvent('domready', function() 
	    {
	    	if($('g_repeat_type').value == 0 || $('g_repeat_type').value == "")
		    {
				$('repeat_frequency-wrapper').style.display = 'none';
				$('repeatenddate-wrapper').style.display = 'none';
				$('repeatstartdate-wrapper').style.display = 'none';
				$('repeatstarttime-wrapper').style.display = 'none';
				$('repeatendtime-wrapper').style.display = 'none';
				$('starttime-wrapper').style.display = 'block';
				$('endtime-wrapper').style.display = 'block';		
			}			
			else
			{
				$('repeat_frequency-wrapper').style.display = 'block';
				$('repeatenddate-wrapper').style.display = 'block';
				$('repeatstartdate-wrapper').style.display = 'block';
				$('repeatstarttime-wrapper').style.display = 'block';
				$('repeatendtime-wrapper').style.display = 'block';
				$('starttime-wrapper').style.display = 'none';
				$('endtime-wrapper').style.display = 'none';	
			}
			//Set dropdown list of event hidden
			$("repeatstartdate-hour").set('value', 0);
			$("repeatstartdate-minute").set('value', 10);
			$("repeatenddate-hour").set('value', 0);
			$("repeatenddate-minute").set('value', 10);
			
			$("repeatstartdate-hour").setStyle("display","none");
			$("repeatstartdate-minute").setStyle("display","none");
			$("repeatenddate-hour").setStyle("display","none");
			$("repeatenddate-minute").setStyle("display","none");
			if($("repeatstartdate-ampm"))
			{
				$("repeatstartdate-ampm").setStyle("display","none");  
			}
			if($("repeatenddate-ampm"))
			{
				$("repeatenddate-ampm").setStyle("display","none");  
			}
			
			$('repeatstarttime-element').getChildren('div').hide();
			$('repeatendtime-element').getChildren('div').hide();
			
			$('spec_start_date-wrapper').style.display = 'none';
			$('spec_end_date-wrapper').style.display = 'none';
			$('specifys-wrapper').style.display = 'none';	 	
		});
	<?php else:?>
		window.addEvent('domready',function(e) 
		{
			<?php if(!$this ->confirm_apply_for): ?>
				Smoothbox.open($('ynevent_kind'), {width : 280});
			<?php endif;?>
			<?php if($this -> repeat_type != 99):?>
	    		$('starttime-element').getChildren('div').hide();
				$('endtime-element').getChildren('div').hide();
	    	<?php endif;?>
		});
		function myselect(e)
		{		
			$('apply_for_action').value = $$('.form-options-wrapper > li input[name=apply_for]:checked')[1].get('value')	
			$('ynevent_create_form').submit();
		}	
		
	<?php endif;?>
</script>

<div class="" style="display: none;">
<?php 
	echo $this->formcheck;
?>
</div>