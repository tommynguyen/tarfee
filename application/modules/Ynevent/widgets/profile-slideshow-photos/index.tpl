<?php
$this->headScript()
	->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js')
	->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/jquery.flexslider.js');

$this->headLink()
	->appendStylesheet($this->baseUrl() . '/application/modules/Ynevent/externals/styles/flexslider.css');
?>

<!-- Place somewhere in the <body> of your page -->
<div class="flexslider">
  <ul class="slides">
  	<?php foreach ($this->paginator as $photo_item) :?>
	    <li>
		    <span class="flexslider-image" style="background-image: url(<?php echo $photo_item->getPhotoUrl('thumb.main'); ?>);"></span>
	    </li>	    
    <?php endforeach;?>
  </ul>
</div>

<style type="text/css">
	.flexslider-image {
		width: 100%;
		height: 200px;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center;
		display: block;
	}
</style>

<script type="text/javascript" charset="utf-8">
	jQuery.noConflict();
	jQuery(window).load(function() {
		jQuery('.flexslider').flexslider({
                prevText: "",
                nextText: "", 
				animation: '<?php echo $this->effect; ?>',
				animationLoop: <?php echo ($this->allowLoop) ? 'true' : 'false'; ?>
		});
  	});
</script>
