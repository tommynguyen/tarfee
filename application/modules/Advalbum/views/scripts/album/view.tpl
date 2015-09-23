<?php
	//Check mobile
	$session = new Zend_Session_Namespace('mobile');
	if(!$session -> mobile)
	{
		$this->headScript()
			->appendFile($this->baseUrl() . '/application/modules/Advalbum/externals/scripts/tinybox.js');;
		$this->headLink()
			->appendStylesheet($this->baseUrl() . '/application/modules/Advalbum/externals/styles/prettyPhoto.css');
	}
?>

<?php if ($this->slideshow || $this->playlist) 
{
	echo $this->html_full;?>
	<script type='text/javascript'>
		<?php if($this->body_class):?>
			document.body.addClass("<?php echo $this->body_class;?>");
		<?php endif;?>
	</script>
<?php
	return;
}
?>

<script type='text/javascript'>

//rating
en4.core.runonce.add(function() 
{
    var album_pre_rate = <?php echo $this->album->rating; ?>;
    var album_rated = '<?php echo $this->is_rated; ?>';
    var album_id = <?php echo $this->album->getIdentity(); ?>;
    var album_total_votes = <?php echo $this->rating_count; ?>;
    var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

    var album_rating_over = window.album_rating_over = function(rating) {
        if( album_rated == 1 ) {
            $('rating_text').innerHTML = "<?php echo $this->translate('you already rated'); ?>";
            //set_rating();
        } else if( viewer == 0 ) {
            $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate'); ?>";
        } else {
            $('rating_text').innerHTML = "<?php echo $this->translate('click to rate'); ?>";
            for(var x=1; x<=5; x++) {
                if(x <= rating) {
                    $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
                } else {
                    $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
                }
            }
        }
    }

    var album_rating_out = window.album_rating_out = function() {
		  if (album_total_votes > 0 && album_total_votes <= 1) {
            $('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
            $('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('ratings')?>";
		  }
        if (album_pre_rate != 0){
        	album_set_rating();
        }
        else {
            for(var x=1; x<=5; x++) {
                $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
            }
        }
    }

    var album_set_rating = window.album_set_rating = function() {
        var rating = album_pre_rate;
        if (album_total_votes > 0 && album_total_votes <= 1) {
            $('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
            $('rating_text').innerHTML = album_total_votes + " <?php echo $this->translate('ratings')?>";
		  }
        for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big');
        }

        for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
        }

        var remainder = Math.round(rating)-rating;
        if (remainder <= 0.5 && remainder !=0){
            var last = parseInt(rating)+1;
            $('rate_'+last).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_half');
        }
    }

    var album_rate = window.album_rate = function(rating) {
        $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
        for(var x=1; x<=5; x++) {
            $('rate_'+x).set('onclick', '');
        }
        (new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->album->getIdentity()), 'album_extended', true) ?>',
            'data' : {
                'format' : 'json',
                'rating' : rating,
                'is_album' : 1
            },
            'onRequest' : function(){
            	album_rated = 1;
            	album_total_votes = album_total_votes + 1;
            	album_pre_rate = (album_pre_rate + rating)/album_total_votes;
                album_set_rating();
            },
            'onSuccess' : function(responseJSON, responseText)
            {
            }
        })).send();
    }

    album_set_rating();
});
</script>
<?php if(!$session -> mobile){ ?>
<script type="text/javascript">
	
window.addEvent('domready', function(){

	addEventSlideshow();
	// add event for button Add to of Adv.Album photo
	function addEventSlideshow() {
		//e.preventDefault();
		var position = $('advalbum_slideshow_button').getPosition();
		var size = $('advalbum_slideshow_menu').getSize();
		$('advalbum_slideshow_menu').setPosition({x: position.x - 190, y: 50 + $('advalbum_slideshow_button').getHeight()});
		$('advalbum_slideshow_menu').setStyle('display', 'none');
		$(document.body).addEvent('click', function(event){
			 var target = event.target;
           // if the user click outside the add to menu box, remove the add to menu box
           if (!target.contains($('advalbum_slideshow_button')) && !target.contains($('advalbum_slideshow_menu'))){
           		$('advalbum_slideshow_menu').setStyle('display', 'none');
            	if ($('advalbum_slideshow_button').hasClass('active')) {
            		$('advalbum_slideshow_button').removeClass('active');
            	}
           }
		});
		$('advalbum_slideshow_button').addEvent('click', function(event){
            // if the user click on slideshow button, show menu if it has existed
            var slideshow_menu = $('advalbum_slideshow_menu');
            if (slideshow_menu.getStyle('display') == 'none') {
            	slideshow_menu.setStyle('display', 'block');
            	this.addClass('active');
            }
            else {
                // show slideshow menu
            	slideshow_menu.setStyle('display', 'none');
            	if (this.hasClass('active')) {
            		this.removeClass('active');
            	}
            }
		});

		$$('#advalbum_slideshow_menu li').each(function(el) {
			el.addEvent('click', function(e){
				$$('#advalbum_slideshow_menu li.active').each(function(el_active){
					el_active.removeClass('active');
				});
				el.addClass('active');
				$('advalbum_slideshow').removeEvents('click');
				$('advalbum_slideshow').addEvent('click', function(ev) {
					slideshow_url = '<?php echo $this->album->getHref(array('slideshow'=>'1')) ?>' + '/effect/' + el.get('for');
					popupSlideshow(slideshow_url);
				});
			});
		});
	}
});
<?php } ?>

</script>
<div>
	<div>
		<!-- MIDDLE CONTENT -->
		<div>
			<form name="gotoPage" id="gotoPage" method="post">
				<input type="hidden" name="nextpage" id="nextpage">

			<?php if ($this->album->count() > 0 && !$session -> mobile) : ?>
			<!-- Slideshow -->
			<span id="advalbum_slideshow_button"></span>
			<ul id="advalbum_slideshow_menu">
			   <li class="border_bottom advalbum_slideshow_item active" for='kenburns'><?php echo $this->translate('Original Effect')?></li>
			   <li class="border_bottom advalbum_slideshow_item" for='flash'><?php echo $this->translate('Flash Effect')?></li>
			   <li class="border_bottom advalbum_slideshow_item" for='fold'><?php echo $this->translate('Fold Effect')?></li>
			   <li class="border_bottom advalbum_slideshow_item" for='push'><?php echo $this->translate('Fush Effect')?></li>
			</ul>
			<?php $slideshow_url = $this->album->getHref(array('slideshow'=>'1')) . '/effect/kenburns' ?>
			<a href="javascript:;" class="slideshow_button" id='advalbum_slideshow' onclick="return popupSlideshow('<?php echo $slideshow_url ?>')"><?php echo $this->translate('Slide Show');?></a>
			<?php endif; ?>
			<h2>
			  <?php echo $this->translate('%1$s\'s Album: %2$s', $this->album->getOwner()->__toString(), $this->album->getTitle()); ?>
			</h2>
				<div id="video_rating" class="rating" onmouseout="album_rating_out();">
					<span id="rate_1" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="album_rate(1);"<?php endif; ?> onmouseover="album_rating_over(1);"></span>
					<span id="rate_2" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="album_rate(2);"<?php endif; ?> onmouseover="album_rating_over(2);"></span>
					<span id="rate_3" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="album_rate(3);"<?php endif; ?> onmouseover="album_rating_over(3);"></span>
					<span id="rate_4" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="album_rate(4);"<?php endif; ?> onmouseover="album_rating_over(4);"></span>
					<span id="rate_5" class="rating_star_big_generic ynvideo_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="album_rate(5);"<?php endif; ?> onmouseover="album_rating_over(5);"></span>
				    <span id="rating_text" class="rating_text ynvideo_rating_text"><?php echo $this->translate('click to rate'); ?></span>
				</div>
			<?php if( $this->mine || $this->canEdit ): ?>
			  <script type="text/javascript">
			    var SortablesInstance;
			    en4.core.runonce.add(function() {
			      $$('.thumbs_nocaptions > li').addClass('sortable');
			      SortablesInstance = new Sortables($$('.thumbs_nocaptions'), {
			        clone: true,
			        constrain: true,
			        //handle: 'span',
			        onComplete: function(e) {
			          var ids = [];
			          $$('.thumbs_nocaptions > li').each(function(el) {
			            ids.push(el.get('id').match(/\d+/)[0]);
			          });
			          //console.log(ids);
			
			          // Send request
			          var url = '<?php echo $this->url(array('action' => 'order')) ?>';
			          var request = new Request.JSON({
			            'url' : url,
			            'data' : {
			              format : 'json',
			              order : ids
			            }
			          });
			          request.send();
			        }
			      });
			    });
			
			  </script>
				<?php endif ?>

				<div class="album_options">
					<?php if ( !$this->album->virtual) :?>
	    				<?php if ($this->mine || $this->can_edit|| $this->can_add_photo): ?>
	    					<?php echo $this->htmlLink(array('route' => 'album_general', 'action' => 'upload', 'album_id' => $this->album->album_id), $this->translate('Add More Photos'), array(
	    							'class' => 'buttonlink icon_photos_new'
	                        )) ?>
						<?php endif;?>
						<?php if ($this->mine || $this->can_edit): ?>
							              
	    					<?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'editphotos', 'album_id' => $this->album->album_id), $this->translate('Manage Photos'), array(
	    							'class' => 'buttonlink icon_photos_manage'
	                        )) ?>
	
	    					<?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'edit', 'album_id' => $this->album->album_id), $this->translate('Edit Settings'), array(
	    							'class' => 'buttonlink icon_photos_settings'
	                        )) ?>
	    					<?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'delete', 'album_id' => $this->album->album_id, 'format' => 'smoothbox'), $this->translate('Delete Album'), array(
	    							'class' => 'buttonlink smoothbox icon_photos_delete'
	                        )) ?>
					    <?php endif;?>
					<?php else:?>
						<?php if ($this->mine || $this->can_edit): ?>
														              	    				
	    					<?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'edit', 'album_id' => $this->album->album_id), $this->translate('Edit Settings'), array(
	    							'class' => 'buttonlink icon_photos_settings'
	                        )) ?>
	    					<?php echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'delete', 'album_id' => $this->album->album_id, 'format' => 'smoothbox'), $this->translate('Delete Album'), array(
	    							'class' => 'buttonlink smoothbox icon_photos_delete'
	                        )) ?>
					    <?php endif;?>
					<?php endif;?>
					<?php
					if (count($this->paginator) > 0) {
	                    echo $this->htmlLink(array('route' => 'album_specific', 'action' => 'download', 'album_id' => $this->album->album_id), $this->translate('Download Album'), array(
	                          'class' => 'buttonlink icon_photos_download'
	                    ));
	                }
	                ?>
				</div>

				<?php if (""!=$this->album->getDescription()): ?>
				<p style="text-align: justify">
					<?php echo $this->album->getDescription() ?>
				</p>
				<br />
				<?php endif ?>
				
                <div class='advalbum_albumview_photos ym_view_list_photo'>
                <?php if($session -> mobile){ ?>
					<a href="javascript:void(0)" class="slideshow_button toggleSlideshowMobile" id='advalbum_slideshow'><?php echo $this->translate('Slide Show');?></a>
					<div class="ymb_photo_list">
						<?php echo $this->html_photo_list; ?>
					</div>
					<div class="ymb_mobile_slideshow">
						<?php echo $this->html_mobile_slideshow; ?>
	                </div>
	            <?php }else{ ?>
	            	<div class="ymb_photo_list">
						<?php echo $this->html_photo_list; ?>
					</div>
	            <?php } ?>
                </div>

				<?php if(count($this->paginator)>1): ?>
				<br />
                    <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
                        array(
                        'pageAsQuery' => false,
                        'query' => $this->formValues
                    )); ?>
                <?php endif; ?>
				<br />
				<div class="album_summary">
					<b><?php echo $this->translate('Album Statistics');?> </b>
				</div>
				<div class="album_summary_info">
					<?php echo $this->translate('Photos: ');?>
					<b><?php echo $this->album->count();?> </b> -
					<?php echo $this->translate('Views:');?>
					<b><?php echo $this->album->view_count;?> </b> -
					<?php echo $this->translate('Comments:');?>
					<b><?php echo $this->album->comment_count;?> </b>
				</div>
			</form>
			<br />
			<!-- AddThis Smart Layers BEGIN -->
			<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
			<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e4e2c280039ea82"></script>
			<script type="text/javascript">
			  addthis.layers({
			    'theme' : 'transparent',
			    'share' : {
			      'position' : 'left',
			      'numPreferredServices' : 5
			    },
			  });
			</script>
			<!-- AddThis Smart Layers END -->
			<br />
		</div>
		<!-- END MIDDLE CONTENT -->
	</div>
</div>
<?php
if($session -> mobile){
$this->headScript()
	->appendFile($this->baseUrl() . '/application/modules/Advalbum/externals/scripts/jquery-1.7.1.min.js')
	->appendFile($this->baseUrl() . '/application/modules/Advalbum/externals/slideshow/responsiveslides.min.js');
$this->headLink()
	->appendStylesheet($this->baseUrl() . '/application/modules/Advalbum/externals/slideshow/responsiveslides.css');
?>
<script type="text/javascript">
    jQuery.noConflict();
	jQuery(function () {
		 jQuery("#ymb_home_featuredphoto").responsiveSlides({
	        speed: 800
	      });
		jQuery('.toggleSlideshowMobile').click(function(){
			if(jQuery(this).parent().hasClass('ym_view_list_photo')){
				jQuery(this).parent().removeClass('ym_view_list_photo');
				jQuery(this).parent().addClass('ym_view_slideshow_photo');
				jQuery(this).text('<?php echo $this->translate('List View');?>');
			}else{
				jQuery(this).parent().addClass('ym_view_list_photo');
				jQuery(this).parent().removeClass('ym_view_slideshow_photo');
				jQuery(this).text('<?php echo $this->translate('Slide Show');?>');
			}
			
		});
	});
	
</script>

<?php } ?>