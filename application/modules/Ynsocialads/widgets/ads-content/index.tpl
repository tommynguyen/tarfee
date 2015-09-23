<?php if(count($this->ads) > 0) :?>
<div class="ynsocial_ads" >
	<div class="ynsocial_ads_content">
		<?php foreach($this->ads as $ad) :?>
		<div class="ynsocial_ads_item">
			<?php 
				$photoTable = Engine_Api::_() -> getItemTable('ynsocialads_photo');
				$photos = $photoTable -> getPhotosAd($ad -> getIdentity());
			?>
			<div class="slideshow-ad" id="slideshow-container-<?php echo $ad->getIdentity();?>">
				<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $ad->getIdentity(); ?> ynsocial_ads_cont_image" href="<?php echo $ad->getLinkUpdateStats()?>">
					<img src="<?php echo $ad -> getPhotoUrl('thumb.normal') ?>"/>
				</a>	
				<?php if(!empty($photos)) :?>
					<?php foreach($photos as $photo) :?>
						<a ad_id='<?php echo $ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $ad->getIdentity(); ?> ynsocial_ads_cont_image" href="<?php echo $ad->getLinkUpdateStats()?>">
						<?php echo $this -> itemPhoto($photo);?>
						</a>
					<?php endforeach;?>
				<?php endif;?>
			</div>	
		</div>	
		<?php endforeach;?>
	</div>
</div>
<?php endif; ?>	

<script type="text/javascript">
var preventClick = function(obj,event){
		var ad_id = obj.getProperty('ad_id');
		var prevent_click = '.prevent_click_'+ad_id;
		$$(prevent_click).addClass('click_disabled');
}
</script>