<?php
	$tableRating = Engine_Api::_() -> getDbTable('reviewRatings','ynvideo');
	$viewer  = Engine_Api::_() -> user() -> getViewer();
?>
	
<?php foreach($this -> ratingTypes as $item) :?>
    <div class="ynvideo_rating_player">
    	<?php echo $this -> translate($item -> title);?>	
    	<div id="video_rating_<?php echo $item -> getIdentity();?>" class="rating" onmouseout="set_rating(<?php echo $item -> getIdentity();?>);">
            <span id="rate_1_<?php echo $item -> getIdentity();?>" class="fa fa-star" onclick="rate(1, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(1, <?php echo $item -> getIdentity();?>);">
            </span>

            <span id="rate_2_<?php echo $item -> getIdentity();?>" class=" fa fa-star  fa fa-star" onclick="rate(2, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(2, <?php echo $item -> getIdentity();?>);">
            </span>

            <span id="rate_3_<?php echo $item -> getIdentity();?>" class=" fa fa-star  fa fa-star" onclick="rate(3, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(3, <?php echo $item -> getIdentity();?>);">
            </span>

            <span id="rate_4_<?php echo $item -> getIdentity();?>" class=" fa fa-star  fa fa-star" onclick="rate(4, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(4, <?php echo $item -> getIdentity();?>);">
            </span>

            <span id="rate_5_<?php echo $item -> getIdentity();?>" class=" fa fa-star  fa fa-star" onclick="rate(5, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(5, <?php echo $item -> getIdentity();?>);">
            </span>
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

    var rate = window.rate = function(rating,id) {
        if (!rated) {
            rated = 1;
        }
        
        (new Request.JSON({
                'format': 'json',
                'url' : '<?php echo $this->url(array('action' => 'rating'), 'video_general', true) ?>',
                'data' : {
                    'format' : 'json',
                    'rating_type' : id,
                    'rating' : rating,
                    'video_id': '<?php echo $this -> video_id;?>',
                },
                'onSuccess' : function(responseJSON, responseText)
                {
                	is_click = 1;
					new_rate = responseJSON[0].rating;
					set_rating(responseJSON[0].rating_type);
					//reset
			   		new_rate = 0;
			   		is_click =0;
                }
        })).send();
        
    }
    
    var rating_over = window.rating_over = function(rating,id) {
        for(var x=1; x<=5; x++) {
            if(x <= rating) {
                $('rate_'+x+'_'+id).set('class', 'fa fa-star');
            } else {
                $('rate_'+x+'_'+id).set('class', 'fa fa-star-o');
            }
        }
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
