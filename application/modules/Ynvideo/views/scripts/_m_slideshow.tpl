<div class="callbacks_container">
<ul class="rslides" id="ymb_home_featuredvideo">
	
	<?php 
		 foreach ($this->videos as $video_item) 
		{ 
		 $poster = $video_item->getOwner();
		 
	?>
	
	      <li>
	      	<?php 
	            echo $this->htmlLink($video_item->getHref(), $this->itemPhoto($video_item, 'thumb.large', '', 
	                array('width' => $this->videoWidth . 'px', 'height' => $this->videoHeight . 'px'))) 
	        ?> 
	      	<div class="caption ynvideo_featured_frame">
		        <div class="ynvideo_feature_user_thumb">
		            <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon')) ?>    
		        </div>
		        <div class="ynvideo_featured_info">
		            <div class="ynvideo_featured_title">
		                <?php echo $this->htmlLink($video_item->getHref(), $this->string()->truncate($video_item->title, 15), array('title' => $this->string()->stripTags($this->video->title))) ?>
		            </div>
		            <div class="ynvideo_feature_info">
		                <?php echo $this->translate('Posted by ')?>
		                <?php echo $this->htmlLink($poster->getHref(), htmlspecialchars ($this->string()->truncate($poster->getTitle(), 15)), array('title' => $poster->getTitle()))?>
		                <?php echo $this->translate(' on ')?>
		                <?php echo $this->locale()->toDateTime(strtotime($video_item->creation_date), array('type' => 'date')) ?>
		            </div>
		            <div class="ynvideo_feature_description" title="<?php echo $this->string()->stripTags($video_item->description)?>">
		                <?php echo $this->string()->truncate($this->string()->stripTags($video_item->description), 20)?>
		            </div>
		            <div class="ynvideo_feature_info">
		                <?php 
		                    echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $video_item));
		                ?>
		                <div class="ynvideo_views">
		                    <?php 
		                        echo $this->translate(array('(%1$s view)', '(%1$s views)', $video_item->view_count), 
		                            $this->locale()->toNumber($video_item->view_count));
		                    ?>
		                </div>
		                <div class="ynvideo_clear"></div>
		            </div>
		        </div>
		        <div class="ynvideo_clear"></div>
		    </div>
	      </li>
    <?php } ?>
    
</ul>
</div>

