<?php if( ($this->video->type == 3) && $this->video_location):
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
  ?>
  <script type='text/javascript'>
    en4.core.runonce.add(function() {
      flashembed("video_embed", {
        src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/flowplayer-3.1.5.swf",
        width: 480,
        height: 386,
        wmode: 'transparent'
      }, {
        config: {
          clip: {
            url: "<?php echo $this->video_location;?>",
            autoPlay: false,
            duration: "<?php echo $this->video->duration ?>",
            autoBuffering: true
          },
          plugins: {
            controls: {
              background: '#000000',
              bufferColor: '#333333',
              progressColor: '#444444',
              buttonColor: '#444444',
              buttonOverColor: '#666666'
            }
          },
          canvas: {
            backgroundColor:'#000000'
          }
        }
      });
    });
    
  </script>
<?php endif ?>
<?php
if (!$this->video || $this->video->status != 1):?>
	<div class = 'tip'>
		<span>
   			<?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.'); ?>
   		</span>
  </div>
   <?php return; // Do no render the rest of the script in this mode
endif;
?>
<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = <?php echo $this->video->rating; ?>;
        var rated = '<?php echo $this->rated; ?>';
        var video_id = <?php echo $this->video->video_id; ?>;
        var total_votes = <?php echo $this->rating_count; ?>;
        var viewer = <?php echo $this->viewer_id; ?>;
		<?php if($this -> video -> parent_type != "user_playercard") :?>
			var rating_over = window.rating_over = function(rating) {
	            if( rated == 1 ) {
	                $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
	            } else if( viewer == 0 ) {
	                $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
	            } else {
	                $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
	                for(var x=1; x<=5; x++) {
	                    if(x <= rating) {
	                        $('rate_'+x).set('class', 'fa fa-star');
	                    } else {
	                        $('rate_'+x).set('class', 'fa fa-star-o');
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
	                for(var x=1; x<=5; x++) 
	                {
	                    $('rate_'+x).set('class', 'fa fa-star-o');
	                }
	            }
	        }
	
	        var set_rating = window.set_rating = function() {
	            var rating = pre_rate;
	            $('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
	            for(var x=1; x<=parseInt(rating); x++) {
	                $('rate_'+x).set('class', 'fa fa-star');
	            }
	
	            for(var x=parseInt(rating)+1; x<=5; x++) {
	                $('rate_'+x).set('class', 'fa fa-star-o');
	            }
	
	            var remainder = Math.round(rating)-rating;
	            if (remainder <= 0.5 && remainder !=0){
	                var last = parseInt(rating)+1;
	                $('rate_'+last).set('class', 'fa fa-star-half-o');
	            }
	        }
	
	        var rate = window.rate = function(rating) {
	            $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
	            for(var x=1; x<=5; x++) {
	                $('rate_'+x).set('onclick', '');
	            }
	            (new Request.JSON({
	                'format': 'json',
	                'url' : '<?php echo $this->url(array('action' => 'rate'), 'video_general', true) ?>',
	                'data' : {
	                    'format' : 'json',
	                    'rating' : rating,
	                    'video_id': video_id
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
	                	total_votes = responseJSON[0].total;
	                	$('rating_text').innerHTML = en4.core.language.translate(['%s rating', '%s ratings', total_votes], total_votes);
	                }
	            })).send();
	
	        }
	        
		<?php endif;?>
        set_rating();
    });
</script>

<div style="width: 1170px;" class="show_on_page">
	<div class="ynvideo_popup_close"><i class="fa fa-times"></i></div>
	<div class="ynvideo_video_view_headline">
        <div class="ynvideo_author">
            <?php echo $this->translate('Posted by') ?>

            <?php
            $poster = $this->video->getOwner();
	            if ($poster) {
	                echo $this->htmlLink($poster, $poster->getTitle(), array('target' => '_parent'));
	            }
            ?>
        </div>

	    <?php if($this -> viewer() -> getIdentity()):?>
	    	<?php $url = $this->url(array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $this -> video ->getGuid()),'default', true);?>
			<div class="yn_video_popup_btn"><a class="smoothbox" href="<?php echo $url?>"><?php echo $this -> translate("Report"); ?></a></div>
			<!-- Add addthis share-->
        	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-558fa99deeb4735f" async="async"></script>
			<div style="float: right" class="addthis_sharing_toolbox" data-url="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->video -> getHref()?>" data-title="<?php echo $this->video -> title?>"></div>
		<?php endif; ?>
	</div>
	<div class="video_view video_view_container">
		<div class="ynvideo_popup_left"> 

		    <?php if ($this->video->type == Ynvideo_Plugin_Factory::getUploadedType() || $this->video->type == Ynvideo_Plugin_Factory::getVideoURLType()): 
		    	if($this-> video_location1 || $this->video->type == Ynvideo_Plugin_Factory::getVideoURLType()):
					if($this->video->type == Ynvideo_Plugin_Factory::getVideoURLType())
					{
						$this-> video_location1 = $this-> video_location;
					}
		    	?> 
		      	<span class="view_html5_player">
		      		<img class = "thumb_video" src ="<?php echo $this-> video -> getPhotoUrl("thumb.large");?>"/>
			      	<video id="my_video" class="video-js vjs-default-skin" controls
						 preload="auto"  poster="<?php echo $this-> video -> getPhotoUrl("thumb.large");?>"
						 data-setup="{}">
			        	<source src="<?php echo $this-> video_location1;?>" type='video/mp4'>
					</video> 
				</span>	
		    <?php 
				else:?>
					<div id="video_embed" class="video_embed"> </div>
				<?php		
					endif;
		    else: ?>
		        <div class="video_embed">
		            <?php
		           	 	echo $this->videoEmbedded;
		            ?>
		        </div>
		    <?php endif; ?>

		    <div class="ynvideo_video_view_description ynvideo_video_show_less" style="height: auto;" id="ynvideo_video">
		    	<div class="yn_video_popup_info">
		    		<div class="yn_video_info_left">
				        <div class="ynvideo_video_view_title">
				            <?php echo htmlspecialchars($this->video->getTitle()) ?>
				        </div>

			        	<?php if($this->video->description):?>
			        		<div class="ynvideo_video_view_desc">
			            		<p><?php echo $this->video->description; ?></p>
			        		</div>
			            <?php endif;?>
			             <div class="video-statistic">
					        <span><?php echo $this->translate(array('%s view','%s views', $this->video->view_count), $this->video->view_count)?></span>
					        <?php $commentCount = $this->video->comments()->getCommentCount(); ?>
					        <span><?php echo $this->translate(array('%s comment','%s comments', $commentCount), $commentCount)?></span>
					        <span><?php echo $this->translate(array('%s favorite', '%s favorites', $this->video->favorite_count), $this->locale()->toNumber($this->video->favorite_count)) ?></span>
					   		<?php 
					        $totalLike = Engine_Api::_() -> getDbtable('likes', 'yncomment') -> likes($this->video) -> getLikeCount();
					        $totalDislike = Engine_Api::_() -> getDbtable('dislikes', 'yncomment') -> getDislikeCount($this->video);?>
					        <span><?php echo $this->translate(array('%s like', '%s likes', $totalLike), $totalLike) ?></span>
					        <span><?php echo $this->translate(array('%s dislike', '%s dislikes', $totalDislike), $totalDislike) ?></span>
					    </div>
		    		</div>
		            <div class="button-action-video">
					    <?php if($this -> viewer() -> getIdentity()):?>
					    <div id="popup_favorite_<?php echo $this->video -> getIdentity()?>">
					        <?php if($this->video -> hasFavorite()):?>
					            <a href="javascript:;" title="<?php echo $this->translate('Unfavorite')?>" style="background:#ff6633;color: #fff" onclick="unfavorite_video(<?php echo $this->video -> getIdentity()?>)">
					                <i class="fa fa-heart"></i>
					            </a>
					        <?php else:?>   
					            <a href="javascript:;" title="<?php echo $this->translate('Favorite')?>" onclick="favorite_video(<?php echo $this->video -> getIdentity()?>)">
					                <i class="fa fa-heart-o"></i>
					            </a>
					        <?php endif;?>  
					    </div>
					
					    <div id="popup_like_unsure_dislike_<?php echo $this -> video -> getIdentity()?>">
					        <?php echo $this -> action('list-likes', 'video', 'ynvideo', array( 'id' => $this -> video -> getIdentity()));?>
					    </div>
					    <?php endif;?>
					</div>
		            <?php if($this -> video -> parent_type != "user_playercard") :?>
		            	<!--
		             <div id="video_rating" class="rating ynvideo_rating" onmouseout="rating_out();">
		                <span id="rate_1" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
		                <span id="rate_2" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
		                <span id="rate_3" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
		                <span id="rate_4" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
		                <span id="rate_5" class="fa fa-star" <?php if (!$this->rated && $this->viewer_id): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
		                <span id="rating_text" class="rating_text ynvideo_rating_text"><?php echo $this->translate('click to rate'); ?></span>
		            </div>
		            -->
		            <?php else :?>

		            	<div class="ynvideo_rating">
			            <!-- if viewer type professional or club -> can rate -->
			            <?php if($this -> viewer() -> getIdentity() 
			            		&& $this -> video -> canAddRatings()
			            		&& $this -> video -> parent_type == "user_playercard") :?>
							
							<?php echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video)); ?>

				            <?php 
				    			$tableRatingType = Engine_Api::_() -> getItemTable('ynvideo_ratingtype');
								$rating_types = $tableRatingType -> getAllRatingTypes();
				            	echo $this->partial('_rate_video.tpl', 'ynvideo', array(
								        'ratingTypes' => $rating_types,
								        'video_id' => $this->video->getIdentity(),
							        )); 
							?>
						<?php endif ?>
						<!-- if player video -->
						<?php if( $this -> video -> parent_type == "user_playercard"):?>

							<!-- view ratings for user not in professional and club-->
							<?php if($this -> viewer() -> getIdentity() && !$this -> video -> canAddRatings()) :?>
								<?php echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video)); ?>
							<?php 
				    			$tableRatingType = Engine_Api::_() -> getItemTable('ynvideo_ratingtype');
								$rating_types = $tableRatingType -> getAllRatingTypes();
				            	echo $this->partial('_view_rate_video.tpl', 'ynvideo', array(
								        'ratingTypes' => $rating_types,
								        'video_id' => $this->video->getIdentity(),
							        )); 
							?>
							<?php endif;?>
							<!-- view ratings for guest-->
							<?php if(!$this -> viewer() -> getIdentity()):?>
								<?php echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $this->video)); ?>

								<?php 
					    			$tableRatingType = Engine_Api::_() -> getItemTable('ynvideo_ratingtype');
									$rating_types = $tableRatingType -> getAllRatingTypes();
					            	echo $this->partial('_view_rate_video.tpl', 'ynvideo', array(
									        'ratingTypes' => $rating_types,
									        'video_id' => $this->video->getIdentity(),
								        )); 
								?>
							<?php endif;?>
						<?php endif;?> 

						</div>
					<?php endif;?>

			    </div>
				    <?php if ($this->video->parent_type == 'user_playercard') :?>
					<?php $player = $this->video->getParent();?>
						<?php if ($player):?>
							<div class="player-info">
							    <div class="player-photo">
							        <?php echo $this->itemPhoto($player, 'thumb.icon')?>
							    </div>
							    <div class="player_info_detail">
							        <div class="player-title">
							          	<a target="_parent" href="<?php echo $player -> getHref()?>"><?php echo $player -> getTitle()?></a>
							        </div>
							        <?php $position = $player->getPosition()?>
							        <?php if ($position) : ?>
							            <div class="player-position">
								        	<?php 
									    		preg_match_all('/[A-Z]/', $position, $matches);
												echo implode($matches[0]);?>
										</div>
							        <?php endif;?>
							        <?php if($this -> viewer() -> getIdentity() && !$player -> isOwner($this -> viewer())):?>
								    	<span title="<?php echo $this -> translate("Keep Eye on this player card")?>" id="user_eyeon_<?php echo $player -> getIdentity()?>">
								    		<?php if($player->isEyeOn()): ?>              
								        	<a class="actions_generic eye-on eye_on" href="javascript:void(0);" onclick="pop_removeEyeOn('<?php echo $player->getIdentity() ?>')">
								        		<i class="fa fa-eye-slash"></i>
								    		</a>
								    		<?php else: ?>
								        	<a class="actions_generic eye_on" href="javascript:void(0);" onclick="pop_addEyeOn('<?php echo $player->getIdentity() ?>')">
								    			<i class="fa fa-eye"></i>
								        	</a>
								    		<?php endif; ?>
										</span>
									<?php endif;?>
							    </div>
							</div>
						<?php endif;?>
					<?php endif;?>
		        
		    </div>
	        <?php 
		        $json = '{"taggingContent":["friends"],"showComposerOptions":["addLink","addSmilies","addPhoto"],"showAsNested":"1","showAsLike":"0","showDislikeUsers":"1","showLikeWithoutIcon":"0","showLikeWithoutIconInReplies":"0","commentsorder":"1","loaded_by_ajax":"0","name":"yncomment.comments","nomobile":"0","notablet":"0","nofullsite":"0"}';
				echo $this->content()->renderWidget('yncomment.comments', (array)json_decode($json));
			?>
	    </div>
	    
	    <div class="ynvideo_popup_right">
	    	<div class="related_videos">
	    	<?php echo $this->content()->renderWidget('ynvideo.show-same-categories'); ?>
	    	</div>
	    </div>
	</div>
</div>

<script type="text/javascript">
	$$('.ynvideo_popup_close').addEvent('click',function(){parent.Smoothbox.close()});	
	 /*   jQuery.noConflict();
    jQuery('.tf_video_rating').click(function() {
        jQuery('.ynvideo_rating_player').slideToggle(400);
    });*/
</script>

<script type="text/javascript">
function pop_addEyeOn(itemId) 
{
    $('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'add-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic eye-on eye_on" href="javascript:void(0);" onclick="pop_removeEyeOn('+itemId+')"><span><i class="fa fa-eye-slash"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}

function pop_removeEyeOn(itemId){
	$('user_eyeon_'+itemId).set('html', '<a class="actions_generic" href="javascript:void(0);"><span><i class="fa fa fa-spinner fa-pulse"></i></span></a>');
    new Request.JSON({
        'url': '<?php echo $this->url(array('action'=>'remove-eye-on'),'user_playercard', true)?>',
        'method': 'post',
        'data' : {
            'id' : itemId
        },
        'onSuccess': function(responseJSON, responseText) {
            if (responseJSON.status == true) {
                html = '<a class="actions_generic eye_on" href="javascript:void(0);" onclick="pop_addEyeOn('+itemId+')"><span><i class="fa fa-eye"></i></span></a>';
                $('user_eyeon_'+itemId).set('html', html);
            }
            else {
                alert(responseJSON.message);
            }            
        }
    }).send();
}
   var unfavorite_video = function(videoId)
   {
   	   var obj = document.getElementById('popup_favorite_' + videoId);
   	   obj.innerHTML = '<a href="javascript:;" style="background:#ff6633; color: #fff"><img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" /></a>';
   	   var url = '<?php echo $this -> url(array('action' => 'remove-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
                obj.innerHTML = '<a href="javascript:;" title="<?php echo $this->translate("Favourite")?>" onclick="favorite_video('+videoId+')">' + '<i class="fa fa-heart-o"></i>' + '</a>';
            }
        });
        request.send();  
   } 
   var favorite_video = function(videoId)
   {
   	   var obj = document.getElementById('popup_favorite_' + videoId);
   	   obj.innerHTML = '<a href="javascript:;"><img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" /></a>';
   	   var url = '<?php echo $this -> url(array('action' => 'add-favorite'), 'video_favorite', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'video_id' : videoId
            },
            'onComplete':function(responseObject)
            {  
                obj.innerHTML = '<a href="javascript:;" style="background:#ff6633;color: #fff" title="<?php echo $this->translate("Unfavourite")?>" onclick="unfavorite_video('+videoId+')">' + '<i class="fa fa-heart"></i>' + '</a>';
            }
        });
        request.send();  
   }
   
   var tempLike = 0;
   var video_like = function(id, action)
   {
   		if (tempLike == 0) 
   		{
   			tempLike = 1;
   			if ($(action + '_video_' + id)) {
				$(action + '_video_' + id).innerHTML = '<img width="16" src="application/modules/Yncomment/externals/images/loading.gif" alt="Loading" />';
			}
			var url = en4.core.baseUrl + 'ynvideo/video/' + action;
   			en4.core.request.send(new Request.JSON({
				url : url,
				data : {
					format : 'json',
					id : id
				},
				onComplete : function(e) {
					tempLike = 0;
				}
			}), {
				'element' : $('popup_like_unsure_dislike_' + id)
			});
		}
   }
</script>
