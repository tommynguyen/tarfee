<?php if( count($this->groups) >  0 ): ?>
<?php 
	$this->headLink()
		->prependStylesheet($this->baseUrl(). '/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.css');
?>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advgroup/externals/scripts/carousel/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advgroup/externals/scripts/carousel/jquery-migrate-1.1.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.min.js"></script>
<div class="advgroup_featured_groups" id="carousel-container">
	<?php
		$i = 0;
		foreach ($this->groups as $item):
		
		$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		{
			$title = $item->getTitle();
			$owner_name = $item->getOwner()->getTitle();
			}
			else
			{
			$title = $this -> string() -> truncate($item->getTitle(),17);
			$owner_name = $this -> string() -> truncate($item->getOwner()->getTitle(),13);
		}			
		$owner = $item->getOwner();
		if($i < $this->limit):
		$i ++;
	?>
	<div class="item_group">
		<div class="top">
			<?php if($item->isNewGroup()): ?>								
			<span class="newGroup"></span>														
			<?php endif; ?>	
			<a class="item_photo" href="<?php echo $item->getHref()?>">
				<div href="<?php echo $item->getHref()?>" class="photo">				 
					<?php if($item->getPhotoUrl("thumb.feature")!= null):?>
					<span style="background-image: url('<?php echo $item->getPhotoUrl("thumb.feature");?>');"></span> 
					<?php else:?>
					<span style="background-image: url('<?php echo $this->baseUrl(); ?>/application/modules/Advgroup/externals/images/nophoto_group_thumb_feature.png');"></span> 
					<?php endif;?>
				</div>
				<div class="overlay"></div>
				<div class="item_info">
					<div class="item_title">
						<span><?php echo $this->translate($title); ?></span>
						<?php //echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
					</div>
					<div class="item_stats">
						<div class="time_active">
							<i class="ynicon-time" title="Time create"></i>
							<?php echo $item -> getTimeAgo(); ?>
						</div>
						<div class="groups_members">
							<i class="ynicon-person" title="Guests"></i>
							<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
						</div>
					</div>
				</div>
			</a>			
		</div>
		<div class="bottom">
			<div class="item_title">
				<?php echo $item ?>
			</div>
			<p class="group_description">
				<?php if(strlen(strip_tags($item->description)) > 60) 
						echo $this -> string() -> truncate(strip_tags($item->description), 50);
                    else 
						echo strip_tags($item->description);
				?>
			</p>
		</div>
	</div>
	<?php endif;  endforeach; ?>
</div>
<script type="text/javascript">
	jQuery.noConflict();
	(function($){
		$(function(){
			if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))){
				if(!$('body').hasClass('Web')){
					$('body').addClass('Web');
				}
			}
			
			/* check column */
			var item = $$('#global_content .layout_main > div[class*="layout_"]');
			if(item.length < 3){
				$('#carousel-container').owlCarousel({
					loop: true,
					margin: 1,
					slideBy: 1,
					responsiveClass:true,
					responsive:{
						0:{
							items: 1,
							nav: false
						},
						560:{
							items: 2,
							nav: true
						},
						768:{
							items: 3,
							nav: true
						}
					},
					autoplay: true,
					autoplayTimeout: 2000,
					autoplayHoverPause: true,
				});
			}
			else {
				$('#carousel-container').owlCarousel({
					loop: true,
					margin: 1,
					slideBy: 1,
					responsiveClass:true,
					responsive:{
						0:{
							items: 1,
							nav: false
						},
						560:{
							items: 2,
							nav: true
						}
					},
					autoplay: true,
					autoplayTimeout: 2000,
					autoplayHoverPause: true,
				});
			}
		});
	})(jQuery);
</script>
<?php else: ?>

<div class="tip">
	<span><?php echo $this->translate('There is no featured group yet.');?></span>
</div>
<?php endif; ?>