<?php
$viewmore_top_url = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('action'=>'listing','sort'=>'top'), 'album_general', null);
	  
$viewmore_recent_url = Zend_Controller_Front::getInstance()->getRouter()
      ->assemble(array('action'=>'listing','sort'=>'recent'), 'album_general', null);
$session = new Zend_Session_Namespace('mobile'); 	 


?>
<div class="layout_advalbum_top_recent_albums_container clearfix">
	<div class="=adv-inner-top-recent-album ymbHomeAbumSlideshow">
		<h3><?php echo $this->translate('Recent Albums');?></h3>
		<div class="global_form_box ">
		<?php echo $this->html_full_recent_albums; ?>
		<?php if (($this->top_albums_count>=6 || $this->recent_albums_count>=6) && !$session -> mobile) { ?> 
			<div class="advalbum_view_more">
                <?php if ($this->recent_albums_count>=6) {?><a href="<?php echo $viewmore_recent_url; ?>"><?php echo $this->translate('View more'); ?></a><?php } else echo "&nbsp;"; ?>
            </div>
		<?php } ?>	
		</div>
	</div>
	<div class="adv-inner-top-album ymbHomeAbumSlideshow">
		<h3><?php echo $this->translate('Top Albums');?></h3>
		<div class="global_form_box">
		<?php echo $this->html_full_top_albums; ?>
		<?php if (($this->top_albums_count>=6 || $this->recent_albums_count>=6 )&& !$session -> mobile) { ?> 
			<div class="advalbum_view_more">
                <?php if ($this->top_albums_count>=6) {?><a href="<?php echo $viewmore_top_url; ?>"><?php echo $this->translate('View more'); ?></a><?php } else echo "&nbsp;"; ?>
            </div>
		<?php } ?>	
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
