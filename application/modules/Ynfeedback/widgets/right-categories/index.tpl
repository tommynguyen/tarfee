<ul class="ynfeedback-right-categories">
	<?php foreach($this -> categories as $category):?>
	<?php $childCount = $category -> getFeedbackCount();?>
	<li class="ynfeedback-right-categories-item <?php echo (isset($_GET['category_id']) && ($_GET['category_id']==$category->getIdentity()))?'active':''; ?>">
		<a href="<?php echo $this->url(array('action' => 'listing'), 'ynfeedback_general') . "?category_id=" . $category->getIdentity();?>">
			<i class="fa fa-angle-right"></i>
			<span><?php echo $this-> translate($category -> title);?></span>
			<span class="counter"><?php echo $childCount;?></span>
		</a>
	</li>
	<?php endforeach;?>
</ul>