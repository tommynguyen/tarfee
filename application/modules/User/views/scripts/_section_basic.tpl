<?php 
    $label = Engine_Api::_()->user()->getSectionLabel($this->section);
    $viewer = Engine_Api::_()->user()->getViewer();
    $user = $this->user;
    $params = $this->params;
    $manage = ($viewer->getIdentity() == $user->getIdentity()) ;
	$canView = $manage || (!empty($params['view']));
    $create = (isset($params['create'])) ? $params['create'] : false;
	$this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
	$fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
	$location = $user->getLocation();
?>
<?php  if($canView && (count($fieldStructure) > 1 || !empty($location) || $create)) :?>
<?php if (!empty($params['view'])) $manage = false;?>	
<div class="icon_section_profile"><i class="fa fa-user"></i></div>
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
		<a href="javascript:void(0)" rel="basic" class="create-button"><?php echo '<i class="fa fa-pencil"></i>'?></a>
	</span>	
<?php endif;?>	
</div>

<div class="profile-section-loading" style="display: none; text-align: center">
    <img src='application/modules/User/externals/images/loading.gif'/>
</div>

<div class="profile-section-content">
<?php if ($create && $manage) : ?>
	<?php
	// General form w/o profile type
    	$aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    	$topLevelId = 0;
    	$topLevelValue = null;
    	if( isset($aliasedFields['profile_type']) ) {
      		$aliasedFieldValue = $aliasedFields['profile_type']->getValue($user);
      		$topLevelId = $aliasedFields['profile_type']->field_id;
      		$topLevelValue = ( is_object($aliasedFieldValue) ? $aliasedFieldValue->value : null );
      		if( !$topLevelId || !$topLevelValue ) {
        		$topLevelId = null;
        		$topLevelValue = null;
      		}
    	}
    
    	// Get form
    	$form  = new Fields_Form_Standard(array(
      		'item' => $user,
      		'topLevelId' => $topLevelId,
      		'topLevelValue' => $topLevelValue,
      		'hasPrivacy' => false
    	));
    	//$form->generate();
    
		$countriesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc(0);
		$countriesAssoc = array('0'=>'') + $countriesAssoc;
	
		$provincesAssoc = array();
		$country_id = $user->country_id;
		if ($country_id) {
			$provincesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($country_id);
			$provincesAssoc = array('0'=>'') + $provincesAssoc;
		}
	
		$form->addElement('Select', 'country_id', array(
			'label' => 'Country',
			'multiOptions' => $countriesAssoc,
			'value' => $country_id
		));
	
		$citiesAssoc = array();
		$province_id = $user->province_id;
		if ($province_id) {
			$citiesAssoc = Engine_Api::_()->getDbTable('locations', 'user')->getLocationsAssoc($province_id);
			$citiesAssoc = array('0'=>'') + $citiesAssoc;
		}
	
		$form->addElement('Select', 'province_id', array(
			'label' => 'Province/State',
			'multiOptions' => $provincesAssoc,
			'value' => $province_id
		));
	
		$city_id = $user->city_id;
		$form->addElement('Select', 'city_id', array(
			'label' => 'City',
			'multiOptions' => $citiesAssoc,
			'value' => $city_id
		));
		
		$continent = '';
		$country = Engine_Api::_()->getItem('user_location', $country_id);
		if ($country) $continent = $country->getContinent();
		$form->addElement('Text', 'continent', array(
			'label' => 'Continent',
			'value' => $continent,
			'disabled' => true
		));
		
		$form->setAttrib('id', 'basic_section-form');
		
		$form->submit->addDecorator('ViewHelper');
		
		$form->addElement('Button', 'cancel', array(
	      'label' => 'Cancel',
	      'order' => 10001,
	      'class' => 'basic-cancel-btn',
	      'decorators' => array(
	        'ViewHelper'
	      )
	    ));
		
	    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
	?>
	
	<?php
	  /* Include the common user-end field switching javascript */
	  echo $this->partial('_jsSwitch.tpl', 'fields', array(
	      'topLevelId' => (int) @$topLevelId,
	      'topLevelValue' => (int) @$topLevelValue
	    ))
	?>
	<div id="basic-section_form_div">
	<?php echo $form->render($this) ?>
	</div>
	
	<script>
		function addEventBasicForm() {
			if ($('province_id-wrapper') && $$('#province_id option').length <= 1) {
				$('province_id-wrapper').hide();
			}
			
			if ($('city_id-wrapper') && $$('#city_id option').length <= 1) {
				$('city_id-wrapper').hide();
			}
			
			if ($('continent-wrapper') && $('continent').value == '') {
				$('continent-wrapper').hide();
			}
			
			$$('.basic-cancel-btn').removeEvents('click');
			$$('.basic-cancel-btn').addEvent('click', function() {
				renderSection('basic', {});
			});
			
			if ($('country_id')) {
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
			}
			
			if ($('province_id')) {
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
			}
			
			if ($('basic_section-form')) {
				$('basic_section-form').removeEvents('submit');
				$('basic_section-form').addEvent('submit', function(e) {
					e.preventDefault();
					var params = this.toQueryString().parseQueryString();
					new Request.JSON({
				        url: '<?php echo $this->url(array('action' => 'save-basic'), 'user_general', true); ?>',
				        method: 'post',
				        data : params,
				        onComplete: function(responseJSON, responseText) {
				            if (responseJSON.status) {
				            	renderSection('basic', {});
				            }
				            else {
				            	var elements = Elements.from(responseJSON.html);
				            	$('basic-section_form_div').empty();
								$('basic-section_form_div').adopt(elements);
								addEventBasicForm();
				            }
				        }
				    }).send();
				})
			}
		}
		window.addEvent('domready', function() {
			addEventBasicForm();
		});
		
	</script>
<?php else: ?>
<?php echo $this->fieldValueLoop($user, $fieldStructure) ?>

<?php if (!empty($location)) :?>
<div class="profile_fields">
	<ul>
		<li>
			<span>
			<?php echo $this->translate('Location')?>		
			</span>
			<span>
			<?php echo implode(', ', $location)?>	
			</span>
		</li>
	</ul>
</div>
<?php endif;?>
<?php 
$laguages = json_decode($user -> languages);
$arr_tmp = array();
foreach ($laguages as $lang_id) 
{
	$langTb =  Engine_Api::_() -> getDbTable('languages', 'user');
	$lang = $langTb -> fetchRow($langTb ->select()->where('language_id = ?', $lang_id));
	if($lang)
		$arr_tmp[] = $lang -> title;
}
	if($arr_tmp):?>
	<div class="profile_fields">
		<ul>
			<li>
				<span>
				<?php echo $this->translate('Languages')?>		
				</span>
				<span>
				<?php echo implode(' | ', $arr_tmp);	?>
				</span>
			</li>
		</ul>
	</div>
	<?php endif;?>
<?php endif; ?>
</div>

<?php endif;?>