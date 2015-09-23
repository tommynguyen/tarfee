<?php if( count($this->blogs) >  0 ): ?>
<?php 
	
		$this->headScript()
		//->appendFile($this->baseUrl() . '/application/modules/ynblog/externals/scripts/ynblog_function.js')
		->appendFile($this->baseUrl() . '/application/modules/Ynblog/externals/scripts/slideshow/Navigation.js')
		->appendFile($this->baseUrl() . '/application/modules/Ynblog/externals/scripts/slideshow/Loop.js')
		->appendFile($this->baseUrl() . '/application/modules/Ynblog/externals/scripts/slideshow/SlideShow.js');

 ?>
<section id="ynblog_navigation" class="demo">
	<div id="ynblog_navigation-slideshow" class="slideshowblog">
		<?php
		$i = 0;
		foreach ($this->blogs as $item):
		$owner = $item->getOwner();
		if($i < $this->limit):
		$i ++;
		?>
		<span id="lp<?php echo $i?>">


			<div class="featured_blogs">
				<div class="featured_blogs_img_wrapper">
					<div class="featured_blogs_img">
						<a href="<?php echo $owner->getHref()?>"> <?php if($owner->getPhotoUrl("thumb.profile")!= null):?>
							<img src="<?php echo $owner->getPhotoUrl("thumb.profile");?>" /> <?php else:?>
							<img
							src="./application/modules/Ynblog/externals/images/nophoto_user_thumb_normal.png" />
							<?php endif;?>
						</a>
					</div>
				</div>
				<div class="blog_info">
					<div class="blog_title" style="font-size: 15px; color: #3BA3D0">
						<b><?php echo $item ?> </b>
					</div>
					<div class="blog_owner" style="font-size: 11px; color: #7E7E7E;">
						<?php echo $this->translate("Posted by");?>

						<?php echo $this->htmlLink($owner->getHref(),$owner->getTitle());?>												
					</div>
					<p class="blog_description">
						<?php echo Engine_Api::_()->ynblog()->subPhrase(strip_tags($item->body),450);?>
					</p>
					

				</div>
			</div>

		</span>

		<?php endif;  endforeach; ?>
		<ul class="ynblog_pagination" id="ynblog_pagination">
			<li><a class="current" href="#lp1"></a></li>
			<?php for ($j = 2; $j <= $i; $j ++):?>
			<li><a href="#lp<?php echo $j?>"></a></li>
			<?php endfor;?>
		</ul>
	</div>
</section>


<?php else: ?>
<div class="tip">
	<span> <?php echo $this->translate('There is no featured blog yet.');?>
	</span>
</div>
<?php endif;?>

<?php
