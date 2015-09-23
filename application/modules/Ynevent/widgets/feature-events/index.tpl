<?php
$session = new Zend_Session_Namespace('mobile');
if($session -> mobile)
{
	echo $this->html_mobile_slideshow;
}
else
{
 $this->headScript()
	->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places')
    ->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/Navigation.js')
	->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/Loop.js')
	->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/SlideShow.js');
   
?>
<script type="text/javascript">
	var eventNavigation = function(url)
	{
		window.location = url;
	}
	
</script>

<div id="ynevent_navigation" class="demo">
	<div id="ynevent_navigation_slideshow" class="ynevent_slideshow">
		<ul id = "ynevent_slideshow_left">
			<?php foreach($this->items as $event): ?>
				<li id="lp<?php echo $event->event_id; ?>" class = "ynevent_slideshow_slide">
			    	<div class="ynevent_album_photo">
			    		<?php
						$eventPhotoUrl = "";
						if ($event->cover_photo)
						{
							$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($event->cover_photo)->current();
							$eventPhotoUrl = $coverFile->map();
						}
						if (!$eventPhotoUrl)
						{
							$eventPhotoUrl = $event->getPhotoUrl('thumb.main');
						}
						?>
			    		<?php echo $this->htmlLink($event->getHref(), '<span class="image-thumb" style="background-image: url('.$eventPhotoUrl.');"></span>'); ?>
			       </div>				
			    </li> 
			<?php endforeach; ?> 	 
		</ul>
		<ul class="ynevent_slideshow_pagination" id="ynevent_pagination">
			<?php
            $current = 1; 
            foreach($this->items as $event): 
	            $startDateObject = new Zend_Date(strtotime($event->starttime));
				$endDateObject = new Zend_Date(strtotime($event->endtime));
				if( $this->viewer() && $this->viewer()->getIdentity() ) 
				{
					$tz = $this->viewer()->timezone;
					$startDateObject->setTimezone($tz);
					$endDateObject->setTimezone($tz);  
				}
			?>
			<li>
                <a class="<?php if ($current == 1) { echo 'current'; $current=0; } ?>" href="#lp<?php echo $event->event_id; ?>">
                    <div class="ynevent_albumfeatured_info">
          				<div title="<?php echo $event->title; ?>" onclick="eventNavigation('<?php echo $event->getHref()?>');" class="ynevent_album_info ynevent_album_title">
                            <?php echo $event->title; ?>
        				</div>
        				<p class="ynevent_album_info"><span><i class="ynicon-time-w" title="<?php echo $this -> translate("Time of event")?>"></i></span><?php echo $this->locale()->toDate($startDateObject).' - '.$this->locale()->toDate($endDateObject)?></p>
        				<?php if($event->address):?>
        					<p class="ynevent_album_info" title="<?php echo $event->address; ?>"><span><i class="ynicon-location-w" title="<?php echo $this -> translate("Location")?>"></i></span><?php echo $this->string()->truncate($event->address, 25); ?></p>
                        <?php endif;?>
                        <p class="ynevent_album_info"><span><i class="ynicon-person-w" title="<?php echo $this -> translate("Guests")?>"></i></span><?php echo $this->translate(array('%s guest', '%s guests', $event->member_count), $this->locale()->toNumber($event->member_count)) ?> </p>
                    </div>
                </a>
            </li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

<?php } ?>

<?php
if($session -> mobile)
{
	$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/scripts/jquery-1.7.1.min.js')
		->appendFile($this->baseUrl() . '/application/modules/Ynevent/externals/slideshow/responsiveslides.min.js');
	$this->headLink()
		->appendStylesheet($this->baseUrl() . '/application/modules/Ynevent/externals/slideshow/responsiveslides.css');
?>

<script type="text/javascript">

	jQuery.noConflict();
	jQuery(function () {
		 jQuery('#ymb_home_featuredevent').responsiveSlides({
	        nav: true,
	        speed: 800,
	        namespace: "callbacks"
	      });
	   });
</script>
<?php } ?>