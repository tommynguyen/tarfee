<?php
	$tableRating = Engine_Api::_() -> getDbTable('reviewRatings','ynvideo');
?>
	
<?php foreach($this -> ratingTypes as $item) :?>
  <div class="ynvideo_rating_player">
	<?php echo $this -> translate($item -> title);?>	
	<div id="video_rating_<?php echo $item -> getIdentity();?>" class="rating">
        <span id="rate_1_<?php echo $item -> getIdentity();?>" class="fa fa-star"></span>
        <span id="rate_2_<?php echo $item -> getIdentity();?>" class="fa fa-star "></span>
        <span id="rate_3_<?php echo $item -> getIdentity();?>" class="fa fa-star "></span>
        <span id="rate_4_<?php echo $item -> getIdentity();?>" class="fa fa-star "></span>
        <span id="rate_5_<?php echo $item -> getIdentity();?>" class="fa fa-star "></span>
    </div>
    <input type="hidden" id="review_rating_<?php echo $item -> getIdentity();?>" name="review_rating_<?php echo $item -> getIdentity();?>" />
  </div>
<?php endforeach;?>
<br />

<script type="application/javascript">
    
    var rated = 0;
    var new_rate = 0;
    var is_click = 0;
    
    var set_rating = window.set_rating = function(id) {
    	if(is_click)
    	{
       	 var rating = new_rate;
        }
        else
        {
          if($('review_rating_'+id).get('value'))
         	var rating = $('review_rating_'+id).get('value');
          else
          	var rating = 0;
        }
        var indexStar = 1;
        if(rating != 0) {
	        for(var x=1; x<=parseInt(rating); x++) {
	            $('rate_'+x+'_'+id).set('class', 'fa fa-star');
	            indexStar = x;
	        }
	        
	    	if((Math.round(rating)-rating)>0) {
	    		var nextIndex = parseInt(indexStar)+1;
	    		$('rate_'+nextIndex+'_'+id).set('class', 'fa fa-star-half-o');
	    		indexStar = nextIndex;
	    	}
	        
	        for(var x=parseInt(indexStar)+1; x<=5; x++) {
	            $('rate_'+x+'_'+id).set('class', 'fa fa-star-o');
	        }
        } else {
        	 for(var x=1; x<=5; x++) {
	            $('rate_'+x+'_'+id).set('class', 'fa fa-star-o');
	        }
        }
        
        $('review_rating_'+id).set('value', rating);
        is_click = 0;
    }

    <?php foreach($this -> ratingTypes as $item) :?>
		is_click = 1;
		<?php $overrallValue = $tableRating -> getRatingOfType($item -> getIdentity(), $this -> video_id);?>
		<?php if(empty($overrallValue)) :?>
			<?php $overrallValue = 0;?>
		<?php endif;?>
		new_rate = <?php echo $overrallValue;?>;
		set_rating(<?php echo $item -> getIdentity()?>);
		//reset
   		new_rate = 0;
   		is_click =0;
    <?php endforeach;?>
    
</script>
