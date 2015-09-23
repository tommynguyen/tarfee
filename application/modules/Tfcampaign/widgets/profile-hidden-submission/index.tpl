<?php
    $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.wookmark.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.imagesloaded.js');
?>
<?php $campaign = $this -> campaign;?>


<!-- submissionPlayers -->   

<?php if(count($this -> submissionPlayers)) :?>



<ul class="profile-submission" id="profile-hidden-submission">
<?php foreach($this -> submissionPlayers as $submissionPlayer) :?>
	<li class="item-profile-submission">
		<?php $player = Engine_Api::_() -> getItem('user_playercard', $submissionPlayer -> player_id);?>

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
		<p><?php echo $submissionPlayer -> countPercentMatching()."%";?></p>
			<p><?php echo $this -> translate("Age");?>: <?php echo $player_age;?></p>
			<p><?php echo $this -> translate("Owner");?>: <?php echo $player -> getOwner();?></p>
			<p><?php echo $this -> translate("Note");?>: <?php echo $submissionPlayer -> getTitle();?></p>
			<hr>
			<p><?php echo $this -> translate("Description");?>: <?php echo $submissionPlayer -> getDescription();?></p>
			<?php $reason = $submissionPlayer -> getReason();?>
			<?php if($reason) :?>
			<p>
				<?php echo $this -> translate('Reason');?>: <?php echo $this -> translate($reason -> title);?>
			</p>
			<?php endif;?>	
		</div>

		<div class="tf_submission_btn">
			<ul class="tf_list_item_submission">
			<?php if($this -> viewer() -> isSelf($campaign -> getOwner())) :?>
				<li>
			<?php echo $this -> htmlLink($this -> url(array('action' => 'unhide', 'campaign_id' => $campaign -> getIdentity(), 'id' => $submissionPlayer -> getIdentity()), 'tfcampaign_specific', true), $this -> translate("unhide"), array('class' => 'smoothbox')) ?>
			</li><?php endif;?>
			</ul>
		</div>
		

	</li>
<?php endforeach;?>
</ul>
<?php endif;?>

<script>
	window.addEvent('domready', function(){
		setPin();
	});

    jQuery.noConflict();
     function setPin(){
	    (function (jQuery){
	        var handler = jQuery('#profile-hidden-submission .item-profile-submission');

	        handler.wookmark({
	            // Prepare layout options.
	            autoResize: true, // This will auto-update the layout when the browser window is resized.
	            container: jQuery('#profile-hidden-submission'), // Optional, used for some extra CSS styling
	            offset: 10, // Optional, the distance between grid items
	            outerOffset: 0, // Optional, the distance to the containers border
	            itemWidth: 270, // Optional, the width of a grid item
	            flexibleWidth: '50%',
	        });
	    })(jQuery);
	}
    $$('.tab_layout_tfcampaign_profile_hidden_submission').addEvent('click',function(){
        setPin();
    })
</script>