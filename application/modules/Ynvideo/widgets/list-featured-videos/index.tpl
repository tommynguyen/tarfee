<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
 
 $session = new Zend_Session_Namespace('mobile');
 if($session -> mobile)
 {
 	echo $this->html_mobile_slideshow;
 }
 else
 {
?>
<style>
    #ynvideo_featured .slides_container div.slide {
        width : <?php echo $this->slideWidth - 30?>px;
    }
    
    #ynvideo_featured .slides_container {
        height : <?php echo $this->slideHeight - 40?>px;
    }
    
    #ynvideo_featured .slides_container .slide {
        height : <?php echo $this->slideHeight - 40?>px;
    }
</style>
<?php
if(defined('YNRESPONSIVE')):?>
<div id="mobilesize_slideshow" class="mobilesize_slideshow">
	<?php echo $this->html_ynresponsive_slideshow; ?>
</div>
<?php endif;?>
<div id="ynvideo_featured" class="fullsize_slideshow">
    <div id="slides" style="width:<?php echo $this->slideWidth?>px;height:<?php echo $this->slideHeight?>px">
        <div class="slides_container">
            <?php foreach ($this->videos as $index => $video) : ?>
                <?php if ($index % 2 == 0) : ?>
                    <div class="slide" style="width:<?php echo $this->slideWidth ?>px">
                <?php endif; ?>
                    <?php
                        echo $this->partial('_video_featured.tpl', 'ynvideo', array(
                            'video' => $video,
                            'videoWidth' => ($this->slideWidth - 10) / 2 - 10,
                            'videoHeight' => $this->slideHeight - 140,
                        ));
                    ?>
                <?php if ( (($index + 1) % 2 == 0) || ($index == (count($this->videos)-1)) ) : ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php } ?>

<?php
if($session -> mobile)
{
	$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Ynvideo/externals/slideshow/responsiveslides.min.js');
	$this->headLink()
		->appendStylesheet($this->baseUrl() . '/application/modules/Ynvideo/externals/slideshow/responsiveslides.css');
?>

<script type="text/javascript">
	jQuery(function () 
	{
		 jQuery("#ymb_home_featuredvideo").responsiveSlides({
	        nav: true,
	        speed: 800,
	        namespace: "callbacks"
	      });
	   });
</script>
<?php 
}
else if(defined('YNRESPONSIVE'))
{?>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
    	var list_middle = $$('.layout_main .layout_middle');
    	var position = list_middle[0].getCoordinates();
        var slideWidth = position.width;
        if(slideWidth < 741)
        {
        	$('ynvideo_featured').style.display = 'none';
        	$('mobilesize_slideshow').style.display = 'block';
        }
        else
        {
        	$('ynvideo_featured').style.display = 'block';
        	$('mobilesize_slideshow').style.display = 'none';
        }
    });
</script>
<?php } ?>