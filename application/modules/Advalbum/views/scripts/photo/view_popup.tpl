<?php
$this->headScript()
	->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.js')
	->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.Crop.js')
	->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
	->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
	->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
	->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
	->appendFile($this->baseUrl() . '/externals/tagger/tagger.js')
	->appendFile($this->baseUrl() . '/application/modules/Advalbum/externals/scripts/tabcontent.js');

$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . '/application/modules/Advalbum/externals/styles/slideshow_popup.css');
?>

<script type="text/javascript">

window.addEvent('domready', function()
{
    	 if(window.parent.$('ynadvalbum_addTo_menu_list')) {

     		window.parent.$('ynadvalbum_addTo_menu_list').destroy();
    	 }
	  if (window.parent.Smoothbox.instance) {
		  window.parent.Smoothbox.instance.content.contentWindow.focus();
		  window.parent.Smoothbox.instance.doAutoResize = function(element){
			    element = $$('span#global_content_simple')[0];
			    var that = window.parent.Smoothbox.instance; 
			    if( !element || !that.options.autoResize )
			    {
			      return;
			    }

			    var size = Function.attempt(function(){
			      return element.getScrollSize();
			    }, function(){
			      return element.getSize();
			    }, function(){
			      return {
			        x : element.scrollWidth,
			        y : element.scrollHeight
			      }
			    });

			    var winSize = window.parent.getSize();
			    if( size.x - 70 > winSize.x ) size.x = winSize.x - 70;
			    if( size.y - 70 > winSize.y ) size.y = winSize.y - 70;

			    that.content.setStyles({
			      'width' : (size.x + 0) + 'px',
			      'height' : (size.y + 0) + 'px'
			    });

			    that.options.width = (size.x + 0);
			    that.options.height = (size.y + 0);

			    that.positionWindow();
		 }
	}
  });

  window.addEvent('domready', function(){
    	$('advalbum_arrow_next').addEvent('click', function(e) {
    		document.location.href = "<?php echo ($this->nextPhoto)?$this->nextPhoto->getHref(array('album_virtual' => $this -> album_virtual)) . '/format/smoothbox':'window.location.href' ?>";
    	});

    	$('advalbum_arrow_previous').addEvent('click', function(e){
    		window.location.href = "<?php echo ($this->previousPhoto)?$this->previousPhoto->getHref(array('album_virtual' => $this -> album_virtual)) . '/format/smoothbox':'window.location.href' ?>";
    	});
  });
  var taggerInstance;
  var movenext = 1;
  en4.core.runonce.add(function() {
    taggerInstance = new Tagger('advalbum_left_advalbum_viewLeft', {
      'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
      'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
      'createRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions' : {
        'container' : $('advalbum_left_advalbum_viewLeft')
      },
      'tagListElement' : 'media_tags',
      'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
      'suggestParam' : <?php echo ( $this->viewer()->getIdentity() ? $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) : 'null' ) ?>,
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
    });

	 // Remove the href attrib while tagging
    var nextHref = $('advalbum_left_advalbum_viewLeft_photo').get('href');
    taggerInstance.addEvents({
      'onBegin' : function() {
        $('advalbum_left_advalbum_viewLeft_photo').erase('href');
      },
      'onEnd' : function() {
        $('advalbum_left_advalbum_viewLeft_photo').set('href', nextHref);
      }
    });
  });

	window.addEvent('keyup', function(e) {
	    if( e.target.get('tag') == 'html' || e.target.get('tag') == 'body' ) {
	      	if( e.key == 'right' ) {
		        window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>" + '/format/smoothbox';
	        } else if( e.key == 'left' ) {
	      		window.location.href = "<?php echo ( $this->nextPhoto ? $this->nextPhoto->getHref() : 'window.location.href' ) ?>" + '/format/smoothbox';
	        }
	    }
    });

  // rating
  en4.core.runonce.add(function() {
      var pre_rate = <?php echo $this->photo->rating; ?>;
      var rated = '<?php echo $this->is_rated; ?>';
      var photo_id = <?php echo $this->photo->getIdentity(); ?>;
      var total_votes = <?php echo $this->rating_count; ?>;
      var viewer = <?php echo $this->viewer()->getIdentity(); ?>;

      var rating_over = window.rating_over = function(rating) {
          if( rated == 1 ) {
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

      var rating_out = window.rating_out = function() {
		  if (total_votes > 0 && total_votes <= 1) {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
		  }
          if (pre_rate != 0){
              set_rating();
          }
          else {
              for(var x=1; x<=5; x++) {
                  $('rate_'+x).set('class', 'advalbum_rating_star_big_generic advalbum_rating_star_big_disabled');
              }
          }
      }

      var set_rating = window.set_rating = function() {
          var rating = pre_rate;
          if (total_votes > 0 && total_votes <= 1) {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('rating')?>";
		  }
		  else {
              $('rating_text').innerHTML = total_votes + " <?php echo $this->translate('ratings')?>";
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

      var rate = window.rate = function(rating) {
          $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!'); ?>";
          for(var x=1; x<=5; x++) {
              $('rate_'+x).set('onclick', '');
          }
          (new Request.JSON({
              'format': 'json',
              'url' : '<?php echo $this->url(array('action' => 'rate', 'subject_id' => $this->photo->getIdentity()), 'album_extended', true) ?>',
              'data' : {
                  'format' : 'json',
                  'rating' : rating,
              },
              'onRequest' : function(){
                  rated = 1;
                  total_votes = total_votes + 1;
                  pre_rate = (pre_rate+rating)/total_votes;
                  set_rating();
              },
              'onSuccess' : function(responseJSON, responseText)
              {
              }
          })).send();
      }
      set_rating();
  });
</script>
<?php
    $album_title_full = trim($this->album->getTitle());
    //$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
    $album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
    if ($this->album->count()>1) {
        $strPhotos = $this->translate('%1$d photos', $this->album->count());
    } else {
        $strPhotos = $this->translate('%1$d photo', $this->album->count());
    }
    $tooltip_text = $this->translate('%1$s (%2$s)', $album_title_tooltip, $strPhotos);
    $album = $this->album;
 ?>
<div class="advalbum_view_photo_popup clearfix">
	<div class="advalbum_left advalbum_viewLeft" id ='advalbum_left_advalbum_viewLeft'>
		<div id="advalbum_view_photo">
		    <a href="javascript:void(0);" class="advalbum_popup_viewPhoto" id="advalbum_left_advalbum_viewLeft_photo">
			<?php
				echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array('id' => 'advalbum_view_photo_image'));
			?>
			</a>
		</div>
		<div class="advalbum_nav">
			<a class="advalbum_arrow_previous" href="javascript:void(0)" id='advalbum_arrow_previous'> Previous</a>
			<a class="advalbum_arrow_next" href="javascript:void(0)" id='advalbum_arrow_next'>Next</a>
		</div>
		<div class="advalbum_viewPhoto_container">
            
		    <div class='advalbum_viewPhoto_info'>
    		      
            	<div style=''>
            	<?php
        	    if (!empty($this->photo->taken_date) && Zend_Date::isDate($this->photo->taken_date, 'YYYY-MM-dd')) {
                    echo $this->photo->taken_date;
                    // echo $this->locale()->toDatetime($this->photo->taken_date);
                    if ($this->photo->location) {
                       echo ' - ';
                    }
                }
             	$href = "https://maps.google.com/?q=" . urlencode($this->photo->location);
             	$location = sprintf("<a href='javascript:void(0)' onclick='openLocation(%s)'  title='%s'>%s</a>", "\"{$href}\"", Advalbum_Api_Core::shortenText($this->photo->location, 70), $this->photo->location);

               if ($this->photo->location) {
              	 echo $location;
               }
           ?>
               </div>
           </div>
			<div class="albums_viewmedia_info_date clearfix">
				<div class="photo-thumbs-function">
				<?php if (!$this->message_view):?>
                    <div class="advalbum-photo-download">                    
                    <span></span>
                    <?php 
                        echo $this->htmlLink(array(
                                    'route' => 'album_photo_specific',
        							'action' => 'download-photo',
        							'album_id' => $this->photo->album_id,
        					        'photo_id' => $this->photo->getIdentity(),
                                ), $this->translate('Download'), array()); 
                     ?>
                    </div>
                    <div class="advalbum-photo-option">
                        <span></span>
                        <span class="option-links">Option</span>
                        <ul>
                            <li><?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'advalbum_photo', 'id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?></li>
                            <li><?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?></li>
    				        <li><?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?></li>
                            <li><a href="<?php echo $this->photo->getHref() ?>" target="_top"><?php echo $this->translate("View Detail"); ?></a></li>
                            <?php if($this->can_edit): ?>
            				<li>
            					<?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'left'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_left.png'> Rotate left",array('class' => 'smoothbox', 'title'=>$this->translate('rotate left')));?>
            				</li>
            				<li>
            					<?php echo $this->htmlLink(array('module' => 'advalbum', 'controller' => 'photo', 'action' => 'rotate','route' => 'default','album_id' => $this->photo->album_id, 'photo_id' => $this->photo->getIdentity(),'dest'=>'right'),"<img src='./application/modules/Advalbum/externals/images/photo_rotate_right.png'> Rotate right",array('class' => 'smoothbox', 'title'=>$this->translate('rotate right')));?>
            				</li>
            				<?php endif;?>
                        </ul>
                    </div>
				<?php endif;?>
                </div>
                
                <div class="thumbs_title">
                    <span class="photo-title"><?php echo $this->photo->getTitle(); ?></span>
					<span><?php echo $this->translate('in %1$s', $this->htmlLink($album, $album->getTitle(),array('target'=>'_top','title' => $album_title_tooltip)) ); ?></span>
				</div>
               
			</div>
		</div>
	</div>
	<div class="advalbum_right">
		<div onclick="parent.Smoothbox.close()" class='tclose advalbum_view_photo'></div>		 
		<ul class="thumbs thumbs_album_small">
			<li id="thumbs-photo-album-<?php echo $album->album_id ?>">
				<a class="thumbs_photo" href="<?php echo $album->getHref();?>">
					<span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
				</a>
				<div class="thumbs_info">
					<span class="thumbs_title">
						<?php echo $this->translate('%1$s\'s Album: %2$s', '<a target="_top" href="'.$album->getOwner()->getHref().'">'.$this -> string() -> truncate($album->getOwner()-> getTitle(), 20).'</a>', $this->htmlLink($album, $album->getTitle()),array('target'=>'_top','title' => $album_title_tooltip)); ?>
					</span>
                    
                    <span>
					<?php
						echo $this->translate('Album added time: ');
						echo $this->timestamp($album->creation_date);
					?>
                    </span>
                    
                    <span>
					<?php
						$photos_count = $album->count();
                        $likes_count = $album->like_count;

		                $str_views = $this->translate('Views: %1$d', $album->view_count);
		                $str_comments = $this->translate('Comments: %1$d', $album->comment_count);
                        $str_likes = $this->translate('Likes: %1$d', $likes_count);
		                		                
                        echo $str_views.'  ';
                        echo $str_comments.'  ';
                        echo $str_likes.'  ';                        
                	?>
                    </span>               
				</div>                                
			</li>
		</ul>
        
        <div class="popup-photo-view-rating clearfix">
            <?php if( $this->canTag ): ?>
                <div class="add-tag">
                    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick'=>'taggerInstance.begin();')) ?>
                </div>
            <?php endif; ?>
            
        	<div id="advalbum_rating" class="rating" onmouseout="rating_out();">
        		<span id="rate_1" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
        		<span id="rate_2" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
        		<span id="rate_3" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
        		<span id="rate_4" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
        		<span id="rate_5" class="rating_star_big_generic advalbum_rating_star_big_generic" <?php if (!$this->is_rated && $this->viewer()->getIdentity()): ?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
        	    <span id="rating_text" class="rating_text advalbum_rating_text"><?php echo $this->translate('click to rate'); ?></span>
        	</div>
    	</div>
        
        <div class="albums_viewmedia_info_tags" id="media_tags" style="display: none;  text-align: left; clear:both;">
           <?php echo $this->translate('Tagged:');?>
        </div>
                    
        <?php if ($this->photo->description): ?>
            <div class="photo-description"><?php echo $this->photo->description ?></div>
        <?php endif; ?>
        
        <?php
			echo $this->action("list", "comment", "core",
				array(
					"type"=>$this->photo->getType(),
					"id"=>$this->photo->getIdentity()
				)
			);
		?>
	</div>
</div>
<script type="text/javascript">
	function popupFixLinks() {
	    var popupArrLinks = document.links;
	    for (idxL=0; idxL<popupArrLinks.length;idxL++) {
	        var jsPos = popupArrLinks[idxL].href.indexOf('/profile/');
	        var jsView = popupArrLinks[idxL].href.indexOf('/view/');
	        if (jsPos>=0 || jsView >= 0) {
	            popupArrLinks[idxL].setAttribute("target","_top");
	        }
	    }
	}
	function cronFixLinks() {
	    popupFixLinks();
	    setTimeout("cronFixLinks()", 5000);
	}

	function do_onload() {
	    cronFixLinks();
	    if (parent.loading_complete) {
	        parent.loading_complete(<?php echo $this->photo->getIdentity(); ?>);
	    }
	}
	document.onload = do_onload();
</script>