
<h2>  
  <?php echo $this->event->__toString()." ".$this->translate("&#187; Photos") ?>
</h2>
<div class='generic_layout_container layout_middle ynevent_photo_list_container'>
  <div class="event_discussions_options">
	  	<?php 
	  	$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		  	echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity()), $this->translate('Back to Event'), array(
		    'class' => 'buttonlink icon_back'
		  ));
		else {
			echo $this->htmlLink(array('route' => 'event_profile', 'id' => $this->event->getIdentity(), 'tab' => $this -> tab), $this->translate('Back to Event'), array(
		    'class' => 'buttonlink icon_back'
		  ));
		} ?>
  	<?php if( $this->canUpload ): ?>
	    <?php echo $this->htmlLink(array(
	        'route' => 'event_extended',
	        'controller' => 'photo',
	        'action' => 'upload',
	        'subject' => $this->subject()->getGuid(),
	        'tab' => $this -> tab,
	      ), $this->translate('Upload Photos'), array(
	        'class' => 'buttonlink icon_event_photo_new'
	    )) ?>
	 <?php endif; ?>
  </div>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
  <ul class="thumbs thumbs_nocaptions">
    <?php 
    $thumb_photo = 'thumb.normal';
		if(defined('YNRESPONSIVE'))
		{
			$thumb_photo = 'thumb.profile';
		}
    foreach( $this->paginator as $photo ): ?>
      <li id='thumbs_nocaptions_<?php echo $photo->getIdentity()?>'>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </a>
        <p id="nocaptions_photo_<?php echo $photo->getIdentity()?>">
        	<?php if($this->viewer->isSelf($this->event->getOwner())) :?>
        	<a style="font-size: 10px" id="nocaptions_photo_slideshow_<?php echo $photo->getIdentity()?>" class="buttonlink <?php if($photo->is_featured) echo 'icon_event_delete'; else echo 'icon_event_slideshow';?>" onclick="return slideshow(<?php echo $photo->getIdentity()?>)" href="javascript:void();" ><?php if($photo->is_featured) echo $this->translate("Remove from Slideshow"); else echo $this->translate("Add to Slideshow");?></a>
        	<?php endif;?>
        	<?php if($this->viewer->isSelf($photo->getOwner()) || $this->viewer->isSelf($this->event->getOwner())) :?>
        	<a style="font-size: 10px"  id="nocaptions_photo_remofile_<?php echo $photo->getIdentity()?>" class="buttonlink icon_event_delete" onclick="return removeFile(<?php echo $photo->getIdentity()?>)" href="javascript:void();" ><?php echo $this->translate("Remove from Event")?></a>
       		<?php endif;?>
        </p>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator); ?>
  <?php endif; ?>
</div>

<script type="text/javascript">
	function slideshow(photo_id)
   	{
		request = new Request.JSON({
			'format' : 'json',
            'url' :  en4.core.baseUrl + 'events/photo/set-slideshow',
            'data': {
            	'photo_id' : photo_id,
            },
            'onSuccess' : function(responseJSON) {
            	if(responseJSON.status)
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
					obj.set('class','buttonlink icon_event_delete');
					obj.set('html',en4.core.language.translate('Remove from Slideshow'));
            	}
            	else
            	{
            		obj = $('nocaptions_photo_slideshow_'+responseJSON.photo_id);
            		obj.set('class','buttonlink icon_event_slideshow');
					obj.set('html',en4.core.language.translate('Add to Slideshow'));
            	}
            }
		});
        request.send();
        return false;
   	};
   	
   	function removeFile(photo_id)
   	{
   		var action = confirm(en4.core.language.translate('Are you sure you want to delete this photo?'));
   		
   		if(action)
   		{
   			request = new Request.JSON({
				'format' : 'json',
	            'url' :  en4.core.baseUrl + 'events/photo/delete-photo',
	            'data': {
	            	'photo_id' : photo_id,
	            },
	            'onSuccess' : function(responseJSON) {
	            	
	            }
			});
	        request.send();
	        
	        $('thumbs_nocaptions_'+photo_id).dispose();
   		}
		
		return false;
   	}  
	
</script>