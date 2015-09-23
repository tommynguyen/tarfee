<div class="event-categories-title" >
    <span data-toggle="collapse" data-target=".event-categories-main"></span> 
	<a href="<?php echo $this -> url(array(), 'ynresponsive_event_listtng', true)?>"><?php echo $this -> translate("All")?></a>
</div>
<ul class="event-categories-main in">
	<?php foreach($this -> categories as $category):?>
	<li <?php if($category -> getIdentity() == $this -> category_id) echo "class='active'"; ?>>
		<a href="<?php echo $this -> url(array('category_id' => $category -> getIdentity()), 'ynresponsive_event_listtng', true)?>"> <?php echo $this -> translate($category -> title);?></a>
	</li>
	<?php endforeach; ?>
</ul>