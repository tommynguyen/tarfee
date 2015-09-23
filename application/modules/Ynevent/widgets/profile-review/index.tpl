<?php
	$this->headTranslate(array(
		'ratings',
		'rating',
	));
?>

<?php if ($this->viewer()->getIdentity() && $this->event->membership()->isMember($this->viewer())) : //ONLY EVENT MEMBERs CAN SUBMIT REVIEW?> 
	<?php if (!$this->isPostedReview): //HAVE NOT POSTED REVIEW YET?>
		<div id="ynevent-profile-rates">
		     <div id="ynevent_rating" class="rating" onmouseout="rating_out();">
		          <span id="review_rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="review_rate(1);"<?php endif; ?> onmouseover="review_rating_over(1);" onmouseout="review_rating_out(1);"></span>
		          <span id="review_rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="review_rate(2);"<?php endif; ?> onmouseover="review_rating_over(2);" onmouseout="review_rating_out(1);"></span>
		          <span id="review_rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="review_rate(3);"<?php endif; ?> onmouseover="review_rating_over(3);" onmouseout="review_rating_out(1);"></span>
		          <span id="review_rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="review_rate(4);"<?php endif; ?> onmouseover="review_rating_over(4);" onmouseout="review_rating_out(1);"></span>
		          <span id="review_rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="review_rate(5);"<?php endif; ?> onmouseover="review_rating_over(5);" onmouseout="review_rating_out(1);"></span>
		          <span id="review_rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span>
		     </div>
		</div>
		
		<script type="text/javascript">
			var pre_rate = <?php echo $this->event->rating; ?>;
		    var rated = '<?php echo $this->rated; ?>';
		    var event_id = <?php echo $this->event->event_id; ?>;
		    var total_votes = <?php echo $this->rating_count; ?>;
		    var viewer = <?php echo $this->viewer_id; ?>;
		    
		    var review_rating_over = window.rating_over = function(rating) {
		         if( rated == 1 ) {
		              $('review_rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
		              //set_rating();
		         } else if( viewer == 0 ) {
		              $('review_rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
		         } else {
		              $('review_rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
		              for(var x=1; x<=5; x++) {
		                   if(x <= rating) {
		                        $('review_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
		                   } else {
		                        $('review_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
		                   }
		              }
		         }
		    }
		
		    var review_rating_out = window.rating_out = function() {
		        	$('review_rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
		         if (pre_rate != 0){
		              set_rating();
		         }
		         else {
		              for(var x=1; x <= 5; x++) {
		                   $('review_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
		              }
		         }
		    }
		
		    var review_set_rating = window.set_rating = function() {
		         var rating = pre_rate;
		         $('review_rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
		         for(var x=1; x <= parseInt(rating); x++) {
		              $('review_rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
		         }
		
		         for(var x=parseInt(rating)+1; x <= 5; x++) {
		              $('review_rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
		         }
		
		         var remainder = Math.round(rating)-rating;
		         if (remainder <= 0.5 && remainder !=0){
		              var last = parseInt(rating)+1;
		              $('review_rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
		         }
		    }
		
		    var review_rate = window.rate = function(rating) {
		         $('review_rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
		         for(var x=1; x<=5; x++) {
		              $('review_rate_'+x).set('onclick', '');
		         }
		         (new Request.JSON({
		              'format': 'json',
		              'url' : '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
		              'data' : {
		                   'format' : 'json',
		                   'rating' : rating,
		                   'event_id': event_id
		              },
		              'onRequest' : function(){
		                   rated = 1;
		                   total_votes = total_votes+1;
		                   pre_rate = (pre_rate+rating)/total_votes;
		                   set_rating();
		              },
		              'onSuccess' : function(responseJSON, responseText)
		              {
		                   var total = responseJSON[0].total;
		                   $('review_rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total], total);
		              }
		         })).send();
		
		    }
		
		    var checkReviewBody = function(rating) {
		        var el = $$("form#ynevent_review_create #body");
		        if (el.length > 0)
		        {
		            el = el[0];
		            if (el.value == '')
		            {
		                alert(en4.core.language.translate("Review body can not be empty!"));
		                return false;
		            }
		            return true;
				}
				return false;
		    }
		    
		    review_set_rating();
		</script>
		<?php echo $this->form->render($this); ?>
	
	<?php else: // POSTED?>
	
	<div class="ynevent_widget_review_my_review">
		<?php if($this->myReview->rating): ?>
		<div class="ynevent_widget_review_my_rating">
			<?php $tempVal = $this->myReview->rating; ?>
			<?php for ($x = 1; $x <= 5; $x++ ): ?>
				<?php if ($x <= $tempVal):?>
					<span class="rating_star_big_generic rating_star_big"></span>
				<?php endif;?>
				<?php if ( ($x+1 > $tempVal) && ($x < $tempVal) ) :?>
					<span class="rating_star_big_generic rating_star_big_half"></span>
				<?php endif;?>
				<?php if ($x > $tempVal) :?>
					<span class="rating_star_big_generic rating_star_big_disabled"></span>
				<?php endif;?>
			<?php endfor;?>
		</div>
		<?php endif;?>
		<div>
			<h4><?php echo $this->translate("Your Review");?></h4>
		</div>
		<div>
			<?php echo $this->myReview->body;?>
		</div>
		<?php if ($this->reportCount && $this->reportCount[$this->myReview->getIdentity()] >= $this->maxReport): ?>
		<div style="margin-top: 8px;">
			<span class="ynevent_widget_review_user_flag"><?php echo $this->translate("Your review has been flagged and hidden.");?></span>
		</div>
		<?php endif;?>
	</div>	
	<?php endif; //END CHECKING POSTED REVIEW?>
<?php endif; //END CHECKING USER?>
<?php foreach($this->reviews as $review): ?>
	<?php 
	if ( $review->user_id == $this->viewer()->getIdentity() 
		|| (intval($this->reportCount[$review->getIdentity()]) >= $this->maxReport) ) 
		{continue;} 
	?>
	<div class="ynevent_widget_review_user_reviews">
		<?php if( $this->viewer() && $this->viewer()->getIdentity() ) :?>
			<?php if (!($review->isUserReported($this->viewer()))) :?>
				<div>
					<?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'review', 'action' => 'report', 'review_id' => $review->getIdentity(), 'tab' => $this->identity), $this->translate("Report"), array('class' => 'buttonlink ynevent_widget_review_user_report smoothbox')); ?>
				</div>
			<?php endif;?>
		<?php endif;?>
		<div class="ynevent_widget_review_user_info">
			<?php
				$reviewDateObject = new Zend_Date(strtotime($review->creation_date));
				if( $this->viewer() && $this->viewer()->getIdentity() ) {
					$tz = $this->viewer()->timezone;
					$reviewDateObject->setTimezone($tz);
				}
			?>
			<div class="user_photo">
				<?php $user = Engine_Api::_()->user()->getUser($review->user_id)?>
				<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'ynevent_members_icon')); ?>
			</div>
			<div class="user_info">
				<strong><?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array()); ?></strong>
				<div>
					<?php echo $this->locale()->toDate($reviewDateObject);?>
				</div>
				<?php if($review->rating): ?>
				<div>
					<?php $tempVal = $review->rating; ?>
					<?php for ($x = 1; $x <= 5; $x++ ): ?>
						<?php if ($x <= $tempVal):?>
							<span class="rating_star_big_generic rating_star_big"></span>
						<?php endif;?>
						<?php if ( ($x+1 > $tempVal) && ($x < $tempVal) ) :?>
							<span class="rating_star_big_generic rating_star_big_half"></span>
						<?php endif;?>
						<?php if ($x > $tempVal) :?>
							<span class="rating_star_big_generic rating_star_big_disabled"></span>
						<?php endif;?>
					<?php endfor;?>
				</div>
				<?php endif;?>
			</div>
		</div>
		<div>
			<?php echo $review->body;?>
		</div>
	</div>
<?php endforeach;?>