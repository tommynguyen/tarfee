<?php $index = 0; ?>
<div class="flexslider" id="<?php echo $this->slider_id; ?>">
  <ul class="slides" style="background-color: #000">
    <?php foreach( $this->items as $item): ?>    
    <?php $title  =  $item->getTitle(); 
    			$poster = $item->getOwner();
    			$photoUrl = $item -> getPhotoUrl('thumb.large');
    			if(!$photoUrl)
    			{
    				$photoUrl = $this->layout()->staticBaseUrl . 'application/modules/Ynvideo/externals/images/nophoto_video_thumb_large.png';
    			}?>
    <li class="<?php echo ++$index==1?'active':''; ?>">
      <div class="overflow-hidden" style="height:<?php echo $this->height;?>px">
      	<a href="<?php echo $item->getHref()?>">
				<span style="background-image: url(<?php echo $photoUrl;?>);"></span>
				</a>
       <?php if($title && $this->show_title): ?>
       	<div class="ynvideo_thumb_img_wrap" style="width:<?php echo $itemWidth?>px">
        <?php 
            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.large', '', 
                array('width' => $itemWidth . 'px', 'height' => $itemHeight . 'px'))) 
        ?>    
    		</div>
       <div class="carousel-caption ynvideo_featured_frame">
          	<div class="ynvideo_feature_user_thumb">
            <?php echo $this->htmlLink($poster->getHref(), $this->itemPhoto($poster, 'thumb.icon')) ?> 
		        <div class="ynvideo_thumb_img_wrap">
			        <span class="video_button_add_to_area">
			            <button class="ynvideo_uix_button ynvideo_add_button" id="ynvideo_btn_video_<?php echo $item->getIdentity()?>" video-id="<?php echo $item->getIdentity()?>">
			                <div class="ynvideo_plus"></div>
			            </button>
			        </span>
		    		</div>
		        </div>
		        <div class="ynvideo_featured_info">
	            <div class="ynvideo_featured_title">
	                <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->title, 15), array('title' => $this->string()->stripTags($item->title))) ?>
	            </div>
	            <div class="ynvideo_feature_info">
	                <?php echo $this->translate('Posted by ')?>
	                <?php echo $this->htmlLink($poster->getHref(), htmlspecialchars ($this->string()->truncate($poster->getTitle(), 15)), array('title' => $poster->getTitle()))?>
	                <?php echo $this->translate(' on ')?>
	                <?php echo $this->locale()->toDateTime(strtotime($item->creation_date), array('type' => 'date')) ?>
	            </div>
	            <div class="ynvideo_feature_description" title="<?php echo $this->string()->stripTags($item->description)?>">
	                <?php echo $this->string()->truncate($this->string()->stripTags($item->description), 150)?>
	            </div>
	            <div class="ynvideo_feature_info">
	                <?php 
	                    echo $this->partial('_video_rating_big.tpl', 'ynvideo', array('video' => $item));
	                ?>
	                <div class="ynvideo_views">
	                    <?php 
	                        echo $this->translate(array('(%1$s view)', '(%1$s views)', $item->view_count), 
	                            $this->locale()->toNumber($item->view_count));
	                    ?>
	                </div>
	                <div class="ynvideo_clear"></div>
	            </div>
        	</div>
        </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
<script type="text/javascript">
jQuery(window).load(function() {
  jQuery('#<?php echo $this -> slider_id; ?>').flexslider({
    animation: "slide"
  });
});
</script>