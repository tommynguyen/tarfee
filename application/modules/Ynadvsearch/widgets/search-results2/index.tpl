<?php
    $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.wookmark.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/wookmark/jquery.imagesloaded.js');
?>

<style>
	.highlighted-text {
		font-weight: bold;
	}
</style>
<?php if(count($this->results) <= 0): ?>
<div class="tip">
	<span>
  		<?php echo $this->translate('No results were found.') ?>
	</span>
</div>
<?php else: ?>
<ul class = "ynadvsearch_searchresult" id="ynadvsearch_searchresults">
<?php $count = 1;?>
<?php foreach( $this->results as $row): ?>
	<?php if ($count > $this->limit) break;?>
	<?php $item = (!empty($row->type) && !empty($row->id)) ? $this->item($row->type, $row->id): $row;?>
	<?php if ($item && !Engine_Api::_()->user()->itemOfDeactiveUsers($item)): 
	if(in_array($item->getType(), array('user_playercard', 'event', 'video', 'blog', 'group', 'tfcampaign_campaign', 'user'))):?>
	<li class="result-search-item <?php echo $item->getType()?>-item">
	<?php switch ($item->getType()) :
		case 'user_playercard':
			$totalPhoto = $item -> getPhotosTotal();
			$totalVideo = $item -> getTotalVideo();?>
		<div id='profile_photo'>
			<?php $photoUrl = ($item -> getPhotoUrl('thumb.main')) ? $item->getPhotoUrl('thumb.main') : "application/modules/User/externals/images/nophoto_playercard_thumb_profile.png" ?>
			<div class="avatar">
				<div class="thumb_profile" style="background-image:url(<?php echo $photoUrl?>)">
					<div class="tarfee_sport_type_position">
						<?php if($item -> getSport()):?>
							<span title="<?php echo $item -> getSport() -> getTitle();?>"><?php echo $this -> itemPhoto($item -> getSport(), 'thumb.icon');?></span>
						<?php endif;?>
						<?php if($item -> getPosition()):?>
							<span title="<?php echo $item -> getPosition() -> getTitle();?>" class="player-position">
								<?php 
						    		preg_match_all('/[A-Z]/', $item -> getPosition() -> getTitle(), $matches);
									echo implode($matches[0]);?>
							</span>
						<?php endif;?>
					</div>
				</div>
			</div>
			<div class="tarfee_gender_player_name">
				<span class="gender_player">
					<?php if (($item->gender) == 1){
						echo '<i class="fa fa-mars"></i>';
					}else{
						echo '<i class="fa fa-venus"></i>';
					}

					?>
				</span>
				<a title="<?php echo $item -> first_name.' '.$item -> last_name;?>" href="<?php echo $item -> getHref()?>" class="player_name" ><?php echo $this -> string() -> truncate($item -> first_name.' '.$item -> last_name, 20)?></a>
			</div>

			<?php $overRallRating = $item -> rating;?>
			<div class="user_rating" title="<?php echo number_format($overRallRating, 2);?>">
				<?php for ($x = 1; $x <= $overRallRating; $x++): ?>
			        <span class="rating_star_generic"><i class="fa fa-star"></i></span>
			    <?php endfor; ?>
			    <?php if ((round($overRallRating) - $overRallRating) > 0): $x ++; ?>
			        <span class="rating_star_generic"><i class="fa fa-star-half-o"></i></span>
			    <?php endif; ?>
			    <?php if ($x <= 5) :?>
			        <?php for (; $x <= 5; $x++ ) : ?>
			            <span class="rating_star_generic"><i class="fa fa-star-o"></i></span>
			        <?php endfor; ?>
			    <?php endif; ?>
			</div>
			<?php
				$countryName = '';
				if($item ->country_id && $country = Engine_Api::_() -> getItem('user_location', $item ->country_id))
				{
					$countryName = $country -> getTitle();
				}
			?>

			<div class="tarfee_infomation_player">
				<p>
					<?php echo  $this->locale()->toDate($item -> birth_date);?> 
				</p>
				<p>
					<?php 
						if($countryName)
						echo $countryName
					?>
				</p>
				<p>
					<?php 
						$laguages = json_decode($item -> languages);
						$arr_tmp = array();
						if($laguages)
						{
							foreach ($laguages as $lang_id) 
							{
								$langTb =  Engine_Api::_() -> getDbTable('languages', 'user');
								$lang = $langTb -> fetchRow($langTb ->select()->where('language_id = ?', $lang_id));
								if($lang)
									$arr_tmp[] = $lang -> title;
							}
						}
						echo implode(' | ', $arr_tmp);
					?>
				</p>
			</div>
			<ul class="tarfee_count">
				<li>
					<?php $eyeons = $item->getEyeOns(); ?>
					<?php $url = $this->url(array('action'=>'view-eye-on', 'player_id'=>$item->getIdentity()), 'user_playercard' , true)?>
					<?php if(count($eyeons)):?>		
					<a href="<?php echo $url?>" class="smoothbox">
						<span class="tarfee-count-number"><?php echo count($eyeons); ?></span>
						<span><?php echo $this->translate('eye on');  ?></span>
					</a>
					<?php else:?>
						<span class="tarfee-count-number"><?php echo count($eyeons); ?></span>
						<span><?php echo $this->translate('eye on');  ?></span>
					<?php endif;?>
				</li>

				<li>
					<span class="tarfee-count-number"><?php  echo $totalVideo; ?></span>
					<span><?php echo $this->translate(array('video','videos', $totalVideo)); ?></span>
				</li>
				
			</ul>
			
			<div class="nickname">
				<?php echo $this->translate('By') ?>
	        	<?php echo $this->htmlLink($item -> getOwner()->getHref(), $item -> getOwner() ->getTitle()) ?>
	     	</div>

		</div>	
		<?php break;?>
		
		<?php case 'video': ?>
			<div class="ynvideo_thumb_wrapper video_thumb_wrapper">
			    <?php if ($item->parent_type == 'user_playercard') :?>
			        <span class="icon-player">
			            <img src="application/themes/ynresponsive-event/images/icon-player.png" />
			        </span>
			    <?php endif; ?>
			
			    <?php
			    if ($item->photo_id) {
			        echo $this->htmlLink($item->getPopupHref(), $this->itemPhoto($item, 'thumb.large'), array('class'=>'smoothbox'));
			    } else {
			        echo $this->htmlLink($item->getPopupHref(),'<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Ynvideo/externals/images/video.png">', array('class'=>'smoothbox'));;
			    }
			    ?>
			</div>
			<?php if ($item->parent_type == 'user_playercard') :?>
			<?php $player = $item->getParent();?>
			<?php if ($player):?>
			<div class="player-info">
			    <div class="player-photo">
			        <?php echo $this->itemPhoto($player, 'thumb.icon')?>
			    </div>
			    <div class="player_info_detail">
			        <div class="player-title">
			            <?php echo $player?>
			        </div>
			        <?php $position = $player->getPosition()?>
			        <?php if ($position) : ?>
					<div class="player-position">
			    		<?php 
			    		preg_match_all('/[A-Z]/', $position, $matches);
						echo implode($matches[0]);?>
			 		</div>
			        <?php endif;?>
			    </div>
			</div>
			<?php endif;?>
			<?php endif;?>
			<div class="video-title">
			    <?php echo $this->htmlLink($item->getPopupHref(), $item->getTitle(), array('class'=>'smoothbox'))?>
			</div>
			<div class="video-statistic-rating">
			
			    <div class="video-statistic">
			        <span><?php echo $this->translate(array('%s view','%s views', $item->view_count), $item->view_count)?></span>
			        <?php $commentCount = $item->comments()->getCommentCount(); ?>
			        <span><?php echo $this->translate(array('%s comment','%s comments', $commentCount), $commentCount)?></span>
			    </div>
			    <?php 
			        echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $item));
			    ?>
			</div>
			<div class="video_author">
			    <?php $user = $item->getOwner() ?>
			    <?php if ($user) : ?>
			        <?php echo $this->translate('by') ?>
			        <?php echo $this->htmlLink($user->getHref(), htmlspecialchars ($this->string()->truncate($user->getTitle(), 25)), array('title' => $user->getTitle())) ?>
			    <?php endif; ?>
			</div>
		<?php break;?>
		
		<?php case 'event': ?>
			<?php if($item -> type_id == 1) :?>
            <span class="icon-event-tryout">
                <img src="application/modules/Ynevent/externals/images/tryout.png" alt="">
            </span>
            <?php else: ?>
            <span class="icon-event-tryout">
                <img src="application/modules/Ynevent/externals/images/event.png" alt="">
            </span>
	        <?php endif;?>
	
	        <div class="ynevents_title">
	            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
	        </div>
	        
	        <div class="ynevent_location">
	    	<?php 
	        	$locationName = array();
				if($item ->country_id && $country = Engine_Api::_() -> getItem('user_location', $item ->country_id))
				{
					$locationName[] = $country -> getTitle();
				}
				if($item ->province_id && $province = Engine_Api::_() -> getItem('user_location', $item ->province_id))
				{
					$locationName[] = $province -> getTitle();
				}
				if($item ->city_id && $city = Engine_Api::_() -> getItem('user_location', $item ->city_id))
				{
					$locationName[] = $city -> getTitle();
				}
				if($locationName):?>
				<span><?php echo $this -> translate("Location");?>:</span>
				<p>
				<?php echo join($locationName, ',');?>
				</p>
			<?php elseif($event -> address):?>
				<span><?php echo $this -> translate("Location");?>:</span>
				<p>
					<?php echo $event -> address;?>
				</p>
			<?php endif;?>
	        </div>
			<div class="ynevents_time_place">
	            <span>
	            	<?php 
					$startDateObj = null;
					if (!is_null($item->starttime) && !empty($item->starttime)) 
					{
						$startDateObj = new Zend_Date(strtotime($item->starttime));	
					}
					if( $this->viewer() && $this->viewer()->getIdentity() ) {
						$tz = $this->viewer()->timezone;
						if (!is_null($startDateObj))
						{
							$startDateObj->setTimezone($tz);
						}
				    }
					if(!empty($startDateObj)) :?>
						<span><?php echo $this -> translate('Date') ;?>:</span>
						<p><?php echo (!is_null($startDateObj)) ?  date('d M, Y', $startDateObj -> getTimestamp()) : ''; ?></p>
						<span><?php echo $this -> translate('Time') ;?>:</span>
						<p><?php echo (!is_null($startDateObj)) ?  date('H:i', $startDateObj -> getTimestamp()) : ''; ?></p>
					<?php endif; ?>
	            </span>
	        </div>
	        <div class="ynevents_author">
		        <?php echo $this->translate('by') ?>
		        <?php
		        	$poster = $item->getOwner();
		            if ($poster) {
		                echo $this->htmlLink($poster, $poster->getTitle());
		            }
		        ?>
		    </div>
		<?php break;?>
		
		<?php case 'blog': ?>
			<div class='blogs_browse_info'>
	        	<p class='blogs_browse_info_title'>
	          	<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
	        	</p>
	        	<p class='blogs_browse_info_date'>
	          	<?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->creation_date) ?>
	        	</p>
	        	<p class='blogs_browse_info_blurb'>
	          	<?php echo $item->getDescription(); ?>
	        	</p>
	      	</div>
		<?php break;?>
		<?php case 'group': ?>
			<?php $photoUrl = ($item -> getPhotoUrl('thumb.profile')) ? $item->getPhotoUrl('thumb.profile') : "application/modules/Advgroup/externals/images/nophoto_group_thumb_profile.png" ?>
			<a href="<?php echo $item->getHref()?>">
				<div class="club-photo" style="background-image: url(<?php echo $photoUrl; ?>)">
				</div>
			</a>
			<div class="club-info-general">
				<div class="club-title">
					<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
				</div>
				<?php 
				$establishDateObj = null;
				if (!is_null($item->establish_date) && !empty($item->establish_date) && $item->establish_date) 
				{
					$establishDateObj = new Zend_Date(strtotime($item->establish_date));	
				}
				if( $this->viewer() && $this->viewer()->getIdentity() ) 
				{
					$tz = $this->viewer()->timezone;
					if (!is_null($establishDateObj))
					{
						$establishDateObj->setTimezone($tz);
					}
			    }
				?>
				<?php if(!empty($establishDateObj)) :?>
					<div class="club-establish">
						<?php echo (!is_null($establishDateObj)) ?  date('d M, Y', $establishDateObj -> getTimestamp()) : ''; ?>
					</div>
				<?php endif;?>
				<?php if ($item->getCountry()) :?>
				<div class="club-country">
					<?php echo $item->getCountry()->getTitle()?>
					<?php if ($item->getCity()) :?>
						<?php echo ", ".$item->getCity()->getTitle()?>
					<?php endif;?>
				</div>
				<?php endif;?>
				<div class="club-like-count">
					<i class="fa fa-heart"></i>
					<span class="like-count">
						<?php $rows = $item -> membership() ->getMembers();?>
						<?php $url = $this->url(array('controller' => 'index','action'=>'view-fan', 'club_id'=> $item->getIdentity()), 'group_extended' , true)?>
						<?php if(count($rows)):?>		
						<a href="<?php echo $url?>" class="smoothbox">
							<?php echo $this -> translate("Fans")." (".count($rows).")";?>
						</a>
						<?php else:?>
							<?php echo $this -> translate("Fans")." (".count($rows).")";?>
						<?php endif;?>
					</span>
				</div>
		<?php break;?>
		<?php case 'tfcampaign_campaign': ?>
			<div class="tfcampaign_sport">
			<?php 
			if($item -> getSport())
				echo $this -> itemPhoto($item -> getSport(), 'thumb.icon');
			else
				echo $this -> itemPhoto($item, 'thumb.icon');?>
			</div>
			<div class="tfcampaign_title"><?php echo $item;?></div>
			<?php if($item -> getLocation()):?>
			<div class="tfcampaign_location">
				<span><?php echo $this -> translate("Location");?>:</span>
				<p><?php echo $item -> getLocation();?></p>
			</div>
			<?php endif;?>
			<div class="tfcampaign_gender">
				<span><?php echo $this -> translate("Gender") ;?>:</span>
				<p><?php echo $item -> getGender();?></p>
			</div>
			<div class="tfcampaign_closing">
				<?php 
					$endDateObj = null;
					if (!is_null($item->end_date) && !empty($item->end_date) && $item->end_date) 
					{
						$endDateObj = new Zend_Date(strtotime($item->end_date));	
					}
					if( $this->viewer() && $this->viewer()->getIdentity() ) {
						$tz = $this->viewer()->timezone;
						if (!is_null($endDateObj))
						{
							$endDateObj->setTimezone($tz);
						}
				    }
					if(!empty($endDateObj)) :?>
						<span><?php echo $this -> translate('Closing Date') ;?>:</span>
						<p><?php echo (!is_null($endDateObj)) ?  date('d M, Y', $endDateObj -> getTimestamp()) : ''; ?></p>
				<?php endif; ?>
			</div>
			<div class="tfcampaign_author">
		        <?php echo $this->translate('by') ?>
		        <?php
		        $poster = $item->getOwner();
		            if ($poster) {
		                echo $this->htmlLink($poster, $poster->getTitle());
		            }
		        ?>
		    </div>
		<?php break;?>
		
		<?php case 'user': ?>
		<div class="ynadvsearch-result-item-photo">
        	<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      	</div>
      	<div class="ynadvsearch-result-item-info">
	    	<?php
	        if(!empty($this->text)):
	            echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), implode(' ', $this->text)), array('class' => 'search_title'));
	            else:
	            echo  $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title'));
	          ?>
	        <?php endif; ?>
        	<div class="search_description">
     		<?php 
     		if(!empty($this->text)):
	            echo $this->viewMore($this->highlightText($item->getDescription(), implode(' ', $this->text)));
	       	else:
	            echo $this->viewMore($item->getDescription());
	          ?>
	        <?php endif; ?>
        	</div>
      	</div>
      	<?php break;?>
  	<?php endswitch; ?>
	</li>
	<?php endif; ?>
	<?php endif; ?>
	<?php $count++;?>
<?php endforeach;?>
</ul>
<?php if (count($this->results) > $this->limit && !$this->reachLimit):?>
<span style="cursor:pointer" id="ynadvsearch-viewmore-btn" onclick="showMore(<?php echo ($this->limit + $this->from)?>)"><?php echo $this->translate('View more result') ?></span>
<div id="ynadvsearch-loading" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 5px;' />
</div>
<script type="text/javascript">
function showMore(from){
    var url = '<?php echo $this->url(array('module' => 'core','controller' => 'widget','action' => 'index','name' => 'ynadvsearch.search-results2'), 'default', true) ?>';
    $('ynadvsearch-viewmore-btn').destroy();
    $('ynadvsearch-loading').style.display = '';
    var params = <?php echo json_encode($this->params)?>;
    params.format = 'html';
    params.from = from;
    var request = new Request.HTML({
      	url : url,
      	data : params,
      	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        	$('ynadvsearch-loading').destroy();
            var result = Elements.from(responseHTML);
            var results = result.getElement('#ynadvsearch_searchresults').getChildren();
            $('ynadvsearch_searchresults').adopt(results);
            var viewMore = result.getElement('#ynadvsearch-viewmore-btn');
            if (viewMore[0]) viewMore.inject($('ynadvsearch_searchresults'), 'after');
            var loading = result.getElement('#ynadvsearch-loading');
            if (loading[0]) loading.inject($('ynadvsearch_searchresults'), 'after');
            eval(responseJavaScript);
        }
    });
   request.send();
  }

</script>
<?php endif;?>	
<?php endif; ?>


<script type="text/javascript">
    jQuery.noConflict();
    (function (jQuery){
        var handler = jQuery('.ynadvsearch_searchresult .result-search-item');
        handler.wookmark({
            // Prepare layout options.
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: jQuery('.ynadvsearch_searchresult'), // Optional, used for some extra CSS styling
            offset: 15, // Optional, the distance between grid items
            outerOffset: 0, // Optional, the distance to the containers border
            itemWidth: 245, // Optional, the width of a grid item
            flexibleWidth: '100%',
        });
    })(jQuery);    
</script>