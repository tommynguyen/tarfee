<?php
	$this->headTranslate(array(
		'ratings',
		'rating',
	));
?>
<script type="text/javascript">
     en4.core.runonce.add(function() {
          var pre_rate = <?php echo $this->event->rating; ?>;
          var rated = '<?php echo $this->rated; ?>';
          var event_id = <?php echo $this->event->event_id; ?>;
          var total_votes = <?php echo $this->rating_count; ?>;
          var viewer = <?php echo $this->viewer_id; ?>;

          var rating_over = window.rating_over = function(rating) {
               if( rated == 1 ) {
                    $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
                    //set_rating();
               } else if( viewer == 0 ) {
                    $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
               } else {
                    $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
                    for(var x=1; x<=5; x++) {
                         if(x <= rating) {
                              $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
                         } else {
                              $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
                         }
                    }
               }
          }
    
          var rating_out = window.rating_out = function() {
              	$('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
               if (pre_rate != 0){
                    set_rating();
               }
               else {
                    for(var x=1; x<=5; x++) {
                         $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
                    }
               }
          }

          var set_rating = window.set_rating = function() {
               var rating = pre_rate;
               $('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
               for(var x=1; x<=parseInt(rating); x++) {
                    $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
               }

               for(var x=parseInt(rating)+1; x<=5; x++) {
                    $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
               }

               var remainder = Math.round(rating)-rating;
               if (remainder <= 0.5 && remainder !=0){
                    var last = parseInt(rating)+1;
                    $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
               }
          }

          var rate = window.rate = function(rating) {
               $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
               for(var x=1; x<=5; x++) {
                    $('rate_'+x).set('onclick', '');
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
                         $('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total], total);
                    }
               })).send();

          }

          var tagAction = window.tagAction = function(tag){
               $('tag').value = tag;
               $('filter_form').submit();
          }
    
          set_rating();
     });
</script>
<h3>
     <?php echo $this->translate('Add Rates') ?>
</h3>
<div id="ynevent-profile-rates">
     <div id="ynevent_rating" class="rating" onmouseout="rating_out();">
          <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
          <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
          <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
          <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
          <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
          <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate'); ?></span>
     </div>

     <div id="addthis" class="addthis_toolbox addthis_default_style addthis_16x16_style">
          <a class="addthis_button_facebook_like"></a> 
          <a class="addthis_button_twitter"></a>     
          <a class="addthis_button_google_plusone"></a>
     </div>     
     <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4f338f5662744635"></script>

</div>
