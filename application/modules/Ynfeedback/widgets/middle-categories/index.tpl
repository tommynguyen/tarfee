<ul class="ynfeedback-middle-categories">
	<?php foreach($this -> categories as $category):?>
	<li>
		<div class="ynfeedback-middle-categories-item">
		<a href="<?php echo $this->url(array('action' => 'listing'), 'ynfeedback_general') . "?category_id=" . $category->getIdentity();?>"><?php echo $this-> translate($category -> title);?></a>
		<?php $childCount = $category -> getFeedbackCount();?>
		<div class="ynfeedback-middle-categories-count"><?php echo $this -> translate( array("%s feedback", "%s feedbacks", $childCount), $childCount );?></div>
		</div>
	</li>
	<?php endforeach;?>
</ul>