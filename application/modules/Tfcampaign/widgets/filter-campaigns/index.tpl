<?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
 	$this->headLink()
		 ->prependStylesheet($staticBaseUrl . 'application/modules/Tfcampaign/externals/styles/slider/styles.css')
		 ->prependStylesheet("//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css")
		 ;
		
	$this->headScript()
  		 ->appendFile($staticBaseUrl . 'application/modules/Tfcampaign/externals/scripts/jquery.min.js')	
		 ->appendScript('jQuery.noConflict();')
  		 ->appendFile($staticBaseUrl . 'application/modules/Tfcampaign/externals/scripts/jquery-ui.min.js')	
		;	
?>
<div id="form-search-wrapper" class="tf_layout_campain_filter" style="display:none">
<form id="fiter-campaign" method="get" action="">

<input id="campaign-sort" value="campaign.creation_date" type="hidden" name="sort">

<input id="campaign-direction" value="DESC" type="hidden" name="direction">

<div class="tf_boxsearch_campaign">
	<input type="text" name="title" id="title" placeholder="<?php echo $this -> translate("Search Campaigns...");?>">
</div>

<?php if( Engine_Api::_()->authorization()->isAllowed('group', $this -> viewer(), 'create')) :?>
<a href="<?php echo $this -> url(array('action' => 'create'), 'tfcampaign_general', true);?>" class="btn-add-campaign"><?php echo $this -> translate("Add Campaign");?><i class="fa fa-plus"></i></a>
<?php endif;?>

<div class="tf_box_filter_campaign">
<div class="ybo_headline"><h3><?php echo $this -> translate("Filters")?></h3></div>

<div class="tf_campaign_filter_age">
	<h5 class="tf_campaign_filter_title"><?php echo $this -> translate("By age");?></h5>

  	<div id="content">       
	    <input type="hidden" value="<?php echo $this->age_from?>" name="from_age" id="from_age">
	    <input type="hidden" value="<?php echo $this->age_to?>" name="to_age" id="to_age">
	    <div id="display-from-age"><?php echo $this->age_from?> (<?php echo (intval(date('Y')) - intval($this->age_from))?>)</div>
	    <div id="display-to-age"><?php echo $this->age_to?> (<?php echo (intval(date('Y')) - intval($this->age_to))?>)</div>
	    <div id="rangeslider"></div>
	</div>
</div>

<div class="tf_campaign_filter_age">
	<h5 class="tf_campaign_filter_title"><?php echo $this -> translate("By date");?></h5>

	<div class="box-date">
		<p>
			<span><?php echo $this -> translate('From ');?></span>
			<input type="text" name="start_date_from" id="start_date_from">
		</p>
		<p>
			<span><?php echo $this -> translate('To ');?></span>
			<input type="text" name="start_date_to" id="start_date_to">
		</p>
	</div>
</div>

<div class="tf_campaign_filter_age">
	<h5 class="tf_campaign_filter_title"><?php echo $this -> translate("Location");?></h5>
	
	<div class="campaign_location">
		<?php
			$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
			$countriesAssoc = array('0'=>$this -> translate('Any')) + $countriesAssoc;
		?>

		<select id="country_id" name="country_id">
			<?php foreach ($countriesAssoc as $key => $value) :?>
				<option value="<?php echo $key;?>"><?php echo $value;?></option>
			<?php endforeach;?>
		</select>

		<select id="province_id" name="province_id"></select>
		<select id="city_id" name="city_id"></select>

		</div>
</div>

<div class="tf_campaign_filter_age">
	<h5 class="tf_campaign_filter_title"><?php echo $this -> translate("Gender");?></h5>
	
	<div class="campaign_location">
		<select id="gender" name="gender">
			<option value="0"><?php echo $this->translate('Any')?></option>
			<option value="1"><?php echo $this->translate('Male')?></option>
			<option value="2"><?php echo $this->translate('Female')?></option>
		</select>
		
		<button type="submit" class="btn-filter"><?php echo $this -> translate('Filter');?></button>
	</div>
</div>
</form>
</div>
</div>
<script type="text/javascript">

jQuery( document ).ready(function() {
	<?php if(!empty($this -> params)) :?>
		 <?php $params = $this -> params;?>
		 
		 <?php if(!empty($params['title'])) :?>
		 	jQuery("#title").val('<?php echo $params['title'];?>');
		 <?php endif;?>
		 
		 <?php if(!empty($params['sort'])) :?>
		 	jQuery("#campaign-sort").val('<?php echo $params['sort'];?>');
		 <?php endif;?>
		 
		 <?php if(!empty($params['country_id'])) :?>
		 	jQuery("#country_id").val('<?php echo $params['country_id'];?>');
		 <?php endif;?>
		 
		 <?php if(!empty($params['start_date_from'])) :?>
		 	jQuery("#start_date_from").val('<?php echo $params['start_date_from'];?>');
		 <?php endif;?>
		 
		 <?php if(!empty($params['start_date_to'])) :?>
		 	jQuery("#start_date_to").val('<?php echo $params['start_date_to'];?>');
		 <?php endif;?>
		 
		 <?php if(!empty($params['gender'])) :?>
		 	jQuery("#gender").val('<?php echo $params['gender'];?>');
		 <?php endif;?>
		 
		 jQuery("#form-search-wrapper").css("display", "block");
		 
		 <?php 
			if (!empty($params['country_id']) && $params['country_id'] != "0") :
			
				$provincesAssoc = array();
				$country_id = $params['country_id'];
				if ($country_id) 
				{
					$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
					$provincesAssoc = array('0'=>'') + $provincesAssoc;
				}
			?>	
				<?php foreach($provincesAssoc as $key => $value) :?>
					jQuery('#province_id').append(jQuery('<option>', {
					    value: '<?php echo $key;?>',
					    text: '<?php echo str_replace("'","\'", $value);?>'
					}));
				<?php endforeach;?>
				<?php if (!empty($params['province_id'])):?>
					jQuery('#province_id').val('<?php echo $params['province_id'];?>');
				<?php endif;?>
			<?php endif;?>
			
			<?php 
			if (!empty($params['province_id'])):
				$citiesAssoc = array();
				$province_id = $params['province_id'];
				if ($province_id) {
					$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
					$citiesAssoc = array('0'=>'') + $citiesAssoc;
				}
			?>	
				<?php foreach($citiesAssoc as $key => $value) :?>
					jQuery('#city_id').append(jQuery('<option>', {
					    value: '<?php echo $key;?>',
					    text: '<?php echo str_replace("'","\'", $value);?>'
					}));
				<?php endforeach;?>
				<?php if (!empty($params['city_id'])):?>
					jQuery('#city_id').val('<?php echo $params['city_id'];?>');
				<?php endif;?>
			<?php endif;?>
		
		if ($$('#province_id option').length <= 1)
		{
			$('province_id').hide();
		}
		
		if ($$('#city_id option').length <= 1) 
		{
			$('city_id').hide();
		}
	
	<?php endif;?>
	
	jQuery("#form-search-wrapper").css("display", "block");
	
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
							'text': '<?php echo $this->translate('Any')?>'
						})  
                    	$('province_id').empty();
                    	$('province_id').grab(option);  
                    	$('province_id').adopt(options);
                    	$('province_id').show();
      				}
      				else {
      					$('province_id').empty();
      					$('province_id').hide();
      				}
      				$('city_id').empty();
      				$('city_id').hide();
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
							'text': '<?php echo $this->translate('Any')?>'
						})  
                    	$('city_id').empty();
                    	$('city_id').grab(option);
                    	$('city_id').adopt(options);
                    	$('city_id').show();
      				}
      				else {
      					$('city_id').empty();
      					$('city_id').hide();
      				}
      			}
      		})
      		makeRequest.send();
		});
	
});

jQuery(function () 
{
      jQuery( "#start_date_from" ).datepicker();
	  jQuery( "#start_date_to" ).datepicker();
	  jQuery('#rangeslider').slider({
	    range: true,
	    min: <?php echo $this->max_age_from?>,
	    max: <?php echo $this->max_age_to?>,
	    values: [ <?php echo $this->age_from?>, <?php echo $this->age_to?> ],
	    slide: function( event, ui ) {
    		var yearFrom = parseInt(<?php echo date('Y')?>) - parseInt(ui.values[0]);
	    	var yearTo = parseInt(<?php echo date('Y')?>) - parseInt(ui.values[1]);
	      jQuery('#from_age').val(ui.values[0]);
	      jQuery('#to_age').val( ui.values[1]);
	      jQuery('#display-from-age').html(ui.values[0]+" ("+yearFrom+")");
	      jQuery('#display-to-age').html(ui.values[1]+" ("+yearTo+")");
	    }
	  });
});
</script>