<script>
window.addEvent('domready', function()
{
	addEventAddTo();
	// add event for button Add to of Adv.Album photo
	function addEventAddTo() {
		$(document.body).addEvent('click', function(event){
			 var target = event.target;
             // if the user click outside the add to menu box, remove the add to menu box
			 if ($('ynadvalbum_addTo_menu_list')) {
                 if (!$('ynadvalbum_addTo_menu_list').contains(target)){
                	 if ($('ynadvalbum_addTo_menu_list')) {
                   		$('ynadvalbum_addTo_menu_list').destroy();
                	 }
                 }
                 else {
             	    if (target.get('id') == 'ynadvalbum_addTo_downloadresizephoto' || target.get('id') == 'ynadvalbum_addTo_downloadfullphoto') {
             	    	if ($('ynadvalbum_addTo_menu_list')) {
                       		$('ynadvalbum_addTo_menu_list').setStyle('display', 'none');
                    	 }
                 	}
                 }
			 }
		});
		$$('button.ynadvalbum_add_button').each(function(el){
			el.addEvent('click', function(e){
				e.stop();
				if ($('ynadvalbum_addTo_menu_list')) {
					$('ynadvalbum_addTo_menu_list').destroy();
				}

				var photo_id = el.get('photo-id');
				var album_id = el.get('album-id');

				var advalbum_addTo_menu_list  = new Element("div", {
					'id': "ynadvalbum_addTo_menu_list"
				});
				var advalbum_addTo_frame_loading  = new Element("div", {
					'id': "ynadvalbum_addTo_frame_loading",
					'class': 'ynadvalbum_addTo_frame'
				});
				var advalbum_addTo_loading  = new Element("div", {
					'id': "ynadvalbum_addTo_loading"
				});
				advalbum_addTo_frame_loading.adopt(advalbum_addTo_loading);
				advalbum_addTo_menu_list.adopt(advalbum_addTo_frame_loading);
				$(document.body).adopt(advalbum_addTo_menu_list);

				var position = el.getPosition();
				$('ynadvalbum_addTo_menu_list').setPosition({x: position.x, y: position.y + el.getHeight()});

			     var makeRequest = new Request({
			     	url: '<?php echo $this->url(array('action' => 'add-to'), 'album_extended', true) ?>',
			        data: { 'photo_id' : photo_id, 'album_id' : album_id },
			     	onComplete: function (respone){
			     		$('ynadvalbum_addTo_menu_list').innerHTML = respone;
			     		$$('#ynadvalbum_addTo_list .smoothbox').each(function(element){
			     			element.addEvent('click', function(event){
								event.stop();
								Smoothbox.open(this);
								$('ynadvalbum_addTo_menu_list').destroy();
							});
					    });
			     	}
			     }).send();

			});
		});
	}
});
</script>

<?php
$session = new Zend_Session_Namespace('mobile');
$params = array(); 
if($this -> album)
{
	$params = array('album_virtual' => $this -> album -> getIdentity());	
}
$photo_list = array();
if (isset($this->arr_photos)) {
	$photo_list = $this->arr_photos;
} else if (isset($this->paginator)) {
	$photo_list = $this->paginator;
}
if (count($photo_list)<=0) { // no photos
	if (isset($this->no_photos_message) && $this->no_photos_message) {
?>
<div class="tip">
      <span><?php echo $this->no_photos_message;?></span>
</div>
<?php
	}
	return;
}

$photo_listing_id = "";
if (isset($this->photo_listing_id)) {
	$photo_listing_id = trim($this->photo_listing_id);
}
if (!$photo_listing_id)  $photo_listing_id = 'photo_listing_' . date("Ymdhis");

$css_main = "";
if ($this->css) {
	$css_main = "class='{$this->css}'";
}

$sortable_css = "class = 'advalbum_albums_gird_thumb swiper-slide";
if ($this->sortable) {
	$sortable_css .= " sortable'";
}
else
{
	$sortable_css .= "'";
}

$shortenLength = Advalbum_Api_Core::SHORTEN_LENGTH_DEFAULT;

$bShowTitle = FALSE;
if (isset($this->show_title_info) && $this->show_title_info) {
	$bShowTitle = TRUE;
}
if (!$bShowTitle) { ?>
<style>

</style>
<?php
}
?>
<div class="adv-album-view-mode">
<div <?php echo $css_main;?> id="<?php echo $photo_listing_id; ?>">
	<?php if(!$session -> mobile):?>
		<div class="ynalbum-listing-tab">
			<?php if(in_array('grid', $this -> mode_enabled)):?>
		    <div title="Grid view" class="grid-view <?php if($this -> view_mode == 'list') echo 'active';?>" data-view="ynalbum-grid-view"></div>
		    <?php endif;?>	
		    <?php if(in_array('pinterest', $this -> mode_enabled)):?>   
		   	<div title="Pinterest view" class="pinterest-view <?php if($this -> view_mode == 'pinterest') echo 'active';?>" data-view="ynalbum-pinterest-view"></div>
		   	<?php endif;?>
		</div>
	<?php endif;?>

<div class="photo-list-content ynalbum-grid-view" >

<div class="photo-grid-view <?php if($session->mobile) echo "ymb_thumb_slide"?>">
  <ul class="ynalbum-grid-list gallery<?php echo $this->rand; ?> clearfix swiper-wrapper">
 <?php $index = 0;
  $thumb_photo = '';
	foreach($photo_list as $photo ):
		$photo_title_full = trim($photo->getTitle());
		// photo title
		$photo_title_tooltip = Advalbum_Api_Core::defaultTooltipText($photo_title_full);
		if ($bShowTitle) {
			$photo_title = Advalbum_Api_Core::shortenText($photo_title_full, $shortenLength);
		}
		$strAuthor = "";
		if (isset($this->no_author_info) && $this->no_author_info) {

		} else {
			$album = $photo->getParent();
			$album_title_full = $album->getTitle();
			$album_title = Advalbum_Api_Core::shortenText($album_title_full, 20);
			$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
			$album_owner = $album->getOwner();
			$album_owner_title_full = $album_owner->getTitle();
			$album_owner_title = Advalbum_Api_Core::shortenText($album_owner_title_full, 20);
			$album_owner_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_owner_title_full);
			$strAuthor = $this->translate('<div class="thumbs_info_grid_name"><span>By %1$s </span><span>&nbsp;in %2$s</span></div>', $this->htmlLink($album_owner, $album_owner_title, array('title' => $album_owner_title_tooltip)), $this->htmlLink($album, $album_title, array('title' => $album_title_tooltip)));
		}

		if ($photo->view_count>1) {
			$str_views = $this->translate('%1$d views', $photo->view_count);
		} else {
			$str_views = $this->translate('%1$d view', $photo->view_count);
		}
		if ($photo->comment_count>1) {
			$str_comments = $this->translate('%1$d comments', $photo->comment_count);
		} else {
			$str_comments = $this->translate('%1$d comment', $photo->comment_count);
		}
	 ?>
      <li <?php echo $sortable_css;?> id="thumbs-photo-<?php echo $photo->photo_id ?>">
		<a class="thumbs_photo_grid photo <?php if(!$session->mobile){?> advalbum_smoothbox <?php } ?>" rel="" href="<?php echo $photo->getHref($params) ?>" <?php echo "title=\"$photo_title_tooltip\"";?>>
			<span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);">
					<div class="ynadvalbum_button_add_to_area">
                        <button class="ynadvalbum_uix_button ynadvalbum_add_button" photo-id="<?php echo $photo->getIdentity()?>" album-id='<?php echo (isset($this->album)) ? ($this->album->getIdentity()) : ($photo->getParent()->getIdentity()); ?>' >
                            <div title="<?php echo $this -> translate("Options")?>" class="ynadvalbum_plus"></div>
                        </button>
                    </div>
			</span>
		</a>
		<div class="thumbs_info_grid">
			<?php if ($bShowTitle && $photo_title) { ?>

            <span class="thumbs_title"><?php echo $this->htmlLink($photo, $photo_title, array('title' => $photo_title_tooltip, 'class' => 'thumbs_photo_link')); ?></span>
			<?php } ?>
            <?php if ($strAuthor) echo "$strAuthor"; ?>

			<?php echo "<div>$str_views, $str_comments</div>"; ?>
			<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $photo)); ?>
		</div>
	  </li>
    <?php $index ++; endforeach;?>
  </ul>
</div>
  <ul id="<?php echo $photo_listing_id; ?>_tiles" class="photo-pinterest-view gallery<?php echo $this->rand; ?> clearfix">
 <?php $index = 0;
  $thumb_photo = 'thumb.normal';
	if(defined('YNRESPONSIVE'))
	{
		$thumb_photo = 'thumb.profile';
	}
	foreach($photo_list as $photo ):
		$photo_title_full = trim($photo->getTitle());
		// photo title
		$photo_title_tooltip = Advalbum_Api_Core::defaultTooltipText($photo_title_full);
		if ($bShowTitle) {
			$photo_title = Advalbum_Api_Core::shortenText($photo_title_full, $shortenLength);
		}

		$strAuthor = "";
		if (isset($this->no_author_info) && $this->no_author_info) {

		} else {
			$album = $photo->getParent();
			$album_title_full = $album->getTitle();
			$album_title = Advalbum_Api_Core::shortenText($album_title_full, 20);
			$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);

			$album_owner = $album->getOwner();
			$album_owner_title_full = $album_owner->getTitle();
			$album_owner_title = Advalbum_Api_Core::shortenText($album_owner_title_full, 20);
			$album_owner_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_owner_title_full);

			$strAuthor = $this->translate('By %1$s in %2$s', $this->htmlLink($album_owner, $album_owner_title, array('title' => $album_owner_title_tooltip)), $this->htmlLink($album, $album_title, array('title' => $album_title_tooltip)));
		}

		if ($photo->view_count>1) {
			$str_views = $this->translate('%1$d views', $photo->view_count);
		} else {
			$str_views = $this->translate('%1$d view', $photo->view_count);
		}
		if ($photo->comment_count>1) {
			$str_comments = $this->translate('%1$d comments', $photo->comment_count);
		} else {
			$str_comments = $this->translate('%1$d comment', $photo->comment_count);
		}
	 ?>
      <li <?php echo $sortable_css;?> id="thumbs-photo-<?php echo $photo->photo_id ?>" class="swiper-slide ">
		<a class="thumbs_photo photo <?php if(!$session->mobile){?> advalbum_smoothbox <?php } ?>" rel="" href="<?php echo $photo->getHref($params) ?>" <?php echo "title=\"$photo_title_tooltip\"";?>>
			<img class="pinterest-thumb"src="<?php echo $photo->getPhotoUrl(); ?>" />
            <span class="ynadvalbum_button_add_to_area">
                <button class="ynadvalbum_uix_button ynadvalbum_add_button" photo-id="<?php echo $photo->getIdentity()?>" album-id='<?php echo (isset($this->album)) ? ($this->album->getIdentity()) : ($photo->getParent()->getIdentity()); ?>' >
                    <div title="<?php echo $this -> translate("Options")?>" class="ynadvalbum_plus"></div>
                </button>
            </span>
		</a>

		<div class="thumbs_info">
			<?php if ($bShowTitle && $photo_title) { ?>

            <span class="thumbs_title"><?php echo $this->htmlLink($photo, $photo_title, array('title' => $photo_title_tooltip, 'class' => 'thumbs_photo_link')); ?></span>
			<?php } ?>
            <?php if ($strAuthor) echo "$strAuthor<br/>"; ?>
			<?php echo "$str_views, $str_comments"; ?>
			<?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $photo)); ?>
		</div>
	  </li>
    <?php $index ++; endforeach;?>
  </ul>
 </div>
	<div style="clear:both"></div>
</div>
<?php if (isset($this->no_bottom_space) && $this->no_bottom_space) {
} else { ?>
<div style="margin-top:20px;"></div>
<?php } ?>
<?php if(!$session -> mobile):?>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advalbum/externals/scripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advalbum/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advalbum/externals/scripts/wookmark/jquery.wookmark.js"></script>
<script type="text/javascript">
    jQuery.noConflict();
    (function ($){
    	
          $('#<?php echo $photo_listing_id; ?>_tiles').imagesLoaded(function() {
            
            var options = {
              itemWidth: 215,
              autoResize: true,
              container: $('#<?php echo $photo_listing_id; ?>_tiles'),
              offset: 25,
              outerOffset: 0,
              flexibleWidth: '50%'
            };
    
            // Get a reference to your grid items.
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li');
    
            var $window = $(window);
            $window.resize(function() {
              var windowWidth = $window.width(),
                  newOptions = { flexibleWidth: '50%' };
    
              // Breakpoint
              if (windowWidth < 1024) {
                newOptions.flexibleWidth = '100%';
              }
    
              handler.wookmark(newOptions);
            });
            
            // Call the layout function.
            handler.wookmark(options);
         });
         
         $('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').click(function() {
            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li');
            var options = {
                  itemWidth: 215,
                  autoResize: true,
                  container: $('#<?php echo $photo_listing_id; ?>_tiles'),
                  offset: 25,
                  outerOffset: 0,
                  flexibleWidth: '50%'
            };
            
            // Breakpoint
            if ( $(window).width() < 1024) {
                options.flexibleWidth = '100%';
            }
                
            $('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
            $(this).addClass('active');
                                    
            $('#<?php echo $photo_listing_id; ?> .photo-list-content').attr('class', 'photo-list-content '+$(this).data('view') );
            
            if ( $(this).hasClass('grid-view') ) {
            	
                setCookie('<?php echo $photo_listing_id; ?>view_mode','grid');
            }  
           
            if ( $(this).hasClass('pinterest-view') ) {
                handler.wookmark(options);                
                setCookie('<?php echo $photo_listing_id; ?>view_mode','pinterest');
            }     
         });
         
    })(jQuery);
            
	window.addEvent('domready', function()
	{	
		en4.core.runonce.add(function()
		{
			var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');	
			$$('#main_tabs li.tab_layout_<?php echo $photo_listing_id; ?>').addEvent('click', function(){
				if(view_mode == 'pinterest')
				{	
					var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
		            var options = {
	                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
		                  offset: 25,
		                  outerOffset: 0,
		                  flexibleWidth: '50%'
		            };
		            
		            // Breakpoint
		            if ( jQuery(window).width() < 1024) {
		                options.flexibleWidth = '100%';
		            }
					handler.wookmark(options);
				}		
			});
		});
		if(getCookie('<?php echo $photo_listing_id; ?>view_mode') != "")
		{					
			var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');				
			$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynalbum-'+ view_mode +'-view');			
			$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
				
			if(view_mode == "grid" )
			{											
				$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.grid-view').addClass('active');
			}				
			if(view_mode == "pinterest" )
			{	
				$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.pinterest-view').addClass('active');
				var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
		            var options = {
		                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
		                  offset: 25,
		                  outerOffset: 0,
		                  flexibleWidth: '50%'
		            };
		            
		            // Breakpoint
		            if ( jQuery(window).width() < 1024) {
		                options.flexibleWidth = '100%';
		            }
					handler.wookmark(options);
			}							
		}
		else
		{						
			$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content '+'<?php echo $this->class_mode;?>');					
			$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
			if("<?php echo  $this->view_mode ?>" == "pinterest" )
			{	
				$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.pinterest-view').addClass('active');
				var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
		            var options = {
		                  itemWidth: 215,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
		                  offset: 25,
		                  outerOffset: 0,
		                  flexibleWidth: '50%'
		            };
		            
		            // Breakpoint
		            if ( jQuery(window).width() < 1024) {
		                options.flexibleWidth = '100%';
		            }
					handler.wookmark(options);
			}				
		
		}				
	});
	
	function setCookie(cname,cvalue,exdays)
    {
		var d = new Date();
		d.setTime(d.getTime()+(exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}
	
	function getCookie(cname)
	{
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) 
		{
			var c = ca[i].trim();
			if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		}
		return "";
	}
</script>
<?php endif;?>
</div>