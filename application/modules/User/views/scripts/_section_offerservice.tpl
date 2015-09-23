<?php 
    $label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
    $create = (isset($params['create'])) ? $params['create'] : false;
	$edit = (isset($params['edit'])) ? $params['edit'] : false;
	$offerServices = $user->getAllOfferServices();
	$services = Engine_Api::_()->getDbTable('services', 'user')->getAllServices();
?>
 <?php if (($manage || count($offerServices)) && count($services)) : ?>
<div class="icon_section_profile"><i class="fa fa-file-text-o"></i></div>
<table>
  <tr>
  	<th><hr></th>  
  	<th><h3 class="section-label"><?php echo $this->translate($label);?></h3></th>
  	<th><hr></th>
  </tr>
</table>
<div class="profile-section-button">
<?php if ($manage) :?>
	<span class="manage-section-button">
		<a href="javascript:void(0)" rel="offerservice" class="create-button"><?php echo '<i class="fa fa-plus-square"></i>'?></a>
	</span>	
<?php endif;?>	
</div>	
<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>
    
<div class="profile-section-content">
<?php if ($create || $edit) : ?>
    <div id="profile-section-form-offerservice" class="profile-section-form">
        <form rel="offerservice" class="section-form">
            <p class="error"></p>
            <?php if ($edit && isset($params['item_id'])) : ?>
            <?php $item = Engine_Api::_()->getItem('user_offerservice', $params['item_id']);?>
            <input type="hidden" name="item_id" class="item_id" id="offerservice-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
            <?php endif; ?>
            
            <div id="offerservice-service_id-wrapper" class="profile-section-form-wrapper">                
                <label><?php echo $this->translate('*Service')?></label>
                <div class="profile-section-form-input">
                    <select name="service_id" id="offerservice-service_id" value="<?php if ($item) echo $item->service_id?>">
                        <?php foreach ($services as $service) : ?>
                        <option value="<?php echo $service->service_id?>" <?php if ($item && $item->service_id == $service->service_id) echo 'selected';?>><?php echo $this->translate($service->title)?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="error"></p>
                </div>
            </div>
            <div id="offerservice-service-wrapper" class="profile-section-form-wrapper" <?php if (!($item && $item->service_id == 9)) echo 'style="display: none"';?>>
                <label for="offerservice-service"></label>
                <div class="profile-section-form-input">
                    <input type="text" id="offerservice-service" name="title" value="<?php if ($item) echo $item->title; else echo '1'?>">
                    <p class="error"></p>
                </div>
            </div>
            <div id="offerservice-location-wrapper" class="profile-section-form-wrapper">
                <label for="offerservice-location"><?php echo $this->translate('Location')?></label>
                <div class="profile-section-form-input profile-section-form-input-map">
                    <input type="text" id="offerservice-location" name="location" value="<?php if ($item) echo $item->location?>"/>
                    <a class='profile-section_location_icon' href="javascript:void(0)" id='offerservice-get-current-location'>
                        <img src="<?php echo $this -> baseUrl();?>/application/modules/User/externals/images/icon-search-advform.png">
                    </a>
                    <input type="hidden" id="offerservice-longitude" name="longitude" value="<?php if ($item) echo $item->longitude?>"/>
                    <input type="hidden" id="offerservice-latitude" name="latitude" value="<?php if ($item) echo $item->latitude?>"/>
                	<p class="error"></p>	
                </div>
            </div>
            
            <div class="profile-section-form-buttons">
                <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
                <button rel="offerservice" type="button" class="cancel-btn"><?php echo $this->translate('Cancel')?></button>
                <?php if ($edit && isset($params['item_id'])) : ?>
                <?php echo $this->translate(' or ')?>
                <a href="javascript:void(0);" class="remove-btn"><?php echo $this->translate('Remove Service')?></a>
                <?php endif; ?>                
            </div>
            
            <script type="text/javascript">
        	window.addEvent('domready', function() {
        		$('offerservice-get-current-location').addEvent('click', function(){
                    getCurrentLocation();   
                });
                
                initialize();
                google.maps.event.addDomListener(window, 'load', initialize);
                
                $('offerservice-service_id').addEvent('change', function(){
                   if(this.value == 9)
                   {
                   		$('offerservice-service-wrapper').style.display = 'block';
                   		$('offerservice-service').value = '';
                   }
                   else
                   {
                   		
                   		$('offerservice-service-wrapper').style.display = 'none';
                   		$('offerservice-service').value = this.value;
                   }
                });
        	});
        	
        	function initialize() {
                var input = /** @type {HTMLInputElement} */(
                    document.getElementById('offerservice-location'));
            
                var autocomplete = new google.maps.places.Autocomplete(input);
            
                google.maps.event.addListener(autocomplete, 'place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        return;
                    }
                    document.getElementById('offerservice-latitude').value = place.geometry.location.lat();     
                    document.getElementById('offerservice-longitude').value = place.geometry.location.lng();
                });
            }
          
            function getCurrentLocation () {   
                if(navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                    var pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    if(pos){
                        current_posstion = new Request.JSON({
                            'format' : 'json',
                            'url' : '<?php echo $this->url(array('action'=>'get-my-location'), 'user_general') ?>',
                            'data' : {
                                latitude : pos.lat(),
                                longitude : pos.lng(),
                            },
                            'onSuccess' : function(json, text) {
                                if(json.status == 'OK') {
                                    document.getElementById('offerservice-location').value = json.results[0].formatted_address;
                                    document.getElementById('offerservice-latitude').value = json.results[0].geometry.location.lat;     
                                    document.getElementById('offerservice-longitude').value = json.results[0].geometry.location.lng;        
                                }
                                else {
                                    handleNoGeolocation(true);
                                }
                            }
                        }); 
                        current_posstion.send();
                    }
                    }, function() {
                        handleNoGeolocation(true);
                    });
                }
                else {
                    // Browser doesn't support Geolocation
                    handleNoGeolocation(false);
                }
                return false;
            }
            
            function handleNoGeolocation(errorFlag) {
                if (errorFlag) {
                    document.getElementById('offerservice-location').value = 'Error: The Geolocation service failed.';
                } 
                else {
                    document.getElementById('offerservice-location').value = 'Error: Your browser doesn\'t support geolocation.';
                }
            }
            </script>            
        </form>
    </div>
<?php endif;?>
	<div class="profile-section-list">
		<?php if (count($offerServices)) : ?>
		<ul id="offerservice-list" class="section-list">
		<?php foreach ($offerServices as $item) :?>
			<li class="section-item" id="offerservice-<?php echo $item->getIdentity()?>">
				<div class="sub-section-item">
					<div class="offerservice-service section-title"><?php echo $item->getTitle()?></div>
					<div class="offerservice-location">
						<span class="icon"><i class="fa fa-map-marker"></i></span>
						<span class="location"><?php echo $item->location?></span>
					</div>
					<?php if ($manage) : ?>
		            <a href="javascript:void(0);" class="edit-btn"><i class="fa fa-pencil"></i></a>
		            <?php endif; ?>
	            </div>
			</li>
		<?php endforeach;?> 
		</ul>
		<?php else:?>
		<div class="tip">
			<span><?php echo $this->translate('You don\'t have any offer services!')?></span>
		</div>
		<?php endif;?>
	</div>
<?php endif;?>
