<div id="parallax_<?php echo $this->slider_id; ?>" class="advalbum_responsive_slideshow_parallax pxs_container" style="height:<?php echo $this -> height;?>px">
	<div class="pxs_bg" style="background-image: url(<?php echo $this->background_image; ?>); height:100%"></div>
	<div class="pxs_loading"><?php echo $this -> translate("Loading images...");?></div>
	<div class="pxs_slider_wrapper">
		<ul class="pxs_slider">
			<?php foreach($this->items as $item): ?>
			<li>
			  <?php if($this->show_description || $this->show_title): ?>
				<div class = "pxs_slider_content" >
				  <?php if($this->show_title): ?>
					<h2><?php echo $this->escape($item->getTitle());?></h2>
					<?php endif; ?>
					<?php if($this->show_description): ?>
					<P><?php echo $this->escape($item->getDescription());?></P>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<a href="<?php echo $item -> getHref();?>"><img style="max-height:<?php echo $this -> height - 40;?>px; margin: 15px auto 0" src="<?php echo $this->escape($item->getPhotoUrl());?>" alt="<?php echo $this->escape($item->getTitle());?>" /></a>
			</li>
			<?php endforeach; ?>
		</ul>
		<div class="pxs_navigation">
			<span class="pxs_next"></span>
			<span class="pxs_prev"></span>
		</div>
		<ul class="pxs_thumbnails">
		<?php foreach($this->items as $item): ?>
			<li>
			  <img src="<?php echo $this->escape($item->getPhotoUrl('thumb.icon'));?>" alt="<?php echo $this->escape($item->getTitle());?>" />
			</li>
		 <?php endforeach; ?>
		</ul>
	</div>
</div>
<script type="text/javascript">
	jQuery('#parallax_<?php echo $this->slider_id; ?>').parallaxSlider({
    auto: <?php echo $this -> speed?>,
    speed: 1000
  });
</script>
<?php if(defined('YNRESPONSIVE')):?>
<div class="advalbum_responsive_slideshow_flex flexslider" id="flexslider_<?php echo $this->slider_id; ?>">
  <ul class="slides">
    <?php foreach( $this->items as $item): ?>    
    <?php $title  =  $item->getTitle(); ?>
    <li class="<?php echo ++$index==1?'active':''; ?>">
      <div class="overflow-hidden" style="height:350px">
		  <span style="background-image: url(<?php echo $item -> getPhotoUrl()?>);"></span>
            <?php if($title && $this->show_title): ?>
            <div class="carousel-caption">
              <p><?php echo $this->htmlLink($item->getHref(), $title) ?></p>
            </div>
            <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
    
  </ul>
</div>
<script type="text/javascript">
jQuery(window).load(function() {
  jQuery('#flexslider_<?php echo $this -> slider_id; ?>').flexslider({
    animation: "slide",
	auto: <?php echo $this -> speed?>,
    speed: 1000
  });
});
</script>
<?php endif;?>