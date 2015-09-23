<?php
$session = new Zend_Session_Namespace('mobile');
$album_list = array();
$album_count = 0;
if (isset($this->arr_albums)) {
	$album_list = $this->arr_albums;
	$album_count = count($album_list);
} else if (isset($this->paginator)) {
	$album_list = $this->paginator;
	$album_count = $this->paginator->getTotalItemCount();
}
if ($album_count<=0) { // no photos
	if (isset($this->no_albums_message) && $this->no_albums_message) {
?>
<div class="tip">
      <span><?php echo $this->no_albums_message;?></span>
</div>
<?php
	}
	return;
}

$album_listing_id = "";
if (isset($this->album_listing_id)) {
	$album_listing_id = trim($this->album_listing_id);
}
if (!$album_listing_id)  $album_listing_id = 'album_listing_' . date("Ymdhis");

$css_main = "";
if ($this->css) {
	$css_main = "{$this->css}";
}

if (isset($this->no_author_info) && $this->no_author_info) {
?>
<style>

</style>
<?php
}
$shortenLength = 20;
?>
<div class="adv-album-view-mode">
<div class ="<?php echo $css_main;?>" id="<?php echo $album_listing_id; ?>">
	<?php if(!$session -> mobile):?>
	<div class="ynalbum-listing-tab">
		<?php if(in_array('list', $this -> mode_enabled)):?>
	    	<div title="<?php echo $this->translate('List view');?>" class="list-view <?php if($this -> view_mode == 'list') echo 'active';?>" data-view="ynalbum-list-view"></div>
	    <?php endif;?>	
	    <?php if(in_array('grid', $this -> mode_enabled)):?>   
	    	<div title="<?php echo $this->translate('Grid view');?>" class="grid-view <?php if($this -> view_mode =='grid') echo 'active';?>" data-view="ynalbum-grid-view"></div>
	    <?php endif;?>
	    <?php if(in_array('pinterest', $this -> mode_enabled)):?>   
	    	<div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view <?php if($this -> view_mode == 'pinterest') echo 'active';?>" data-view="ynalbum-pinterest-view"></div>
		 <?php endif;?>
	</div>
	<?php endif;?>
	<div id="<?php echo $album_listing_id; ?>" class="album-listing-view-mode <?php echo $css_main;?>">
		<div id="<?php echo $album_listing_id; ?>_view" class="<?php if ($this -> class_mode) echo $this -> class_mode; else echo 'ynalbum-list-view'; ?>">
		    <ul class="ynalbum-list">
		    <?php
		        $thumb_photo = 'thumb.normal';
		    	if(defined('YNRESPONSIVE'))
		    	{
		    		$thumb_photo = 'thumb.profile';
		    	}
		    	foreach($album_list as $album ):
				$album_title_full = trim($album->getTitle());
				$album_title_tooltip = "";
				if (isset($this->short_title) && $this->short_title) {
					$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
					$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
				} else {
					$album_title = $album_title_full;
				}
			 ?>
		      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='advalbum_albums_thumb'>
		        <a href="<?php echo $album->getHref(); ?>" class="ynalbum-thumb">
		            <span class="span-image" style="background-image:url(<?php echo $album->getPhotoUrl(); ?>);"></span>
		        </a>
		        <div class="ynalbum-content">			
		            <span class="thumbs_title" style="white-space:nowrap;"><a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album_title; ?></a></span>
		            <span class="ynalbum-rating"><?php echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album)); ?></span>
		            <div class="thumbs_info">
		            <?php
		            	// end photo album
						$photos_count = $album->count();
						if ($photos_count>1) {
							$str_photos = $this->translate('%1$s photos', $photos_count);
						} else {
						    $str_photos = $this->translate('%1$s photo', $photos_count);
						}
						if ($album->view_count>1) {
							$str_views = $this->translate('%1$d views', $album->view_count);
						} else {
						    $str_views = $this->translate('%1$d view', $album->view_count);
						}
						if ($album->comment_count>1) {
							$str_comments = $this->translate('%1$d comments', $album->comment_count);
						} else {
						    $str_comments = $this->translate('%1$d comment', $album->comment_count);
						}
						if (isset($this->no_author_info) && $this->no_author_info) {
							$album_info_1 = $this->translate('%1$s', $str_photos);
							$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
						} else {
							$album_info_1 = $this->translate('%2$s by %1$s', $album->getOwner()->__toString(), $str_photos);
							$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
						}
						echo "<div class='album-info'><span class='advalbum_list_photos'>" . $album_info_1 . "</span><span>" . $album_info_2 . "</span></div>";
		                
                        echo "<div class='album-photo-lists'>";
		                //photo album
		                if($album->virtual){
			        		$photo_list = $album->getVirtualPhotos();
						}
						else {
							$photo_list = $album->getAlbumPhotos();
						}
			        	foreach ($photo_list as $photo)
						{
							echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
						}
                        echo "</div>";
				      ?>
		            </div>
		        </div>            
			  </li>
		    <?php endforeach;?>
		    </ul>
		    
		    <ul class="ynalbum-grid-list">
		    <?php
		        $thumb_photo = '';

		    	foreach($album_list as $album ):
				$album_title_full = trim($album->getTitle());
				$album_title_tooltip = "";
				if (isset($this->short_title) && $this->short_title) {
					$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
					$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
				} else {
					$album_title = $album_title_full;
				}
				
			 ?>
		      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='advalbum_albums_gird_thumb'>
				
		        <div class="thumbs_title_grid">
		        		<a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album_title; ?></a>
		        </div>

		        <div class="thumbs_photo_grid">
			        <span style="background-image: url(<?php echo $album->getPhotoUrl($thumb_photo); ?>);"></span>
		        </div>

		        <div class="thumbs_info_grid">
		        <?php
					// end photo album
					$photos_count = $album->count();
					if ($photos_count>1) {
						$str_photos = $this->translate('%1$s photos', $photos_count);
					} else {
					    $str_photos = $this->translate('%1$s photo', $photos_count);
					}
					if ($album->view_count>1) {
						$str_views = $this->translate('%1$d views', $album->view_count);
					} else {
					    $str_views = $this->translate('%1$d view', $album->view_count);
					}
					if ($album->comment_count>1) {
						$str_comments = $this->translate('%1$d comments', $album->comment_count);
					} else {
					    $str_comments = $this->translate('%1$d comment', $album->comment_count);
					}
					if (isset($this->no_author_info) && $this->no_author_info) {
						$album_info_1 = $this->translate('%1$s', $str_photos);
						$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
					} else {
						$album_info_1 = $this->translate('%2$s by %1$s', $album->getOwner()->__toString(), $str_photos);
						$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
					}
					echo "<span class='advalbum_list_photos'>" . $album_info_1 . "</span><span>" . $album_info_2 . "</span>";
					// rating
					echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album));
				?>
		        </div>
			  </li>

		    <?php endforeach;?>
		    </ul>
		    
		    <ul id="<?php echo $album_listing_id; ?>_tiles" class="ynalbum-pinterest-list">
			     <?php
			      $thumb_photo = 'thumb.normal';
				if(defined('YNRESPONSIVE'))
				{
					$thumb_photo = 'thumb.profile';
				}
				foreach($album_list as $album ):
					$album_title_full = trim($album->getTitle());
					$album_title_tooltip = "";
					if (isset($this->short_title) && $this->short_title) {
						$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
						$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
					} else {
						$album_title = $album_title_full;
					}
				 ?>
			      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='advalbum_albums_thumb'>
			        <div class="thumbs_title"><a href="<?php echo $album->getHref(); ?>" title="<?php echo $album_title_tooltip;?>"><?php echo $album_title; ?></a></div>
			        <div class="thumbs_content">
			            <div class="thumbs_image">
			                <img src="<?php echo $album->getPhotoUrl(); ?>" alt=""/>
			                <div class="pinterest-thumb-inner">
			                <?php
			                    //photo album
			    	        	if($album->virtual){
				        		$photo_list = $album->getVirtualPhotos();
								}
								else {
									$photo_list = $album->getAlbumPhotos();
								}
								$count = 0;
			    	        	foreach ($photo_list as $photo)
			    				{
			    					$count ++;
									if($count >= 3)
									{
										echo '<a class="album-photo-list" href="'.$album->getHref().'"><span style="background-image: url(application/modules/Advalbum/externals/images/pinterest-view-more.png);">'.$this->translate("View all").'</span></a>';
										break;
									}
									else 
									{
										echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
									}
			    				}
			                ?>
			                </div>
			            </div>
			            <div class="thumbs_info clearfix">
			                <div class="album-author-image">
			                    <?php
			                        $user = $album->getOwner();
			                        echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')); 
			                    ?>
			                </div>
			                <div class="album-content">
			                <?php
			    				// rating
			                    echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album));
			                    
			                    // end photo album     
			    				$photos_count = $album->count();
			    				if ($photos_count>1) {
			    					$str_photos = $this->translate('%1$s photos', $photos_count);
			    				} else {
			    				    $str_photos = $this->translate('%1$s photo', $photos_count);
			    				}
			    				if ($album->view_count>1) {
			    					$str_views = $this->translate('%1$d views', $album->view_count);
			    				} else {
			    				    $str_views = $this->translate('%1$d view', $album->view_count);
			    				}
			    				if ($album->comment_count>1) {
			    					$str_comments = $this->translate('%1$d comments', $album->comment_count);
			    				} else {
			    				    $str_comments = $this->translate('%1$d comment', $album->comment_count);
			    				}
			    				if (isset($this->no_author_info) && $this->no_author_info) {
			    					$album_info_1 = $this->translate('%1$s', $str_photos);
			    					$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
			    				} else {
			    					$album_info_1 = $this->translate('%2$s by %1$s', $album->getOwner()->__toString(), $str_photos);
			    					$album_info_2 = $this->translate('%1$s, %2$s', $str_views, $str_comments);
			    				}
			                    echo "<span class='advalbum_list_photos'>" . $album_info_1 . "</span>";
			    			?>
			                </div>
			            </div>
			        </div>
			        <div class="thumbs_stats">
			            <span class="album-stat-icon-like"></span> <?php echo $album->like_count; ?>
			            <span class="album-stat-icon-comment"></span> <?php echo $album->comment_count; ?>
			        </div>            
				  </li>
			    <?php endforeach;?>
		  </ul>
		</div>
	</div>
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
          $('#<?php echo $album_listing_id; ?>_tiles').imagesLoaded(function() {
            
            var options = {
              itemWidth: 220,
              autoResize: true,
              container: $('#<?php echo $album_listing_id; ?>_tiles'),
              offset: 25,
              outerOffset: 0,
              flexibleWidth: '50%'
            };
    
            // Get a reference to your grid items.
            var handler = $('#<?php echo $album_listing_id; ?>_tiles li');
    
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
         
         $('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div').click(function() {
            var handler = $('#<?php echo $album_listing_id; ?>_tiles li');
            var options = {
                  itemWidth: 220,
                  autoResize: true,
                  container: $('#<?php echo $album_listing_id; ?>_tiles'),
                  offset: 25,
                  outerOffset: 0,
                  flexibleWidth: '50%'
            };
            
            // Breakpoint
            if ( $(window).width() < 1024) {
                options.flexibleWidth = '100%';
            }
                
            $('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
            $(this).addClass('active');
                                    
            $('#<?php echo $album_listing_id; ?>_view').attr('class', $(this).data('view') );
            
            if ( $(this).hasClass('list-view') ) {
                setCookie('<?php echo $album_listing_id; ?>view_mode','list');
            }  
            if ( $(this).hasClass('grid-view') ) {
                  setCookie('<?php echo $album_listing_id; ?>view_mode','grid');
            }  
            if ( $(this).hasClass('pinterest-view') ) {
                handler.wookmark(options);    
                setCookie('<?php echo $album_listing_id; ?>view_mode','pinterest');
            }    
         });
         
    })(jQuery);
	window.addEvent('domready', function()
	{
		var view_mode = "";
		en4.core.runonce.add(function()
		{
			var view_mode  = getCookie('<?php echo $album_listing_id; ?>view_mode');	
			$$('#main_tabs li.tab_layout_<?php echo $album_listing_id; ?>').addEvent('click', function(){
				if(view_mode == 'pinterest')
				{	
					var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li');
		            var options = {
		                  itemWidth: 220,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
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
		if(getCookie('<?php echo $album_listing_id; ?>view_mode')!= "")
		{
			view_mode = getCookie('<?php echo $album_listing_id; ?>view_mode');
			document.getElementById('<?php echo $album_listing_id; ?>_view').set('class',"ynalbum-"+getCookie('<?php echo $album_listing_id; ?>view_mode')+"-view");
			
			$$('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
			if(view_mode == "list" )
			{				
				$$('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div.list-view').addClass('active');
			}	
			if(view_mode == "grid" )
			{
				$$('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div.grid-view').addClass('active');
			}	
			if(view_mode == "pinterest" )
			{
				$$('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div.pinterest-view').addClass('active');
				var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li');
		            var options = {
		                  itemWidth: 220,
		                  autoResize: true,
		                  container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
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
			document.getElementById('<?php echo $album_listing_id; ?>_view').set('class', "<?php echo $this -> class_mode;?>");
			$$('#<?php echo $album_listing_id; ?> .ynalbum-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
			if("<?php echo  $this->view_mode ?>" == "pinterest" )
			{	
				var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li');
	            var options = {
	                  itemWidth: 220,
	                  autoResize: true,
	                  container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
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
		if(view_mode == "pinterest" )
		{
			var tab_element = document.getElementsByClassName('tab_layout_<?php echo $album_listing_id; ?>');
			if(tab_element)
			{
				var class_name = '.tab_layout_<?php echo $album_listing_id; ?>';
				//console.log(class_name);
				$$(class_name).addEvent('click', function(event){
					var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li');
			            var options = {
			                  itemWidth: 220,
			                  autoResize: true,
			                  container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
			                  offset: 25,
			                  outerOffset: 0,
			                  flexibleWidth: '50%'
			            };
			            
			            // Breakpoint
			            if ( jQuery(window).width() < 1024) {
			                options.flexibleWidth = '100%';
			            }
						handler.wookmark(options);
				});
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