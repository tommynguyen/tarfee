<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US">
<head>
<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<meta HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<?php $tmp = date("YmdHis"); ?>
<link rel="stylesheet" href="<?php echo $this->baseUrl() ?>/application/modules/Advalbum/externals/slideshow/idangerous.swiper.css">
<?php
$photo_count = count($this->arr_photos);
$thumbnails_height = $photo_count * (90+2*2+1*2+9); // height, padding, border, margin bottom
?>
<style>
html,
body {
	background-color: transparent;
	height:300px;
}
body,html {
	position: relative;
	height: 100%;
}
.swiper-container {
	width: 100%;
	height: 100%;
}
.swiper-wrapper {
	height: 100%;
}
.swiper-slide {
	text-align: center;
	height: 100%;
	/*width: 25%;*/
}
.swiper-slide img {
	vertical-align: middle;
	width: 100%;
	height: auto;
	border:1px solid #fff;
	box-shadow: 0px 2px 5px rgba(0,0,0,0.2);
	-webkit-transition: 300ms;
	-moz-transition: 300ms;
	-ms-transition: 300ms;
	-o-transition: 300ms;
	transition: 300ms;
	-webkit-backface-visibility: hidden; 
	opacity: 0.5;
}
.swiper-slide-active img {
	-webkit-transform: scale(1);
	-moz-transform: scale(1);
	-ms-transform: scale(1);
	-o-transform: scale(1);
	transform: scale(1);
	opacity: 1;
}
.swiper-slide .inner {
	height: 100%;
	padding: 0px 5px 0px;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
}
.pagination {
	position: absolute;
	text-align: center;
	left: 0;
	bottom: 0;
	width: 100%;
	line-height: 40px;
	height: 40px;
	z-index: 20;
}
.swiper-pagination-switch {
	vertical-align: middle;
	display: inline-block;
	width: 14px;
	height: 14px;
	background: #000;
	cursor: pointer;
	-webkit-transition: 300ms;
	-moz-transition: 300ms;
	-ms-transition: 300ms;
	-o-transition: 300ms;
	transition: 300ms;
	margin: 0 1px;
}
.swiper-pagination-switch:first-child {
	border-radius: 3px 0 0 3px;
}
.swiper-pagination-switch:last-child {
	border-radius: 0 3px 3px 0;
}
.swiper-visible-switch {
	background: #888;
}
.swiper-active-switch {
	background: #fff;
}


</style>
<script type="text/javascript" src="<?php echo $this->baseUrl() ?>/application/modules/Advalbum/externals/slideshow/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl() ?>/application/modules/Advalbum/externals/scripts/idangerous.swiper-2.0.min.js"></script>
<script type="text/javascript">
	$(function(){
		var gallery = $('.ym_large_featuredphoto').swiper({
			slidesPerView:'auto',
			watchActiveIndex: true,
			calculateHeight:true,
			centeredSlides: true,
			resizeReInit: true,
			keyboardControl: true,
			updateOnImagesReady:true,
			grabCursor: true,
			onImagesReady: function(){
				changeSize()
			}
		})
		function changeSize() {
			//Unset Width
			$('.swiper-slide').css('width','')
			//Get Size
			var imgWidth = $('.swiper-slide img').width();
			if (imgWidth+40>$(window).width()) imgWidth = $(window).width()-40;
			//Set Width
			$('.swiper-slide').css('width', imgWidth+40);
		}	
		changeSize();
		//Smart resize
		$(window).resize(function(){
			changeSize()
			gallery.resizeFix(true)	
		})
	})
</script>
</head>
<body>
<div class="swiper-container ym_large_featuredphoto">
	
	<div class="swiper-wrapper" style="width:2424px;">
		<?php
		foreach ($this->arr_photos as $photo_item) {
		?>
		<div class="swiper-slide">
			<div class="inner">
				<a target='_parent' href="<?php echo $photo_item->getHref() ?>">
					<img src ="<?php echo $photo_url = $photo_item->getPhotoUrl("profile.normal"); ?>"/>
				</a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
</body>
</html>
<?php function fixPhotoURL($photo_url) {
	$pos = strpos($photo_url, '?');
	if ($pos!==FALSE) {
		$photo_url = substr($photo_url, 0, $pos);
	}
	return $photo_url;
}
?>