<link rel="stylesheet" type="text/css" media="all" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/jquery-ui.min.js"></script>

<style>
	.search-form-item {
		display: none;
	}
	
	.search-form-item.active {
		display: block;
	}
	
</style>
<div id="basic-search-filter" style="display:none; position: absolute">
	<div id="contentype-filter">
		<h3><?php echo $this->translate('Content Type')?></h3>
		<ul>
			<li>
				<input id="type-all" type="checkbox" name="type[]" <?php if (in_array('all', $this->type)) echo 'checked'?> class="type-checkbox type" value="all"/>
				<label for="type-all"><?php echo $this->translate('all content types')?></label>
			</li>
			<?php $allowType = Engine_Api::_()->ynadvsearch()->getAllowSearchTypes();?>
			<?php foreach($allowType as $key => $value):?>
			<li>
				<input id="type-<?php echo $key?>" type="checkbox" name="type[]" <?php if (in_array($key, $this->type)) echo 'checked'?> class="type-checkbox type" value="<?php echo $key?>"/>
				<label for="type-<?php echo $key?>"><?php echo $this->translate($value)?></label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div id="sport-filter">
		<h3><?php echo $this->translate('Sport')?></h3>
		<ul>
			<li>
				<input id="sport-all" type="checkbox" name="sport[]" <?php if (in_array('all', $this->sport)) echo 'checked'?> class="sport-type-checkbox type-checkbox" value="all"/>
				<label for="sport-all"><?php echo $this->translate('all sport types')?></label>
			</li>
			<?php $sports = Engine_Api::_()->getDbTable('sportcategories', 'user')->getCategoriesLevel1();?>
			<?php foreach($sports as $sport):?>
			<li>
				<input id="sport-<?php echo $sport->getIdentity()?>" type="checkbox" name="sport[]" <?php if (in_array($sport->getIdentity(), $this->sport)) echo 'checked'?> class="sport-type-checkbox type-checkbox" value="<?php echo $sport->getIdentity()?>"/>
				<label for="sport-<?php echo $sport->getIdentity()?>"><?php echo $this->translate($sport->getTitle())?></label>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php if ($this->viewer()->getIdentity()):?>
<div id="advanced-search-filter" style="display:none; position: absolute">
	<ul id="advanced-search-tab" class="advanced-search-tab">
		<li class="search-tab-item active" id="search-tab-item_player"><?php echo $this->translate('Player')?></li>	
		<li class="search-tab-item" id="search-tab-item_professional"><?php echo $this->translate('Professional')?></li>
		<li class="search-tab-item" id="search-tab-item_organization"><?php echo $this->translate('Organization')?></li>
		<li class="search-tab-item" id="search-tab-item_event"><?php echo $this->translate('Event/Tryout')?></li>
		<li class="search-tab-item" id="search-tab-item_campaign"><?php echo $this->translate('Campaign')?></li>
	</ul>
	<div class="search-form" id="advsearch-form">
		<?php $url = $this->url(array(),'ynadvsearch_search',true)?>
		
		<div class="search-form-item active" id="player-advanced-search">
			<form class="advsearch-form" method="post" action="<?php echo $url?>">
				<input name="advsearch" value="player" type="hidden"/>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_continent"><?php echo $this->translate('Continent')?></label>
					<select class="form-element search-element continent" id="player_continent" rel="player" name="continent">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->continents as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_country_id"><?php echo $this->translate('Country')?></label>
					<select class="form-element search-element country_id" rel="player" id="player_country_id" name="country_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_province_id"><?php echo $this->translate('Province/State')?></label>
					<select class="form-element search-element province_id" rel="player" id="player_province_id" name="province_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_city_id"><?php echo $this->translate('City')?></label>
					<select class="form-element search-element" id="player_city_id" name="city_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_keyword"><?php echo $this->translate('Keyword')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('keyword...')?>" class="form-element search-element" id="player_keyword" name="keyword" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_sport"><?php echo $this->translate('Sport Type')?></label>
					<select class="form-element search-element" id="player_sport" name="sport">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->sports as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_position_id"><?php echo $this->translate('Sport Position')?></label>
					<select class="form-element search-element" id="player_position_id" name="position_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_gender"><?php echo $this->translate('Gender')?></label>
					<select class="form-element search-element" id="player_gender" name="gender">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<option value="1"><?php echo $this->translate('Male')?></option>
						<option value="2"><?php echo $this->translate('Female')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label"><?php echo $this->translate('Age: ')?><span id="age-rangeval"><?php echo $this->age_from?> (<?php echo (intval(date('Y')) - intval($this->age_from))?>) - <?php echo $this->age_to?> (<?php echo (intval(date('Y')) - intval($this->age_to))?>)</span></label>
					<input type="hidden" class="form-element search-element" value="<?php echo $this->age_from?>" id="player_age_from" name="age_from"/>
					<input type="hidden" class="form-element search-element" value="<?php echo $this->age_to?>" id="player_age_to" name="age_to"/>
					<div id="age-rangeslider"></div>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="player_relation_id"><?php echo $this->translate('Posted by')?></label>
					<select class="form-element search-element" id="player_relation_id" name="relation_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->relations as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label"><?php echo $this->translate('Professional Rating: ')?><span id="rating-rangeval"><?php echo $this->rating_from?> - <?php echo $this->rating_to?></span></label>
					<input type="hidden" class="form-element search-element" value="<?php echo $this->rating_from?>" id="player_rating_from" name="rating_from"/>
					<input type="hidden" class="form-element search-element" value="<?php echo $this->rating_to?>" id="player_rating_to" name="rating_to"/>
					<div id="rating-rangeslider"></div>
				</div>
				<button type="submit"><?php echo $this->translate('Search')?></button>
			</form>
		</div>
		
		<div class="search-form-item" id="professional-advanced-search">
			<form class="advsearch-form" method="post" action="<?php echo $url?>">
				<input name="advsearch" value="professional" type="hidden"/>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_continent"><?php echo $this->translate('Continent')?></label>
					<select class="form-element search-element continent" id="professional_continent" rel="professional" name="continent">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->continents as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_country_id"><?php echo $this->translate('Country')?></label>
					<select class="form-element search-element country_id" rel="professional" id="professional_country_id" name="country_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_province_id"><?php echo $this->translate('Province/State')?></label>
					<select class="form-element search-element province_id" rel="professional" id="professional_province_id" name="province_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>

				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_city_id"><?php echo $this->translate('City')?></label>
					<select class="form-element search-element" id="professional_city_id" name="city_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_keyword"><?php echo $this->translate('Keyword')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('keyword...')?>" class="form-element search-element" id="professional_keyword" name="keyword" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_displayname"><?php echo $this->translate('Name')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('name...')?>" class="form-element search-element" id="professional_displayname" name="displayname" />
				</div>
				
				<!-- <div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_role"><?php echo $this->translate('Role')?></label>
					<select class="form-element search-element" id="professional_role" name="role">
						<option value="any"><?php echo $this->translate('Any')?></option>
						<option value="campaign"><?php echo $this->translate('Campaign')?></option>
						<option value="agent"><?php echo $this->translate('Agent')?></option>
						<option value="coach"><?php echo $this->translate('Coach')?></option>
						<option value="journalist"><?php echo $this->translate('Journalist')?></option>
						<option value="admin"><?php echo $this->translate('Admin')?></option>
						<option value="medical"><?php echo $this->translate('Medical')?></option>
					</select>
				</div> -->
				
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="professional_service"><?php echo $this->translate('Services Offered')?></label>
					<select class="form-element search-element" id="professional_service" name="service">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->services as $service):?>
						<option value="<?php echo $service->service_id?>"><?php echo $this->translate($service->title)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<button type="submit"><?php echo $this->translate('Search')?></button>
			</form>
		</div>
		
		<div class="search-form-item" id="organization-advanced-search">
			<form class="advsearch-form" method="post" action="<?php echo $url?>">
				<input name="advsearch" value="organization" type="hidden"/>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_continent"><?php echo $this->translate('Continent')?></label>
					<select class="form-element search-element continent" id="organization_continent" rel="organization" name="continent">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->continents as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_country_id"><?php echo $this->translate('Country')?></label>
					<select class="form-element search-element country_id" rel="organization" id="organization_country_id" name="country_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_province_id"><?php echo $this->translate('Province/State')?></label>
					<select class="form-element search-element province_id" rel="organization" id="organization_province_id" name="province_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_city_id"><?php echo $this->translate('City')?></label>
					<select class="form-element search-element" id="organization_city_id" name="city_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_keyword"><?php echo $this->translate('Keyword')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('keyword...')?>" class="form-element search-element" id="organization_keyword" name="keyword" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_displayname"><?php echo $this->translate('Name')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('name...')?>" class="form-element search-element" id="organization_displayname" name="displayname" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_sport"><?php echo $this->translate('Sport Type')?></label>
					<select class="form-element search-element" id="organization_sport" name="sport">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->sports as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
					</select>
				</div>
				
				<!-- <div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="organization_type"><?php echo $this->translate('Type')?></label>
					<select class="form-element search-element" id="organization_type" name="organization_type">
						<option value="0"></option>
						<option value="club"><?php echo $this->translate('Club')?></option>
						<option value="agency"><?php echo $this->translate('Agency')?></option>
						<option value="academy"><?php echo $this->translate('Academy')?></option>
						<option value="none_profit"><?php echo $this->translate('Non-for-Profit')?></option>
					</select>
				</div> -->
				<button type="submit"><?php echo $this->translate('Search')?></button>
			</form>
		</div>
		
		<div class="search-form-item" id="event-advanced-search">
			<form class="advsearch-form" method="post" action="<?php echo $url?>">
				<input name="advsearch" value="event" type="hidden"/>

				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_continent"><?php echo $this->translate('Continent')?></label>
					<select class="form-element search-element continent" id="event_continent" rel="event" name="continent">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->continents as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>

				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_country_id"><?php echo $this->translate('Country')?></label>
					<select class="form-element search-element country_id" rel="event" id="event_country_id" name="country_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_province_id"><?php echo $this->translate('Province/State')?></label>
					<select class="form-element search-element province_id" rel="event" id="event_province_id" name="province_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_city_id"><?php echo $this->translate('City')?></label>
					<select class="form-element search-element" id="event_city_id" name="city_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>

				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_keyword"><?php echo $this->translate('Keyword')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('keyword...')?>" class="form-element search-element" id="event_keyword" name="keyword" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_type"><?php echo $this->translate('Type')?></label>
					<select class="form-element search-element" id="event_event_type" name="event_type">
						<option value=""><?php echo $this -> translate('any')?></option>
						<option value="0"><?php echo $this->translate('Event')?></option>
						<option value="1"><?php echo $this->translate('Tryout')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="event_sport"><?php echo $this->translate('Sport Type')?></label>
					<select class="form-element search-element" id="event_sport" name="sport">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->sports as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
					</select>
				</div>
				<button type="submit"><?php echo $this->translate('Search')?></button>
			</form>
		</div>
		
		<div class="search-form-item" id="campaign-advanced-search">
			<form class="advsearch-form" method="post" action="<?php echo $url?>">
				<input name="advsearch" value="campaign" type="hidden"/>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_continent"><?php echo $this->translate('Continent')?></label>
					<select class="form-element search-element continent" id="campaign_continent" rel="campaign" name="continent">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->continents as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $this->translate($value)?></option>
						<?php endforeach;?>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_country_id"><?php echo $this->translate('Country')?></label>
					<select class="form-element search-element country_id" rel="campaign" id="campaign_country_id" name="country_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_province_id"><?php echo $this->translate('Province/State')?></label>
					<select class="form-element search-element province_id" rel="campaign" id="campaign_province_id" name="province_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_city_id"><?php echo $this->translate('City')?></label>
					<select class="form-element search-element" id="campaign_city_id" name="city_id">
						<option value="0"><?php echo $this -> translate('any')?></option>
					</select>
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_keyword"><?php echo $this->translate('Keyword')?></label>
					<input type="text" placeholder="<?php echo $this -> translate('keyword...')?>" class="form-element search-element" id="campaign_keyword" name="keyword" />
				</div>
				<div class="form-wrapper search-wrapper">
					<label class="form-label search-label" for="campaign_sport"><?php echo $this->translate('Sport Type')?></label>
					<select class="form-element search-element" id="campaign_sport" name="sport">
						<option value="0"><?php echo $this -> translate('any')?></option>
						<?php foreach ($this->sports as $key => $value):?>
						<option value="<?php echo $key?>"><?php echo $value?></option>
						<?php endforeach;?>
					</select>
				</div>
				<button type="submit"><?php echo $this->translate('Search')?></button>
			</form>
		</div>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/jquery.tokeninput.js"></script>
<script>
jQuery.noConflict();
jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
(function($) { 
	$(document).ready(function () {
		
		$('.sport-type-checkbox').on('click', function() {
			var id = $(this).attr('id');
			if (id == 'sport-all') {
				if ($(this).is(':checked')) {
					$('.sport-type-checkbox').prop('checked', true);
				}
				else {
					$('.sport-type-checkbox').prop('checked', false);
				}
			}
			else {
				if (!$(this).is(':checked')) {
					$('#sport-all').prop('checked', false);
				}
			}
		});
		
		$('.type-checkbox.type').on('click', function() {
			var id = $(this).attr('id');
			if (id == 'type-all') {
				if ($(this).is(':checked')) {
					$('.type-checkbox.type').prop('checked', true);
				}
				else {
					$('.type-checkbox.type').prop('checked', false);
				}
			}
			else {
				if (!$(this).is(':checked')) {
					$('#type-all').prop('checked', false);
				}
			}
		});
		
		<?php if ($this->viewer()->getIdentity()):?>
		
		<?php if (!$this->isPro) :?>
		$('.advsearch-form input').prop('disabled', true);
		$('.advsearch-form select').prop('disabled', true);
		$('.advsearch-form button[type="submit"]').text('<?php echo $this->translate('Professional for free')?>');
		$('.advsearch-form button[type="submit"]').on('click', function(e) {
			e.preventDefault();
			var url = '<?php echo $this->url(array('controller' => 'settings','action' => 'index','module' => 'payment'), 'default', true); ?>';
			window.location.href = url;
		});
		<?php endif;?>
		
		$('.search-tab-item').on('click', function() {
			var index = $('#advanced-search-tab').children('li.search-tab-item').index($(this));
			$('.search-tab-item').removeClass('active');
			$(this).addClass('active');
			index = index+1;
			$('.search-form-item').removeClass('active');
	
			$('.search-form-item:nth-child('+index+')').addClass('active');
		});
		
		$('.continent').on('change', function() {
			var continent = $(this).val();
			var type = $(this).attr('rel');
			$.ajax({
	  			url: "<?php echo $this->url(array('action'=>'get-countries'),'user_general', true)?>?continent="+continent,
	  			success : function (respone){
					respone  = $.trim(respone);
					var option = $('<option />', {
						'value': '0',
						'text': '<?php echo $this -> translate('any')?>'
					});
	              	if(respone.length > 0) {
	                	$('#'+type+'_country_id').empty();
	                	$('#'+type+'_country_id').append(option);
	                	$('#'+type+'_country_id').append(respone);
	  				}
	  				else {
	  					$('#'+type+'_country_id').empty();
	  					$('#'+type+'_country_id').append(option);
	  				}
	  			}
	  		});
		});
		
		$('.country_id').on('change', function() {
			var id = $(this).val();
			var type = $(this).attr('rel');
			$.ajax({
	  			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
	  			success: function (respone){
					respone  = $.trim(respone);
					var option = $('<option />', {
						'value': '0',
						'text': '<?php echo $this -> translate('any')?>'
					});
	              	if(respone.length > 0) {
	                	$('#'+type+'_province_id').empty();
	                	$('#'+type+'_province_id').append(option);
	                	$('#'+type+'_province_id').append(respone);
	  				}
	  				else {
	  					$('#'+type+'_province_id').empty();
	  					$('#'+type+'_province_id').append(option);
	  				}
	  			}
	  		});
		});
		
		$('.province_id').on('change', function() {
			var id = $(this).val();
			var type = $(this).attr('rel');
			$.ajax({
	  			url: "<?php echo $this->url(array('action'=>'sublocations'),'user_general', true)?>/location_id/"+id,
	  			success: function (respone){
					respone  = $.trim(respone);
					var option = $('<option />', {
						'value': '0',
						'text': '<?php echo $this -> translate('any')?>'
					});
	              	if(respone.length > 0) {
	                	$('#'+type+'_city_id').empty();
	                	$('#'+type+'_city_id').append(option);
	                	$('#'+type+'_city_id').append(respone);
	  				}
	  				else {
	  					$('#'+type+'_city_id').empty();
	  					$('#'+type+'_city_id').append(option);
	  				}
	  			}
	  		})
		});
		
		$('#player_sport').on('change', function() {
			var id = $(this).val();
			var type = $(this).attr('rel');
			$.ajax({
	  			url: "user/player-card/subcategories/cat_id/"+id,
	  			success: function (respone){
					respone = $.trim(respone);
					$('#player_position_id').empty();
					if (respone != "") {
						$('#player_position_id').html('<option value="0"><?php echo $this -> translate('any')?></option>' + respone);
					}
					else {
						$('#player_position_id').html('<option value="0"><?php echo $this -> translate('any')?></option>');
					}
	  			}
	  		})
		});
		<?php endif;?>
		if ($('#age-rangeslider')) {
			$('#age-rangeslider').slider({
			    range: true,
			    min: <?php echo $this->max_age_from?>,
			    max: <?php echo $this->max_age_to?>,
			    values: [ <?php echo $this->age_from?>, <?php echo $this->age_to?> ],
			    slide: function( event, ui ) {
			    	var yearFrom = parseInt(<?php echo date('Y')?>) - parseInt(ui.values[0]);
			    	var yearTo = parseInt(<?php echo date('Y')?>) - parseInt(ui.values[1]);
			      	$('#age-rangeval').html(ui.values[0]+" ("+yearFrom+")"+" - "+ui.values[1]+" ("+yearTo+")");
			      	$('#player_age_from').val(ui.values[0]);
			      	$('#player_age_to').val(ui.values[1]);
			    }
		  	});
		}
		
		if ($('#rating-rangeslider')) {
			$('#rating-rangeslider').slider({
			    range: true,
			    min: 0,
			    max: 5,
			    step: 0.1,
			    values: [ <?php echo $this->rating_from?>, <?php echo $this->rating_to?> ],
			    slide: function( event, ui ) {
			      	$('#rating-rangeval').html(ui.values[0]+" - "+ui.values[1]);
			      	$('#player_rating_from').val(ui.values[0]);
			      	$('#player_rating_to').val(ui.values[1]);
			    }
		  	});
		}
		
		//populate adv search
		<?php if(!empty($this->params['advsearch'])) :?>
		var advsearch = '<?php echo $this->params['advsearch']?>';
		
		<?php if ($this->countriesOption) :?>
		var options = $('<select />', {}).html('<?php echo $this->string()->escapeJavascript($this->countriesOption) ?>').text();
		var field = advsearch+'_country_id';
		$('#'+field).append(options);
		<?php endif;?>
		
		<?php if ($this->provincesOption) :?>
		var options = $('<select />', {}).html('<?php echo $this->string()->escapeJavascript($this->provincesOption) ?>').text();
		var field = advsearch+'_province_id';
		$('#'+field).append(options);
		<?php endif;?>
		
		<?php if ($this->citiesOption) :?>
		var options = $('<select />', {}).html('<?php echo $this->string()->escapeJavascript($this->citiesOption) ?>').text();
		var field = advsearch+'_city_id';
		$('#'+field).append(options);
		<?php endif;?>
		
		<?php if ($this->positionsOption) :?>
		var options = $('<select />', {}).html('<?php echo $this->string()->escapeJavascript($this->positionsOption) ?>').text();
		$('#player_position_id').append(options);
		<?php endif;?>
		
		<?php foreach ($this->params as $key => $value) :?>
		var field = advsearch+'_<?php echo $key;?>';
		if ($('#'+field)) {
			$('#'+field).val('<?php echo $value?>');
		} 
		<?php endforeach;?>
		
		$('#search-tab-item_'+advsearch).click();
		<?php endif;?>
		
		var options =  {
            theme: "facebook"
            , method: "POST"
            , noResultsText: '<?php echo $this->translate('No keywords found.')?>'
            , searchingText: '<?php echo $this->translate('Searching...')?>'
            , placeholder: '<?php echo $this->translate('Enter keyword')?>'
            , preventDuplicates: true
            , hintText: ''
            , allowFreeTagging: true
            , animateDropdown: false
            , prePopulate : <?php echo json_encode($this->tokens)?>
            <?php if ($this->max_keywords) :?>
            , tokenLimit: <?php echo $this->max_keywords?>
            <?php endif; ?>
        };
		$('#global_search_field').tokenInput('<?php echo $this->url(array('action'=>'suggest-keywords'), 'ynadvsearch_suggest', true)?>', options);
	
		var form = $('#global_search_form');
		if (form) {
			var button = $('<button />', {
				type: 'button',
				class: 'btn-search-main',
				text: '<?php echo $this->translate('Search')?>',
				click: function() {
					var searchForm = $(this).closest('#global_search_form');
					var query = searchForm.find('#global_search_field');
					var input = searchForm.find('#token-input-global_search_field');
					var values = query.tokenInput('get');
					var arr = [];
					for (var i = 0; i < values.length; i++) {
						arr.push(values[i].name);
					}
					if (input.val() != '') {
						arr.push(input.val());
					}
					query.val(arr.join());
					searchForm.submit();
				}	
			});
			form.append(button);
			
			//form.attr('method', 'POST');
			var filter = $('<div />', {
				id: 'search-filter',
				'class': 'box-search_form_filter'
			}).append(
				$('<span />', {
					'class': 'global_search_form_filter_advanced',
					text: '<?php echo $this->translate('filter')?>',
					click: function() {
						var parent = $(this).closest('#search-filter');
						var div_filter = parent.find('#basic-search-filter');
						div_filter.fadeToggle(400);
					}
				}).append(
					$('<i />', {
						'class': 'fa fa-angle-down',
					})
				)
			);
			
			$("#basic-search-filter").detach().appendTo(filter);
			form.append(filter);
			
			<?php if ($this->viewer()->getIdentity()):?>
			var advsearch = $('<div />', {
				id: 'search-advsearch',
				'class': 'box-search_form_advsearch'
			}).append(
				$('<span />', {
					'class': 'global_search_form_filter_advanced',
					text: '<?php echo $this->translate('advanced ')?>',
					click: function() {
						var parent = $(this).closest('#search-advsearch');
						var div_filter = parent.find('#advanced-search-filter');
						div_filter.fadeToggle(400);
					}
				}).append(
					$('<i />', {
						'class': 'fa fa-angle-down',
					})
				)
			);
			
			$("#advanced-search-filter").detach().appendTo(advsearch);
			form.append(advsearch);
			<?php endif;?>
		}
		
		<?php if ($this->viewer()->getIdentity()):?>
		$('#search-filter .global_search_form_filter_advanced').click(function() {
			$('#advanced-search-filter').css('display','none');
		});

		
		$('#search-advsearch .global_search_form_filter_advanced').click(function() {
			$('#basic-search-filter').css('display','none');
		});
		
		<?php endif;?>

      	$(document).mouseup(function (e){
			var advanced_box = $('#search-advsearch');

			if (!advanced_box.is(e.target) // if the target of the click isn't the container...
			  && advanced_box.has(e.target).length === 0) // ... nor a descendant of the container
			{
			  advanced_box.find('#advanced-search-filter').hide();
			}

			var filter_box = $('#search-filter');

			if (!filter_box.is(e.target) // if the target of the click isn't the container...
			  && filter_box.has(e.target).length === 0) // ... nor a descendant of the container
			{
			  filter_box.find('#basic-search-filter').hide();
			}
      	});

	});
})(jQuery);
</script>
