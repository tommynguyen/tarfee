<?php 
$settings = Engine_Api::_()->getApi('settings', 'core');
$color = $settings->getSetting('tffooter_color', 'EEEEEE'); 
$categories = Engine_Api::_() -> getDbTable('categories', 'socialConnect') -> getAllCategories();
$app_store = $settings->getSetting('tffooter_applelink', '');
$google = $settings->getSetting('tffooter_googlelink', '');
$table = Engine_Api::_()->getDbTable('pages', 'socialConnect');?>
<div class="tf_footer_landing" <?php if($color):?>style = "background-color:<?php echo $color?>"<?php endif;?>>
	<div class="tf_footer_landing_inner">
		<div class="tf_footer_col_left">
			<?php foreach($categories as $category):?>
			<ul>
				<li class="category_title"><?php echo $category -> category_name?></li>
				<?php 
					  $select = $table -> select();
					  $select -> where('category_id = ?', $category -> getIdentity());
       				  $pages = $table->fetchAll($select -> order('order'));?>
       			<?php foreach($pages as $page):?>
					<li><a href="javascript:void(0)" data-target="#tffooter_page_<?php echo $page -> getIdentity()?>" data-toggle="modal"><?php echo $page -> title?></a></li>
				<?php endforeach;?>
			</ul>
			<?php endforeach;?>
		</div>
		<div class="tf_footer_col_right">
			<div class="section-row">
				<div class="buttons-icons">
					<a href="<?php echo $app_store?>" id="footer-app-store-link">
						<img src="application/themes/ynresponsive-event/images/app-store-badge.png" width="150" class="appstore-app app_store" height="43">
					</a>
					<a href="<?php echo $google?>" id="footer-app-store-link">
						<img src="application/themes/ynresponsive-event/images/google_play_badge_v2.png" width="150" class="appstore-app google_play" height="43">
					</a>
				</div>
			</div>
		</div>
	</div><!--end tf footer landing-->
	<?php $select = $table -> select();
		  $pages = $table->fetchAll($select -> order('order'));?>
	<?php foreach($pages as $page):?>
		<!-- Modal-->
		<div class="modal fade" id="tffooter_page_<?php echo $page -> getIdentity()?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		  <div class="modal-dialog">
		    <div class="modal-content">
		    	<button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
				<h2><?php echo $page -> title?></h2>
				<?php echo $page -> content?>
		    </div>
		  </div>
		</div>
	<?php endforeach;?>
</div>
