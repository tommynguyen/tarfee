<?php
    $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.wookmark.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.imagesloaded.js');
?>

<?php $campaign = $this -> campaign;?>

<!-- filter -->
<select id="filter-submission">
	<option value="matching"><?php echo $this -> translate("% of matching");?></option>
	<option value="rating"><?php echo $this -> translate("rating");?></option>
	<option value="location"><?php echo $this -> translate("location");?></option>
	<option value="age"><?php echo $this -> translate("age");?></option>
	<option value="gender"><?php echo $this -> translate("gender");?></option>
</select>

<br>

<!-- submissionPlayers -->   

<?php if(count($this -> submissionPlayers)) :?>

<ul class="profile-submission" id="profile-submission-pin">
<?php foreach($this -> submissionPlayers as $submissionPlayer) :?>
	<li class="item-profile-submission">
	<?php $player = Engine_Api::_() -> getItem('user_playercard', $submissionPlayer -> player_id);?>
	<?php if($player) :?>
		
		<div class="title">
			<?php echo $player;?>
		</div>

		<?php echo $this -> itemPhoto($player);?>

		<?php $overRallRating = $player -> rating;?>
		<div class="user_rating" title="<?php echo $overRallRating;?>">
			<?php for ($x = 1; $x <= $overRallRating; $x++): ?>
		        <span class="rating_star_generic"><i class="fa fa-star"></i></span>&nbsp;
		    <?php endfor; ?>
		    <?php if ((round($overRallRating) - $overRallRating) > 0): $x ++; ?>
		        <span class="rating_star_generic"><i class="fa fa-star-half-o"></i></span>&nbsp;
		    <?php endif; ?>
		    <?php if ($x <= 5) :?>
		        <?php for (; $x <= 5; $x++ ) : ?>
		            <span class="rating_star_generic"><i class="fa fa-star-o"></i></span>&nbsp;
		        <?php endfor; ?>
		    <?php endif; ?>
		</div>

		<?php 
			$today = new DateTime();
			$birthdate = new DateTime($player -> birth_date);
			$interval = $today->diff($birthdate);
			$player_age =  $interval->format('%y');
		?> 
		<hr>

		<div class="infomation">
			<p>
				<?php echo $submissionPlayer -> countPercentMatching()."%";?><br/>
			</p>
			<?php
				$countryName = '';
				if($player ->country_id && $country = Engine_Api::_() -> getItem('user_location', $player ->country_id))
				{
					$countryName = $country -> getTitle();
				}
			?>
			<p><?php echo $countryName;?></p>
			<p><?php echo $this -> translate("Age");?>: <span><?php echo $player_age;?></span></p>
			<p><?php echo $this -> translate("Owner");?>: <span><?php echo $player -> getOwner();?></span> </p>
			<p><?php echo $this -> translate("Note");?>: <span><?php echo $submissionPlayer -> getTitle();?></span> </p>
			<hr>
			<p><?php echo $this -> translate("Description");?>: <span><?php echo $submissionPlayer -> getDescription();?></span> </p>

		</div>

		<div class="tf_submission_btn">
			<ul class="tf_list_item_submission">
				<?php if($this -> viewer() -> isSelf($campaign -> getOwner())) :?>
				<li class="tf_submission_item">			
					<?php echo $this -> htmlLink($this -> url(array('action' => 'hide', 'campaign_id' => $campaign -> getIdentity(), 'id' => $submissionPlayer -> getIdentity()), 'tfcampaign_specific', true), $this -> translate("hide"), array('class' => 'smoothbox')) ?>
				</li>
				<?php endif;?>

				<?php if($this -> viewer() -> isSelf($submissionPlayer -> getOwner())) :?>
				<li>
					<?php echo $this -> htmlLink($this -> url(array('action' => 'withdraw', 'campaign_id' => $campaign -> getIdentity(), 'id' => $submissionPlayer -> getIdentity()), 'tfcampaign_specific', true), $this -> translate("withdraw").'&nbsp;&nbsp;&nbsp;<i class="fa fa-times"></i>', array('class' => 'smoothbox')) ?>
				</li>
				<?php endif;?>

				<?php
					Engine_Api::_()->core()->clearSubject();
					Engine_Api::_()->core()->setSubject($player -> getOwner());
					$menuUser = new User_Plugin_Menus();
					$menuMessage = new Messages_Plugin_Menus();
					$aFollowButton = $menuUser -> onMenuInitialize_UserProfileFriend();
					$aReportButton  = $menuUser -> onMenuInitialize_UserProfileReport();
					$aMessageButton = $menuMessage -> onMenuInitialize_UserProfileMessage();
				?>
				<?php if($aFollowButton && !empty($aFollowButton['params'])) :?>
				<li>
					<a class='<?php if(isset($aFollowButton['class'])) echo $aFollowButton['class']; ?>' href="<?php echo $this -> url($aFollowButton['params'], $aFollowButton['route'], array()); ?>" > 
						<?php echo $this -> translate($aFollowButton['label']) ?>
					</a>
				</li>
				<?php endif;?>

				<?php if($aReportButton) : unset($aReportButton['params']['format'])?>
				<li>
					<a class='<?php if(isset($aReportButton['class'])) echo $aReportButton['class']; ?>' href="<?php echo $this -> url($aReportButton['params'], $aReportButton['route'], array()); ?>" > 
						<?php echo $this -> translate($aReportButton['label']) ?>
					</a>
				</li>
				<?php endif;?>

				<?php if($aMessageButton) :?>
				<li>
					<a class='<?php if(isset($aMessageButton['class'])) echo $aMessageButton['class']; ?>' href="<?php echo $this -> url($aMessageButton['params'], $aMessageButton['route'], array()); ?>" > 
						<?php echo $this -> translate($aMessageButton['label']) ?>
					</a>
				</li>

				<?php endif;?>
			</ul>
		</div>
		<!-- clear subject for loop to get action button -->
		<?php Engine_Api::_()->core()->clearSubject('user');?>
	<?php endif;?>

	</li>
<?php endforeach;?>
</ul>

<?php 
	//set subject to campaign
	Engine_Api::_()->core()->clearSubject();
	Engine_Api::_()->core()->setSubject($campaign);
?>
<?php endif;?>

<script type="text/javascript">
	window.addEvent('domready', function(){
		<?php if($this -> filterType):?>
			$('filter-submission').set('value', '<?php echo $this -> filterType;?>');
		<?php endif;?>
		$('filter-submission').addEvent('change', function(){
			type_id = this.value;
			url = '<?php echo $this -> url(array('id' => $campaign -> getIdentity(), 'slug' => $campaign -> getSlug()), 'tfcampaign_profile',true);?>' + '/sort/' + type_id;
			window.location.assign(url);
		});

		setPin1();
	});


    jQuery.noConflict();
    function setPin1(){
	    (function (jQuery){
	        var handler = jQuery('#profile-submission-pin .item-profile-submission');

	        handler.wookmark({
	            // Prepare layout options.
	            autoResize: true, // This will auto-update the layout when the browser window is resized.
	            container: jQuery('#profile-submission-pin'), // Optional, used for some extra CSS styling
	            offset: 10, // Optional, the distance between grid items
	            outerOffset: 0, // Optional, the distance to the containers border
	            itemWidth: 270, // Optional, the width of a grid item
	            flexibleWidth: '50%',
	        });
	    })(jQuery);
	}

    $$('.tab_layout_tfcampaign_profile_submission').addEvent('click',function(){
        setPin1();
    })

</script>