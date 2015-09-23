<?php 
    $label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
    $create = (isset($params['create'])) ? $params['create'] : false;
	$edit = (isset($params['edit'])) ? $params['edit'] : false; 
	$education = $user->getAllEducations();
	$enable = Engine_Api::_()->user()->checkSectionEnable($user, 'education');
?>
<?php if (($manage || count($education)) && $enable) : ?>
<div class="icon_section_profile"><i class="fa fa-graduation-cap"></i></div>
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
			<a href="javascript:void(0)" rel="education" class="create-button"><?php echo '<i class="fa fa-plus-square"></i>'?></a>
		</span>	
	<?php endif;?>	
	</div>

	<div class="profile-section-loading" style="display: none; text-align: center">
	    <img src='application/modules/User/externals/images/loading.gif'/>
	</div>

	<div class="profile-section-content">
		<?php if ($create || $edit) : ?>
   			<div id="profile-section-form-education" class="profile-section-form">
        		<form rel="education" class="section-form">
	            <p class="error"></p>
	            <?php $item = null;?>
	            <?php if ($edit && isset($params['item_id'])) : ?>
	            <?php $item = Engine_Api::_()->getItem('user_education', $params['item_id']);?>
	            	<input type="hidden" name="item_id" class="item_id" id="education-<?php echo $item->getIdentity()?>" value=<?php echo $item->getIdentity()?> />
	            <?php endif; ?>
	            <div id="education-degree-wrapper" class="profile-section-form-wrapper">
	                <label for="education-degree"><?php echo $this->translate('*Degree')?></label>
	                <div class="profile-section-form-input">                        
	                    <input type="text" id="education-degree" name="degree" value="<?php if ($item) echo $item->degree?>"/>
	                    <p class="error"></p>
	                </div>
	            </div>
            	<div id="education-institute-wrapper" class="profile-section-form-wrapper">
	                <label for="education-institute"><?php echo $this->translate('*Institute')?></label>
	                <div class="profile-section-form-input">                        
	                    <input type="text" id="education-institute" name="institute" value="<?php if ($item) echo $item->institute?>"/>
	                    <p class="error"></p>
	                </div>
            	</div>
            	<div id="education-year_attended-wrapper" class="profile-section-form-wrapper">                
	                <label><?php echo $this->translate('Year Attended')?></label>
	                <div class="profile-section-form-input form-input-2item">
	                    <?php $curYear = intval(date("Y"));?>
	                    <?php $maxYear = intval(date("Y")) + 10;?>
	                    <select name="attend_from" id="education-attend_from" value="<?php if ($item) echo $item->attend_from?>">
	                        <option value="0000"><?php echo $this->translate('-')?></option>
	                        <?php for ($i = $curYear; $i >= 1900; $i--) : ?>
	                        <option value="<?php echo $i?>" <?php if ($item && $item->attend_from == $i) echo 'selected';?>><?php echo $this->translate($i)?></option>
	                        <?php endfor; ?>
	                    </select>
	                     - 
	                    <select name="attend_to" id="education-attend_to" value="<?php if ($item) echo $item->attend_to?>">
	                        <option value="0000"><?php echo $this->translate('-')?></option>
	                        <?php for ($i = $maxYear; $i >= 1900; $i--) : ?>
	                        <option value="<?php echo $i?>" <?php if ($item && $item->attend_to == $i) echo 'selected';?>><?php echo $this->translate($i)?></option>
	                        <?php endfor; ?>
	                    </select>
	                    <p class="error"></p>
	                </div>
            	</div>
                
	            <div id="education-location-wrapper" class="profile-section-form-wrapper">
	                <label for="education-location"><?php echo $this->translate('Location')?></label>
	                <div class="profile-section-form-input profile-section-form-input-map">
	                	<p class="error"></p>
	                    <input type="text" id="education-location" name="location" value="<?php if ($item) echo $item->location?>"/>
	                    <a class='profile-section_location_icon' href="javascript:void(0)" id='education-get-current-location'>
	                        <img src="<?php echo $this -> baseUrl();?>/application/modules/User/externals/images/icon-search-advform.png">
	                    </a>
	                    <input type="hidden" id="education-longitude" name="longitude" value="<?php if ($item) echo $item->longitude?>"/>
	                    <input type="hidden" id="education-latitude" name="latitude" value="<?php if ($item) echo $item->latitude?>"/>
	                </div>
	            </div>
            
	            <div class="profile-section-form-buttons">
	                <button type="submit" id="submit-btn"><?php echo $this->translate('Save')?></button>
	                <button rel="education" type="button" class="cancel-btn"><?php echo $this->translate('Cancel')?></button>
	                <?php if ($edit && isset($params['item_id'])) : ?>
	                <?php echo $this->translate(' or ')?>
	                <a href="javascript:void(0);" class="remove-btn"><?php echo $this->translate('Remove Education')?></a>
	                <?php endif; ?>                
	            </div>
            
	            <script type="text/javascript">
	        	window.addEvent('domready', function() {
	        		$('education-get-current-location').addEvent('click', function(){
	                    getCurrentLocation();   
	                });
	                
	                initialize();
	                google.maps.event.addDomListener(window, 'load', initialize);
	        	});
	        	
	        	function initialize() {
	                var input = /** @type {HTMLInputElement} */(
	                    document.getElementById('education-location'));
	            
	                var autocomplete = new google.maps.places.Autocomplete(input);
	            
	                google.maps.event.addListener(autocomplete, 'place_changed', function() {
	                    var place = autocomplete.getPlace();
	                    if (!place.geometry) {
	                        return;
	                    }
	                    document.getElementById('education-latitude').value = place.geometry.location.lat();     
	                    document.getElementById('education-longitude').value = place.geometry.location.lng();
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
	                                    document.getElementById('education-location').value = json.results[0].formatted_address;
	                                    document.getElementById('education-latitude').value = json.results[0].geometry.location.lat;     
	                                    document.getElementById('education-longitude').value = json.results[0].geometry.location.lng;        
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
	                    document.getElementById('education-location').value = 'Error: The Geolocation service failed.';
	                } 
	                else {
	                    document.getElementById('education-location').value = 'Error: Your browser doesn\'t support geolocation.';
	                }
	            }
	            </script>            
        </form>
    		</div>
		<?php endif;?>
		<div class="profile-section-list">
    		<?php if (count($education) > 0) : ?>
        		<ul id="education-list" class="section-list">
		        <?php foreach ($education as $item) :?>
		        <li class="section-item" id="education-<?php echo $item->getIdentity()?>">
		            <div class="sub-section-item">
		                <div class="education-degree section-title">
		                	<span><?php echo $item->degree?></span>
		                </div>
		                
		                <div class="education-institute">
		                	<span><?php echo $item->institute?></span>
		                </div>
		                
		                <div class="education-time time">
		                    <?php if ($item->attend_from >= 1900) : ?>
		                    <span class="from"><?php echo $item->attend_from?></span>
		                    <?php endif;?>
		
		                    <?php if ($item->attend_from >= 1900 && $item->attend_to >= 1900) : ?>
		                    <span>-</span>
		                    <?php endif;?>
		
		                    <?php if ($item->attend_to >= 1900) : ?>
		                    <span class="to"><?php echo $item->attend_to?></span>
		                    <?php endif;?>
		                </div>
		                
		                <div class="education-location">
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
	    	<?php else: ?>
			    <div class="tip">
					<span><?php echo $this->translate('You don\'t have any educcation!')?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
