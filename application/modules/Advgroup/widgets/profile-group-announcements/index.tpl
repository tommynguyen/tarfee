<?php 
	$this->headLink()
		->prependStylesheet($this->baseUrl(). '/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.css');
	$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/jquery-1.9.1.min.js')
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/jquery-migrate-1.1.1.min.js')
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.min.js');

?>
<?php  if(count($this->announcements) <= 0) :?> 
<?php  if($this->allow_manage):?>
<ul class="announcements">
	<li>
		<div class="advgroup_announcement_item" style="position: relative;">
			<div class="title">
				<?php echo $this->translate("Announcement"); ?>
			</div>
			<div class='advgroup_announcement_button'>
				<?php if($this->aManageAnnouncementButton):?> 
	 				  <a href="<?php echo $this->url($this->aManageAnnouncementButton['params'], $this->aManageAnnouncementButton['route'], array());?>" class="advgroup_icon_annoucement_manage buttonlink">	    			
				</a>
				<?php endif;?>
			</div>
		</div>
	</li>
</ul>
<?php endif;?>  
<?php else:?>   
<ul class="announcements" id="advgroup_announcement">	
    <?php foreach( $this->announcements as $item ): ?>
		<li>
			<div class="advgroup_announcement_item">
				<div class="title">
					<div class='advgroup_announcement_button'>
					<?php if($this->aManageAnnouncementButton):?>				
						<a href="<?php echo $this->url($this->aManageAnnouncementButton['params'], $this->aManageAnnouncementButton['route'], array());?>" class="advgroup_icon_annoucement_manage buttonlink">				
						</a>	
						<span></span>				
					<?php endif;?>
				
					<?php echo $this->htmlLink(array(
						'route' => 'group_extended',
						'module' => 'advgroup',
						'controller' => 'announcement',
						'action' => 'mark',
						'group_id' => $this->group->getIdentity(),
						'announcement_id' => $item->getIdentity(),
						'user_id' => $this->user_id,		
					  ), $this->translate('Mark this announcement?'), array(
						'class' => 'advgroup_icon_annoucement_mark buttonlink smoothbox',
						'title'=>"Mark this announcement"
					))?>
					</div>
					<p><?php echo $item->title ?></p>
				</div>
				<div class="content">					
					<?php echo $item->body;  ?>
				</div>
			</div>
			<?php  ?>			
      </li>
      <?php endforeach;?>
</ul>
<?php endif; ?>
<script type="text/javascript">
	function toggleInfo(block_id,img_id){
		if(document.getElementById(block_id).style.display == 'none'){
			document.getElementById(block_id).style.display = 'block';
			document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/up.jpg';
		}else{
			document.getElementById(block_id).style.display = 'none';
			document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/down.jpg';
		}
	}
</script>
<script type="text/javascript">
	jQuery.noConflict();
	(function($){
		$(function(){
			if($('#advgroup_announcement > li').length > 1){
				$('#advgroup_announcement').owlCarousel({
					loop: true,
					margin: 1,
					slideBy: 1,
					autoHeight : true,
					responsiveClass:true,
					responsive:{
						0:{
							items: 1,
							nav: true
						}
					}
				});
			}
		});
	})(jQuery);
</script>