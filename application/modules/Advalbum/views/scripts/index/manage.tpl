<script>
window.addEvent('domready', function(){
	
	if($('filter_form'))
	{
		$('filter_form').set('action','');
	}
	
	addEventAddToAlbum();
	// add event for button Add to of Adv.Album photo
	function addEventAddToAlbum() {
		$(document.body).addEvent('click', function(event){
			 var target = event.target;
             // if the user click outside the add to menu box, remove the add to menu box
             if (!target.contains($('ynadvalbum_addTo_menu_list'))){
            	 if($('ynadvalbum_addTo_menu_list')) {
               		$('ynadvalbum_addTo_menu_list').destroy();
            	 }
             }
		});
		$$('button.ynadvalbum_add_button').each(function(el){
			el.addEvent('click', function(e){
				e.stop();
				//e.preventDefault();
				if ($('ynadvalbum_addTo_menu_list')) {
					$('ynadvalbum_addTo_menu_list').destroy();
				}

				var album_id = el.get('album-id');
				//var parent_offset = el.getOffsetParent().getCoordinates();

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
			        data: { 'album_id' : album_id },
			     	onComplete: function (respone){
			     		//el.innerHTML = respone;
			     		$('ynadvalbum_addTo_menu_list').innerHTML = respone;
			     		$$('#ynadvalbum_delete_album .smoothbox').each(function(element){
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
$shortenLength = 20;
?>
<div>
  <ul class="thumbs thumbs_album">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
     <?php
      foreach( $this->paginator as $album ):
     	$album_title_full = trim($album->getTitle());
		$album_title_tooltip = "";
		$album_title = Advalbum_Api_Core::shortenText($album_title_full, $shortenLength);
		$album_title_tooltip = Advalbum_Api_Core::defaultTooltipText($album_title_full);
	 ?> 
      <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='advalbum_albumview_photos'>
		<a class="thumbs_photo" href="<?php echo $album->getHref();?>">
			<span style="width:120px;height:90px;padding:0;margin:0;background-image: url(<?php echo $album->getPhotoUrl('thumb.profile'); ?>);">
				<span class="ynadvalbum_button_add_to_area_album">
		             <button class="ynadvalbum_uix_button ynadvalbum_add_button" album-id='<?php echo $album->getIdentity() ?>' >
		                    <div class="ynadvalbum_plus"></div>
		             </button>
		        </span>
			</span>
		</a>
		<p class="thumbs_info">
            <span class="thumbs_title" style="white-space:nowrap;">
            <?php echo $this->htmlLink($album->getHref(), $album_title, array('title' => $album_title_tooltip)); ?>
            </span>
            <?php
				$photos_count = $album->count();
				if ($photos_count > 1) {
					$str_photos = $this->translate('%1$s photos', $photos_count);
				} else {
				    $str_photos = $this->translate('%1$s photo', $photos_count);
				}
				if ($album->view_count > 1) {
					$str_views = $this->translate('%1$d views', $album->view_count);
				} else {
				    $str_views = $this->translate('%1$d view', $album->view_count);
				}
				if ($album->comment_count > 1) {
					$str_comments = $this->translate('%1$d comments', $album->comment_count);
				} else {
				    $str_comments = $this->translate('%1$d comment', $album->comment_count);
				}

				$album_info = $this->translate('%1$s<br/> %2$s, %3$s', $str_photos, $str_views, $str_comments);

				echo $album_info;
				// rating
				echo $this->partial('_rating_big.tpl', 'advalbum', array('subject' => $album));
			?>
		</p>
	  </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
            array(
            'pageAsQuery' => false,
            'query' => $this->formValues
        )); ?>
<?php endif; ?>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any albums yet.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$screating%2$s your first album!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
</div>